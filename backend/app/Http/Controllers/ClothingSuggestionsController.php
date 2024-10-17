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
        $weather = $validatedData['weather'];

  
        $forecasts = $this->getTodayForecasts($weather['list']);


        if (empty($forecasts)) {
            return response()->json(['error' => 'No forecast data available for today.'], 400);
        }

        $temperatureRangeId = $this->getOverallTemperatureRangeId($forecasts);
        $temperatureRangeText = $this->getTemperatureRangeText($temperatureRangeId);
        $weatherConditions = $this->getOverallWeatherConditions($forecasts);

        $clothingQuery = Clothing::with(['photo', 'type', 'style', 'material'])
            ->where('temperature_range_id', $temperatureRangeId) 
            ->where('gender', $gender); 

        $clothingQuery = $this->modifyQueryBasedOnWeather($clothingQuery, $weatherConditions, $forecasts);

        $clothingItems = $clothingQuery->get();

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
            return null; 
        }

        $minTemp = min($temperatures);
        $maxTemp = max($temperatures); 

        if ($maxTemp >= 30) {
            return 1;
        } elseif ($maxTemp >= 20) {
            return 2;
        } elseif ($minTemp >= 10) {
            return 3; 
        } elseif ($minTemp >= 0) {
            return 4;
        } else {
            return 5;
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

        return ($maxTemp - $minTemp) >= 8; 
    }


    private function generateClothingSuggestionsText($clothingItems)
    {
        $suggestions = [];

        foreach ($clothingItems as $item) {
            $typeName = $item->type->type ?? null;
            $styleName = $item->style->style ?? null;
            $materialName = $item->material->material ?? null;
            $photoName = $item->photo->photo ?? null;

            if (!isset($suggestions[$typeName])) {
                $suggestions[$typeName] = [];
            }

            $suggestionItem = [
                'type' => $typeName,
                'style' => $styleName,
                'material' => $materialName,
                'photo' => $photoName
            ];

            $suggestions[$typeName][] = $suggestionItem;
        }

        if (empty($suggestions)) {
            return 'No clothing suggestions available for the current conditions.';
        }

        return ($suggestions);
    }

}
