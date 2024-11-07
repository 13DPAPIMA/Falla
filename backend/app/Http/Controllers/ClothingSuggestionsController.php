<?php

namespace App\Http\Controllers;

use App\Models\Clothing;
use DateTime;
use Illuminate\Http\Request;

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

        $gender = $validatedData['gender'] ?? 'neutral';
        $weatherList = $validatedData['weather']['list'];

        $averageTemp = $this->calculateAverageTemperature($weatherList);

        $temperatureRangeId = $this->getTemperatureRangeId($averageTemp);
        $temperatureRangeText = $this->getTemperatureRangeText($temperatureRangeId);

        $weatherConditions = $this->getWeatherConditions($weatherList);

        $clothingItems = $this->getClothingItems($temperatureRangeId, $gender, $weatherConditions);

        $clothingSuggestions = $this->generateClothingSuggestions($clothingItems);

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
        $currentDate = (new DateTime())->format('Y-m-d');
        $todaysTemperatures = [];
    
        foreach ($weatherList as $forecast) {
            if (strpos($forecast['dt_txt'], $currentDate) === 0) {
                $hour = (int)substr($forecast['dt_txt'], 11, 2); // Извлекаем час
                if ($hour >= 6 && $hour <= 18) { // Только дневные часы
                    $todaysTemperatures[] = $forecast['main']['temp'];
                }
            }
        }
    
        return empty($todaysTemperatures) ? 0 : array_sum($todaysTemperatures) / count($todaysTemperatures);
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
            $condition = strtolower($weather['main']);
            if (!in_array($condition, $conditions)) {
                $conditions[] = $condition;
            }
        }
    }
    return $conditions;
}


    private function getClothingItems($temperatureRangeId, $gender, $weatherConditions)
    {
    $layers = ['base', 'mid', 'outer', 'pants']; 
    $clothingItems = [];

    foreach ($layers as $layer) {
        $query = Clothing::with(['photo', 'type', 'style', 'material'])
            ->where('temperature_range_id', $temperatureRangeId)
            ->where(function ($q) use ($gender) {
                $q->where('gender', $gender)->orWhere('gender', 'neutral');
            })
            ->where('layer', $layer);

        if ($temperatureRangeId <= 2 && $layer === 'base') {
            $query->whereNotIn('type_id', [1, 6, 10]); 
        }

        if ($layer === 'outer' && (in_array('rain', $weatherConditions) || in_array('drizzle', $weatherConditions))) {
            $query->where('water_resistant', true);
        }

        $clothingItems[$layer] = $query->get();
    }

        return $clothingItems;
    }

    private function generateClothingSuggestions($clothingItems)
    {

        $suggestions = [];

        foreach ($clothingItems as $layer => $items) {
            if (empty($items)) {
                continue;
            }

            $layerSuggestions = [];

            foreach ($items as $item) {
                $layerSuggestions[] = [
                    'type' => $item->type->type ?? null,
                    'style' => $item->style->style ?? null,
                    'material' => $item->material->material ?? null,
                    'color' => $item->color ?? null,
                    'water_resistant' => $item->water_resistant ?? null,
                    'photo' => $item->photo->photo_url ?? null,
                ];
            }

            $suggestions[$layer] = $layerSuggestions;
        }

        return $suggestions;
    }



}
