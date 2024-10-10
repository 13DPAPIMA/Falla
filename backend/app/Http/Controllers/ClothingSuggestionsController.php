<?php

namespace App\Http\Controllers;

use App\Models\Clothing;
use Illuminate\Http\Request;

class ClothingSuggestionsController extends Controller
{
    public function getClothingSuggestions(Request $request)
    {
        // **Step 1: Validate input data**
        $validatedData = $request->validate([
            'weather.list' => 'required|array|min:1',
            'weather.list.*.main.temp' => 'required|numeric',
            'weather.list.*.weather' => 'required|array|min:1',
            'weather.list.*.dt_txt' => 'required|string',
            'gender' => 'nullable|string|in:male,female,neutral'
        ]);

        // **Step 2: Get weather and gender data**
        $gender = $validatedData['gender'] ?? 'neutral';
        $weather = $validatedData['weather'];

        // **Step 3: Get today's forecasts**
        $forecasts = $this->getTodayForecasts($weather['list']);

        if (empty($forecasts)) {
            return response()->json(['error' => 'No forecast data available for today.'], 400);
        }

        // **Step 4: Analyze weather conditions**
        $temperatureRangeId = $this->getOverallTemperatureRangeId($forecasts);
        $temperatureRangeText = $this->getTemperatureRangeText($temperatureRangeId);
        $weatherConditions = $this->getOverallWeatherConditions($forecasts);

        // **Step 5: Build the clothing query**
        $clothingQuery = Clothing::with(['photo', 'type', 'style', 'material']) // Eager load relationships
            ->where('temperature_range_id', $temperatureRangeId)
            ->where('gender', $gender);

        // **Step 6: Modify the query based on weather conditions**
        $clothingQuery = $this->modifyQueryBasedOnWeather($clothingQuery, $weatherConditions, $forecasts);

        // **Step 7: Get clothing suggestions**
        $clothingItems = $clothingQuery->get();

        // **Step 8: Prepare the response data**
        $clothingSuggestionsText = $this->generateClothingSuggestionsText($clothingItems);

        $responseData = [
            'temperature_range_id' => $temperatureRangeId,
            'temperature_range_text' => $temperatureRangeText,
            'weather_conditions' => $weatherConditions,
            'clothing_suggestions' => $clothingSuggestionsText
        ];

        if ($clothingItems->isEmpty()) {
            $responseData['message'] = 'No clothing suggestions available for the current conditions.';
        }

        return response()->json($responseData);
    }

    private function getTodayForecasts(array $forecastList)
    {
        $today = date('Y-m-d');
        $todayForecasts = [];

        foreach ($forecastList as $forecast) {
            if (isset($forecast['dt_txt']) && strpos($forecast['dt_txt'], $today) === 0) {
                $todayForecasts[] = $forecast;
            }
        }

        return $todayForecasts;
    }

    private function getOverallTemperatureRangeId(array $forecasts)
    {
        $temperatures = array_map(function ($forecast) {
            return $forecast['main']['temp'];
        }, $forecasts);

        if (empty($temperatures)) {
            return null; // Обработка случая отсутствия температур
        }

        $minTemp = min($temperatures);
        $maxTemp = max($temperatures);

        if ($maxTemp >= 30) {
            return 1; // Очень жарко
        } elseif ($maxTemp >= 20) {
            return 2; // Тепло
        } elseif ($minTemp >= 10) {
            return 3; // Умеренно
        } elseif ($minTemp >= 0) {
            return 4; // Прохладно
        } else {
            return 5; // Холодно
        }
    }

    private function getTemperatureRangeText($temperatureRangeId)
    {
        switch ($temperatureRangeId) {
            case 1:
                return 'Very hot';
            case 2:
                return 'Warm';
            case 3:
                return 'Decent';
            case 4:
                return 'Cold';
            case 5:
                return 'Very cold';
            default:
                return 'Unknown';
        }
    }

    private function getOverallWeatherConditions(array $forecasts)
    {
        $conditions = [];

        foreach ($forecasts as $forecast) {
            foreach ($forecast['weather'] as $weather) {
                $conditions[] = strtolower($weather['main']);
            }
        }

        return array_unique($conditions);
    }

    private function modifyQueryBasedOnWeather($query, array $weatherConditions, array $forecasts)
    {
        if (in_array('rain', $weatherConditions) || in_array('drizzle', $weatherConditions)) {
            $query->where('water_resistant', true);
        }

        if (in_array('snow', $weatherConditions)) {
            $query->where('water_resistant', true)
                  ->where('warmth_level', '>=', 4);
        }

        return $query;
    }

    private function hasSignificantTemperatureVariation(array $forecasts)
    {
        $temperatures = array_map(function ($forecast) {
            return $forecast['main']['temp'];
        }, $forecasts);

        if (empty($temperatures)) {
            return false;
        }

        $maxTemp = max($temperatures);
        $minTemp = min($temperatures);

        return ($maxTemp - $minTemp) >= 8; // Adjust threshold as needed
    }

    private function generateClothingSuggestionsText($clothingItems)
    {
        $suggestions = [];

        foreach ($clothingItems as $item) {
            $typeName = $item->type->name ?? null;
            $styleName = $item->style->name ?? null;
            $materialName = $item->material->name ?? null;

            // Skip if no names are available
            if (!$typeName && !$styleName && !$materialName) {
                continue;
            }

            $parts = array_filter([$typeName, $styleName, $materialName]);
            $itemName = implode(' ', $parts);

            $suggestions[] = $itemName;
        }

        if (empty($suggestions)) {
            return 'No clothing suggestions available for the current conditions.';
        }

        return implode(', ', $suggestions);
    }

}
