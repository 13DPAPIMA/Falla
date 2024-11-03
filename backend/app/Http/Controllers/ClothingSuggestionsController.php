<?php

namespace App\Http\Controllers;

use App\Models\Clothing;
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

        // Шаг 1: Рассчитать среднюю температуру
        $averageTemp = $this->calculateAverageTemperature($weatherList);

        // Шаг 2: Определить температурный диапазон
        $temperatureRangeId = $this->getTemperatureRangeId($averageTemp);
        $temperatureRangeText = $this->getTemperatureRangeText($temperatureRangeId);

        // Шаг 3: Выявить погодные условия
        $weatherConditions = $this->getWeatherConditions($weatherList);

        // Шаги 4 и 5: Выбрать подходящие элементы одежды
        $clothingItems = $this->getClothingItems($temperatureRangeId, $gender, $weatherConditions);

        // Шаг 6: Сгенерировать предложения по одежде
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

    // Добавьте этот метод в контроллер
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
