<?php

namespace App\Http\Controllers;

use App\Models\Clothing;
use Illuminate\Http\Request;

class ClothingSuggestionsController extends Controller
{
    public function getClothingSuggestions(Request $request)
    {
        // Проверяем, что данные о погоде существуют в запросе
        if (!isset($request->weather)) {
            return response()->json(['error' => 'Weather data is missing'], 400);
        }

        $weather = $request->weather; // Получаем данные о погоде из запроса

        // Проверка, что в данных о погоде есть список прогнозов (list)
        if (!isset($weather['list']) || !is_array($weather['list']) || empty($weather['list'])) {
            return response()->json(['error' => 'Invalid weather data format. Missing "list" key or no forecasts.'], 400);
        }

        // Берем первый прогноз из списка (это можно изменить в зависимости от логики)
        $currentForecast = $weather['list'][0];

        // Проверка, что ключ 'main' существует в прогнозе
        if (!isset($currentForecast['main']) || !isset($currentForecast['main']['temp'])) {
            return response()->json(['error' => 'Invalid weather data format. Missing "main" key in forecast.'], 400);
        }

        // Проверка, что ключ 'weather' существует в прогнозе
        if (!isset($currentForecast['weather'][0]['main'])) {
            return response()->json(['error' => 'Invalid weather data format. Missing "weather" key in forecast.'], 400);
        }

        // Логика для определения диапазона температур
        $temperature = $currentForecast['main']['temp']; // Температура
        $weatherCondition = $currentForecast['weather'][0]['main']; // Основное состояние погоды (дождь, солнце и т.д.)

        // Определяем диапазон температур
        $temperatureRangeId = $this->getTemperatureRangeId($temperature);

        // Логика определения типа погоды
        $gender = 'neutral'; // Здесь можно задать пол на основе предпочтений пользователя

        // Выполняем запрос на одежду, соответствующую погодным условиям
        $clothing = Clothing::with('photo')
            ->where('temperature_range_id', $temperatureRangeId)
            ->where('gender', $gender)
            ->when($weatherCondition === 'Rain', function ($query) {
                // Если идет дождь, выбираем одежду с водонепроницаемостью
                return $query->where('water_resistant', true);
            })
            ->get();

        return response()->json($clothing);
    }

    // Метод для определения температурного диапазона
    private function getTemperatureRangeId($temperature)
    {
        if ($temperature >= 25) {
            return 1; // Жаркая погода
        } elseif ($temperature >= 15) {
            return 2; // Теплая погода
        } elseif ($temperature >= 5) {
            return 3; // Прохладная погода
        } else {
            return 4; // Холодная погода
        }
    }
}