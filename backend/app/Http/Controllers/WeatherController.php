<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function getWeather(Request $request)
    {
        $city = $request->input('city', 'Riga');
        $apiKey = env('OPENWEATHERMAP_API_KEY');

        $encodedCity = urlencode($city);
        $fiveDaysUrl =  "https://api.openweathermap.org/data/2.5/forecast?q={$encodedCity}&lang=en&units=metric&appid={$apiKey}";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $fiveDaysUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        if (empty($response)) {
            return response()->json(['error' => 'No data received from OpenWeatherMap'], 500);
        }

        $data = json_decode($response, true);

        if (isset($data['cod']) && $data['cod'] !== '200') {
            return response()->json(['error' => $data['message']], $data['cod']);
        }

        return response()->json($data);
    }
}
