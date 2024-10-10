<?php

namespace App\Http\Controllers;

use App\Models\Clothing;
use Illuminate\Http\Request;

class ClothingSuggestionsController extends Controller
{
    public function getClothingSuggestions(Request $request)
    {
        
        // **Step 1: Validate the Request Data**
        $validatedData = $request->validate([
            'weather.list' => 'required|array|min:1',
            'weather.list.*.main.temp' => 'required|numeric',
            'weather.list.*.weather' => 'required|array|min:1',
            'weather.list.*.dt_txt' => 'required|string',
            'gender' => 'nullable|string|in:male,female,neutral'
        ]);
        
        // **Step 2: Retrieve Weather Data and Gender from Validated Data**
        $gender = $validatedData['gender'] ?? 'neutral';
        $weather = $validatedData['weather'];

        
        // **Step 3: Get Today's Forecasts**
        $forecasts = $this->getTodayForecasts($weather['list']);

        if (empty($forecasts)) {
            return response()->json(['error' => 'No forecast data available for today.'], 400);
        }

        // **Step 4: Analyze Weather Conditions**
        $temperatureRangeId = $this->getOverallTemperatureRangeId($forecasts);
        $weatherConditions = $this->getOverallWeatherConditions($forecasts);

        // **Step 5: Build the Clothing Query**
        $clothingQuery = Clothing::with('photo')
            ->where('temperature_range_id', $temperatureRangeId)
            ->where('gender', $gender);

        // **Step 6: Modify the Query Based on Weather Conditions**
        $clothingQuery = $this->modifyQueryBasedOnWeather($clothingQuery, $weatherConditions, $forecasts);

        // **Step 7: Get Clothing Suggestions**
        $clothing = $clothingQuery->get();

        if ($clothing->isEmpty()) {
            return response()->json(['message' => 'No clothing suggestions available for the current conditions.'], 200);
        }

        return response()->json($clothing);
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
            return null; // Handle this case appropriately
        }

        $minTemp = min($temperatures);
        $maxTemp = max($temperatures);

        if ($maxTemp >= 30) {
            return 1; // Very Hot
        } elseif ($maxTemp >= 20) {
            return 2; // Warm
        } elseif ($minTemp >= 10) {
            return 3; // Mild
        } elseif ($minTemp >= 0) {
            return 4; // Cool
        } else {
            return 5; // Cold
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

        if (in_array('thunderstorm', $weatherConditions)) {
            $query->where('protective', true);
        }

        if (in_array('mist', $weatherConditions) || in_array('fog', $weatherConditions)) {
            $query->where('visibility_friendly', true);
        }

        if ($this->hasSignificantTemperatureVariation($forecasts)) {
            $query->where('layerable', true);
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
}
