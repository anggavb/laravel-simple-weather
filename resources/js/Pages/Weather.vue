<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

const weatherData = ref(null);
const loading = ref(false);
const error = ref('');

const form = useForm({
    city: ''
});

const searchWeather = async () => {
    if (!form.city.trim()) {
        error.value = 'Please enter a city name';
        return;
    }

    loading.value = true;
    error.value = '';
    weatherData.value = null;

    try {
        const response = await axios.post('/weather/search', {
            city: form.city
        });

        if (response.data.success) {
            weatherData.value = response.data.data;
        } else {
            error.value = response.data.message || 'Failed to fetch weather data';
        }
    } catch (err) {
        if (err.response && err.response.status === 404) {
            error.value = 'City not found. Please check the spelling and try again.';
        } else {
            error.value = 'An error occurred while fetching weather data';
        }
    } finally {
        loading.value = false;
    }
};

const getWeatherIconUrl = (icon) => {
    return `https://openweathermap.org/img/w/${icon}.png`;
};

const handleSubmit = (e) => {
    e.preventDefault();
    searchWeather();
};
</script>

<template>
    <Head title="Weather" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Weather App
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <!-- Search Section -->
                <div class="mb-8 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Search Weather by City
                        </h3>
                        
                        <form @submit="handleSubmit" class="flex gap-4">
                            <div class="flex-1">
                                <input
                                    v-model="form.city"
                                    type="text"
                                    placeholder="Enter city name (e.g., Jakarta, London)"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :disabled="loading"
                                />
                            </div>
                            <button
                                type="submit"
                                :disabled="loading"
                                class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                            >
                                <span v-if="loading">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Searching...
                                </span>
                                <span v-else>Search</span>
                            </button>
                        </form>

                        <!-- Error Message -->
                        <div v-if="error" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-md">
                            <p class="text-sm text-red-700">{{ error }}</p>
                        </div>
                    </div>
                </div>

                <!-- Weather Result -->
                <div v-if="weatherData" class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Main Weather Info -->
                        <div class="text-center mb-6">
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                                {{ weatherData.city }}, {{ weatherData.country }}
                            </h2>
                            <div class="flex items-center justify-center mb-4">
                                <img 
                                    :src="getWeatherIconUrl(weatherData.icon)" 
                                    :alt="weatherData.description"
                                    class="w-20 h-20"
                                />
                                <div class="ml-4">
                                    <div class="text-5xl font-bold text-blue-600">
                                        {{ weatherData.temperature }}°C
                                    </div>
                                    <div class="text-lg text-gray-600 capitalize">
                                        {{ weatherData.description }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Weather Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Feels Like -->
                            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-6 text-center">
                                <div class="text-orange-600 mb-2">
                                    <svg class="w-8 h-8 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 2L13.09 8.26L20 9L14 14.74L15.18 21.02L10 18L4.82 21.02L6 14.74L0 9L6.91 8.26L10 2Z"/>
                                    </svg>
                                </div>
                                <div class="text-2xl font-bold text-gray-900">{{ weatherData.feels_like }}°C</div>
                                <div class="text-sm text-gray-600 font-medium">Feels Like</div>
                            </div>

                            <!-- Humidity -->
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 text-center">
                                <div class="text-blue-600 mb-2">
                                    <svg class="w-8 h-8 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="text-2xl font-bold text-gray-900">{{ weatherData.humidity }}%</div>
                                <div class="text-sm text-gray-600 font-medium">Humidity</div>
                            </div>

                            <!-- Wind Speed -->
                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 text-center">
                                <div class="text-green-600 mb-2">
                                    <svg class="w-8 h-8 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 12a1 1 0 001 1h4.586l-2.293 2.293a1 1 0 001.414 1.414l4-4a1 1 0 000-1.414l-4-4a1 1 0 10-1.414 1.414L9.586 11H5a1 1 0 00-1 1z"/>
                                    </svg>
                                </div>
                                <div class="text-2xl font-bold text-gray-900">{{ weatherData.wind_speed }} m/s</div>
                                <div class="text-sm text-gray-600 font-medium">Wind Speed</div>
                            </div>
                        </div>

                        <!-- Tips Section -->
                        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold text-gray-900 mb-2">Weather Tips:</h4>
                            <ul class="text-sm text-gray-700 space-y-1">
                                <li v-if="weatherData.temperature > 30">• It's quite hot! Stay hydrated and avoid prolonged sun exposure.</li>
                                <li v-else-if="weatherData.temperature < 10">• It's cold outside! Wear warm clothing when going out.</li>
                                <li v-else>• Perfect weather for outdoor activities!</li>
                                
                                <li v-if="weatherData.humidity > 70">• High humidity levels - you might feel sticky.</li>
                                <li v-if="weatherData.wind_speed > 10">• Strong winds - be careful with outdoor activities.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Sample Cities -->
                <div v-if="!weatherData" class="mt-8 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            Try these popular cities:
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="city in ['Jakarta', 'London', 'Tokyo', 'New York', 'Paris', 'Sydney']"
                                :key="city"
                                @click="form.city = city; searchWeather()"
                                class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded-full text-sm font-medium text-gray-700 transition-colors duration-150"
                            >
                                {{ city }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>