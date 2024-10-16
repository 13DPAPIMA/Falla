<?php

namespace App\Http\Controllers;

use App\Models\Clothing;
use Illuminate\Http\Request;

class ClothingSuggestionsController extends Controller
{
    public function getClothingSuggestions(Request $request)
    {
        // Валидация входящих данных
        $validatedData = $request->validate([
            'weather.list' => 'required|array|min:1', // Обязательный массив прогнозов погоды
            'weather.list.*.main.temp' => 'required|numeric', // Обязательная температура для каждого прогноза
            'weather.list.*.weather' => 'required|array|min:1', // Обязательный массив погодных условий для каждого прогноза
            'weather.list.*.dt_txt' => 'required|string', // Обязательная дата и время прогноза
            'gender' => 'nullable|string|in:male,female,neutral' // Необязательный гендер: male, female или neutral
        ]);

        // Получение значения гендера или установка значения по умолчанию 'neutral'
        $gender = $validatedData['gender'] ?? 'neutral';
        $weather = $validatedData['weather'];

        // Получение прогнозов на сегодняшний день
        $forecasts = $this->getTodayForecasts($weather['list']);

        // Проверка наличия прогнозов на сегодня
        if (empty($forecasts)) {
            return response()->json(['error' => 'No forecast data available for today.'], 400);
        }

        // Определение диапазона температур на основе прогнозов
        $temperatureRangeId = $this->getOverallTemperatureRangeId($forecasts);
        // Получение текстового описания диапазона температур
        $temperatureRangeText = $this->getTemperatureRangeText($temperatureRangeId);
        // Получение общих погодных условий на основе прогнозов
        $weatherConditions = $this->getOverallWeatherConditions($forecasts);

        // Запрос к модели Clothing с предварительной загрузкой связанных моделей
        $clothingQuery = Clothing::with(['photo', 'type', 'style', 'material'])
            ->where('temperature_range_id', $temperatureRangeId) // Фильтрация по диапазону температур
            ->where('gender', $gender); // Фильтрация по гендеру

        // Модификация запроса на основе погодных условий
        $clothingQuery = $this->modifyQueryBasedOnWeather($clothingQuery, $weatherConditions, $forecasts);

        // Получение списка одежды из базы данных
        $clothingItems = $clothingQuery->get();

        // Генерация текста с предложениями по одежде
        $clothingSuggestionsText = $this->generateClothingSuggestionsText($clothingItems);

        // Формирование данных для ответа
        $responseData = [
            'temperature_range_id' => $temperatureRangeId,
            'temperature_range_text' => $temperatureRangeText,
            'weather_conditions' => $weatherConditions,
            'clothing_suggestions' => $clothingSuggestionsText
        ];

        // Добавление сообщения, если нет доступных предложений по одежде
        if ($clothingItems->isEmpty()) {
            $responseData['message'] = 'No clothing suggestions available for the current conditions.';
        }

        // Возврат ответа в формате JSON
        return response()->json($responseData);
    }

    private function getTodayForecasts(array $forecastList)
    {
        $today = date('Y-m-d'); // Текущая дата в формате ГГГГ-ММ-ДД
        $todayForecasts = [];

        foreach ($forecastList as $forecast) {
            if (isset($forecast['dt_txt']) && strpos($forecast['dt_txt'], $today) === 0) {
                $todayForecasts[] = $forecast; // Добавление прогноза, если дата совпадает с сегодняшней
            }
        }

        return $todayForecasts;
    }

    private function getOverallTemperatureRangeId(array $forecasts)
    {
        // Извлечение всех температур из прогнозов
        $temperatures = array_map(function ($forecast) {
            return $forecast['main']['temp'];
        }, $forecasts);

        if (empty($temperatures)) {
            return null; // Возврат null, если нет температурных данных
        }

        $minTemp = min($temperatures); // Минимальная температура
        $maxTemp = max($temperatures); // Максимальная температура

        // Определение диапазона температур по заданным критериям
        if ($maxTemp >= 30) {
            return 1; // Очень жарко
        } elseif ($maxTemp >= 20) {
            return 2; // Тепло
        } elseif ($minTemp >= 10) {
            return 3; // Комфортно
        } elseif ($minTemp >= 0) {
            return 4; // Холодно
        } else {
            return 5; // Очень холодно
        }
    }


    private function getTemperatureRangeText($temperatureRangeId)
    {
        switch ($temperatureRangeId) {
            case 1:
                return 'Very hot'; // Очень жарко
            case 2:
                return 'Warm'; // Тепло
            case 3:
                return 'Decent'; // Комфортно
            case 4:
                return 'Cold'; // Холодно
            case 5:
                return 'Very cold'; // Очень холодно
            default:
                return 'Unknown'; // Неизвестно
        }
    }


    private function getOverallWeatherConditions(array $forecasts)
    {
        $conditions = [];

        foreach ($forecasts as $forecast) {
            foreach ($forecast['weather'] as $weather) {
                $conditions[] = strtolower($weather['main']); // Добавление погодных условий в нижнем регистре
            }
        }

        return array_unique($conditions); // Удаление дубликатов
    }


    private function modifyQueryBasedOnWeather($query, array $weatherConditions, array $forecasts)
    {
        // Если ожидается дождь или морось, фильтруем одежду по водоотталкивающим свойствам
        if (in_array('rain', $weatherConditions) || in_array('drizzle', $weatherConditions)) {
            $query->where('water_resistant', true);
        }

        // Если ожидается снег, фильтруем одежду по водоотталкивающим свойствам и уровню тепла
        if (in_array('snow', $weatherConditions)) {
            $query->where('water_resistant', true)
                  ->where('warmth_level', '>=', 4); // Требуется высокий уровень тепла
        }

        return $query;
    }


    private function hasSignificantTemperatureVariation(array $forecasts)
    {
        // Извлечение всех температур из прогнозов
        $temperatures = array_map(function ($forecast) {
            return $forecast['main']['temp'];
        }, $forecasts);

        if (empty($temperatures)) {
            return false; // Возврат false, если нет температурных данных
        }

        $maxTemp = max($temperatures); // Максимальная температура
        $minTemp = min($temperatures); // Минимальная температура

        return ($maxTemp - $minTemp) >= 8; // Проверка на значительную разницу температур
    }

    /**
     * Генерация текста с предложениями по одежде на основе полученных элементов одежды.
     *
     * @param \Illuminate\Support\Collection $clothingItems Коллекция объектов одежды.
     * @return string|array Текстовые предложения или сообщение об отсутствии предложений.
     */
    private function generateClothingSuggestionsText($clothingItems)
    {
        $suggestions = [];

        foreach ($clothingItems as $item) {
            // Получение связанных данных из моделей
            $typeName = $item->type->type ?? null;
            $styleName = $item->style->style ?? null;
            $materialName = $item->material->material ?? null;
            $photoName = $item->photo->photo ?? null;

            // Инициализация массива для типа одежды, если еще не существует
            if (!isset($suggestions[$typeName])) {
                $suggestions[$typeName] = [];
            }

            // Формирование элемента предложения по одежде
            $suggestionItem = [
                'type' => $typeName,
                'style' => $styleName,
                'material' => $materialName,
                'photo' => $photoName
            ];

            // Добавление элемента в соответствующий тип одежды
            $suggestions[$typeName][] = $suggestionItem;
        }

        // Если нет предложений, возвращаем сообщение об отсутствии
        if (empty($suggestions)) {
            return 'No clothing suggestions available for the current conditions.';
        }

        // Возвращаем массив предложений по одежде
        return ($suggestions);
    }

}
