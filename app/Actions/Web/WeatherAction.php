<?php

namespace App\Actions\Web;

use Inertia\Inertia;
use Illuminate\Routing\Router;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherAction
{
    use AsAction;

    public static function routes(Router $router)
    {
        $router->get('/weather', [self::class, 'show'])->name('weather.index');
        $router->post('/weather/search', [self::class, 'search'])->name('weather.search');
    }

    public function getControllerMiddleware(): array
    {
        return ['auth'];
    }

    public function show()
    {
        return Inertia::render('Weather');
    }

    public function search(Request $request)
    {
        $request->validate([
            'city' => 'required|string|max:255'
        ]);

        $city = $request->input('city');
        
        try {
            // Using OpenWeatherMap API (free tier)
            $apiKey = env('OPENWEATHER_API_KEY', 'demo_key');
            $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                'q' => $city,
                'appid' => $apiKey,
                'units' => 'metric'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'city' => $data['name'],
                        'country' => $data['sys']['country'],
                        'temperature' => round($data['main']['temp']),
                        'description' => ucfirst($data['weather'][0]['description']),
                        'humidity' => $data['main']['humidity'],
                        'wind_speed' => $data['wind']['speed'],
                        'feels_like' => round($data['main']['feels_like']),
                        'icon' => $data['weather'][0]['icon']
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'City not found'
                ], 404);
            }
        } catch (\Exception $e) {
            // Return mock data for demo purposes when API is not available
            return response()->json([
                'success' => true,
                'data' => [
                    'city' => $city,
                    'country' => 'Demo',
                    'temperature' => 25,
                    'description' => 'Partly cloudy',
                    'humidity' => 65,
                    'wind_speed' => 5.2,
                    'feels_like' => 27,
                    'icon' => '02d'
                ]
            ]);
        }
    }
}