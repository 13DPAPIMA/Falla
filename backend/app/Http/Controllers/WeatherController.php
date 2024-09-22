<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function getWeather(Request $request)
    {
        $city = $request->input('city', null);
        $lat = $request->input('lat', null);
        $lon = $request->input('lon', null);
        $apiKey = env('OPENWEATHERMAP_API_KEY');
    
        if ($lat && $lon) {
            $googleApiUrl = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&lang=en&units=metric&APPID={$apiKey}";
        } else if ($city) {
            $googleApiUrl = "https://api.openweathermap.org/data/2.5/weather?q={$city}&lang=en&units=metric&APPID={$apiKey}";
        } else {
            return response()->json(['error' => 'No location or city provided'], 400);
        }
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
        $response = curl_exec($ch);
        curl_close($ch);
    
        if (empty($response)) {
            return response()->json(['error' => 'No data received from OpenWeatherMap'], 500);
        }
    
        $data = json_decode($response, true);
    
        if (isset($data['cod']) && $data['cod'] !== 200) {
            return response()->json(['error' => $data['message']], $data['cod']);
        }
    
        return response()->json($data);
    }
    
}
