<?php

namespace App\Http\Controllers;

use App\Services\WardrobeService; 
use App\Models\Clothing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClothingSuggestionsController extends Controller
{
    public function getClothingSuggestions(Request $request)
{
    $validatedData = $request->validate([
        'weather.list' => 'required|array|min:1',
        'weather.list.*.main.temp' => 'required|numeric',
        'weather.list.*.weather' => 'required|array|min:1',
        'weather.list.*.dt_txt' => 'required|string',
        'gender' => 'nullable|string|in:male,female,neutral'
    ]);

    $user = $request->user();
    if ($user) {
        $userId = $user->id;
        $gender = $validatedData['gender'] ?? $user->gender ?? 'neutral';
        Log::info("Authenticated User ID: " . $userId);
    } else {
        Log::info("No authenticated user.");
        return response()->json(['message' => 'User not authenticated'], 401);
    }

    $weatherList = $validatedData['weather']['list'];
    $averageTemp = $this->calculateAverageTemperature($weatherList);
    $temperatureRangeId = $this->getTemperatureRangeId($averageTemp);
    $temperatureRangeText = $this->getTemperatureRangeText($temperatureRangeId);
    $weatherConditions = $this->getWeatherConditions($weatherList);

    $wardrobeService = app(WardrobeService::class);
    $wardrobe = $wardrobeService->getUserWardrobe($userId);

    if ($wardrobe) {
        $wardrobeClothingIds = $wardrobe->wardrobeItems()->pluck('clothing_id');
        $wardrobeClothings = Clothing::with(['type', 'style', 'material'])
            ->whereIn('id', $wardrobeClothingIds)
            ->get();

        Log::info("User's wardrobe contains " . $wardrobeClothings->count() . " items.");

        foreach ($wardrobeClothings as $clothing) {
            Log::info("Wardrobe Item - ID: " . $clothing->id .
                ", Type: " . ($clothing->type->type ?? 'Unknown') .
                ", Layer: " . $clothing->layer .
                ", Temperature Range ID: " . $clothing->temperature_range_id .
                ", Gender: " . $clothing->gender);
        }
    } else {
        Log::info("No wardrobe found for user ID: " . $userId);
    }

    $result = $this->getClothingItems($temperatureRangeId, $gender, $weatherConditions, $userId);
    $clothingItems = $result['clothingItems'];
    $wardrobeClothingIds = $result['wardrobeClothingIds'];

    $clothingSuggestions = $this->generateClothingSuggestions($clothingItems, $wardrobeClothingIds);

    $responseData = [
        'temperature_range_id' => $temperatureRangeId,
        'temperature_range_text' => $temperatureRangeText,
        'weather_conditions' => $weatherConditions,
        'clothing_suggestions' => $clothingSuggestions,
    ];

    if ($this->allLayersAreEmpty($clothingSuggestions)) {
        $responseData['message'] = 'No clothing suggestions available for the current conditions.';
    } else {
        $responseData['message'] = 'Here are some clothing suggestions for the current conditions.';
    }

    return response()->json($responseData);
}



    private function allLayersAreEmpty($clothingSuggestions)
    {
        foreach ($clothingSuggestions as $layer => $items) {
            if (!empty($items)) {
                return false;
            }
        }
        return true;
    }

    private function calculateAverageTemperature(array $weatherList)
    {
        $temperatures = array_column(array_column($weatherList, 'main'), 'temp');
        return array_sum($temperatures) / count($temperatures);
    }

    private function getTemperatureRangeId($averageTemp)
    {
        if ($averageTemp < 0) {
            return 1; // Below 0°C
        } elseif ($averageTemp <= 10) {
            return 2; // 0°C - 10°C
        } elseif ($averageTemp <= 20) {
            return 3; // 11°C - 20°C
        } elseif ($averageTemp <= 30) {
            return 4; // 21°C - 30°C
        } else {
            return 5; // Above 30°C
        }
    }

    private function getTemperatureRangeText($temperatureRangeId)
    {
        $ranges = [
            1 => 'Below 0°C',
            2 => '0°C - 10°C',
            3 => '11°C - 20°C',
            4 => '21°C - 30°C',
            5 => 'Above 30°C',
        ];

        return $ranges[$temperatureRangeId] ?? 'Unknown';
    }

    private function getWeatherConditions(array $weatherList)
    {
        $conditions = [];
        foreach ($weatherList as $forecast) {
            foreach ($forecast['weather'] as $weather) {
                $conditions[] = strtolower($weather['main']);
            }
        }
        return array_unique($conditions);
    }

    private function getClothingItems($temperatureRangeId, $gender, $weatherConditions, $userId)
{
    $layers = ['base', 'mid', 'outer', 'pants'];
    $clothingItems = [];
    $wardrobeClothingIds = collect();

    $wardrobeService = app(WardrobeService::class);
    $wardrobe = $wardrobeService->getUserWardrobe($userId);

    if ($wardrobe) {
        $wardrobeClothingIds = $wardrobe->wardrobeItems()->pluck('clothing_id');
        Log::info("Wardrobe Clothing IDs: " . $wardrobeClothingIds->implode(','));
    } else {
        Log::info("No wardrobe found for user ID: " . $userId);
    }

    foreach ($layers as $layer) {
        $baseQuery = Clothing::with(['photo', 'type', 'style', 'material'])
            // ->where('temperature_range_id', $temperatureRangeId) // Закомментировано для тестирования
            ->where(function ($q) use ($gender) {
                $q->where('gender', $gender)->orWhere('gender', 'neutral');
            })
            ->where('layer', $layer);

        // Остальные условия...

        if ($wardrobeClothingIds->isNotEmpty()) {
            $wardrobeItems = (clone $baseQuery)
                ->whereIn('id', $wardrobeClothingIds)
                ->get();

            Log::info("For layer '$layer', found " . $wardrobeItems->count() . " items in wardrobe matching the filters.");

            if ($wardrobeItems->isNotEmpty()) {
                $clothingItems[$layer] = $wardrobeItems;
                continue;
            } else {
                Log::info("No wardrobe items match the filters for layer '$layer'.");
            }
        }

        $generalItems = $baseQuery->get();
        Log::info("For layer '$layer', found " . $generalItems->count() . " general items matching the filters.");

        $clothingItems[$layer] = $generalItems;
    }

    return [
        'clothingItems' => $clothingItems,
        'wardrobeClothingIds' => $wardrobeClothingIds
    ];
}


    

private function generateClothingSuggestions($clothingItems, $wardrobeClothingIds)
{
    $suggestions = [];

    foreach ($clothingItems as $layer => $items) {
        if ($items->isEmpty()) {
            continue;
        }

        $layerSuggestions = [];

        foreach ($items as $item) {
            $isFromWardrobe = $wardrobeClothingIds->contains(function ($value) use ($item) {
                return intval($value) === intval($item->id);
            });

            $layerSuggestions[] = [
                'type' => $item->type->type ?? null,
                'style' => $item->style->style ?? null,
                'material' => $item->material->material ?? null,
                'color' => $item->color ?? null,
                'water_resistant' => $item->water_resistant ?? null,
                'photo' => $item->photo->url ?? null,
                'from_wardrobe' => $isFromWardrobe,
            ];
        }

        $suggestions[$layer] = $layerSuggestions;
    }

    return $suggestions;
}


    

}
