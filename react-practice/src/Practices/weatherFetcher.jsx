import { useState, useEffect } from "react";

function WeatherFetcher() {
  const [city, setCity] = useState("Karachi");
  const [weather, setWeather] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [coordinates, setCoordinates] = useState({ lat: 24.8607, lon: 67.0011 }); // Karachi coordinates

  // First, get coordinates from city name
  useEffect(() => {
    async function getCoordinates() {
      if (!city.trim()) return;
      
      setLoading(true);
      setError(null);
      
      try {
        // Use OpenStreetMap Nominatim for geocoding (free, no API key)
        const geoRes = await fetch(
          `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(city)}&limit=1`
        );
        const geoData = await geoRes.json();
        
        if (geoData.length === 0) {
          throw new Error(`City "${city}" not found`);
        }
        
        const { lat, lon } = geoData[0];
        setCoordinates({ lat: parseFloat(lat), lon: parseFloat(lon) });
        
      } catch (err) {
        setError(err.message);
        setWeather(null);
        setLoading(false);
      }
    }

    const timeoutId = setTimeout(getCoordinates, 800);
    return () => clearTimeout(timeoutId);
  }, [city]);

  // Then, get weather data using coordinates
  useEffect(() => {
    async function fetchWeather() {
      if (!coordinates.lat || !coordinates.lon) return;
      
      try {
        // Use Open-Meteo API (free, no API key required)
        const weatherRes = await fetch(
          `https://api.open-meteo.com/v1/forecast?latitude=${coordinates.lat}&longitude=${coordinates.lon}&current=temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m&timezone=auto`
        );
        
        const weatherData = await weatherRes.json();
        
        if (weatherData.error) {
          throw new Error(weatherData.reason || "Weather data not available");
        }
        
        setWeather({
          city: city,
          temperature: weatherData.current.temperature_2m,
          apparentTemperature: weatherData.current.apparent_temperature,
          humidity: weatherData.current.relative_humidity_2m,
          windSpeed: weatherData.current.wind_speed_10m,
          weatherCode: weatherData.current.weather_code
        });
        
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    }

    if (coordinates.lat && coordinates.lon) {
      fetchWeather();
    }
  }, [coordinates, city]);

  // Convert weather code to description
  const getWeatherDescription = (code) => {
    const weatherCodes = {
      0: "Clear sky",
      1: "Mainly clear",
      2: "Partly cloudy",
      3: "Overcast",
      45: "Foggy",
      48: "Depositing rime fog",
      51: "Light drizzle",
      53: "Moderate drizzle",
      55: "Dense drizzle",
      61: "Slight rain",
      63: "Moderate rain",
      65: "Heavy rain",
      80: "Rain showers",
      81: "Moderate rain showers",
      82: "Violent rain showers",
      95: "Thunderstorm"
    };
    return weatherCodes[code] || "Unknown";
  };

  return (
    <div className="p-6 max-w-md mx-auto bg-white rounded-xl shadow-lg">
      <h2 className="text-2xl font-bold text-center mb-6 text-gray-800">🌤 Weather App</h2>
      
      <div className="mb-4">
        <input
          className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-800"
          type="text"
          value={city}
          onChange={(e) => setCity(e.target.value)}
          placeholder="Enter city name"
        />
      </div>
      
      {loading && (
        <div className="text-center py-4">
          <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
          <p className="mt-2 text-gray-600">Loading weather data...</p>
        </div>
      )}
      
      {error && (
        <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
          <p>Error: {error}</p>
          <p className="text-sm mt-1">Try: "London", "New York", "Tokyo", "Paris"</p>
        </div>
      )}
      
      {weather && !loading && !error && (
        <div className="bg-blue-50 border border-blue-200 rounded-lg p-6 text-gray-800">
          <h3 className="text-xl font-bold mb-2 text-center">{weather.city}</h3>
          <div className="text-center mb-4">
            <span className="text-4xl font-bold">{weather.temperature}°C</span>
            <p className="text-sm text-gray-600">Feels like {weather.apparentTemperature}°C</p>
          </div>
          
          <div className="grid grid-cols-2 gap-4 text-sm">
            <div className="text-center">
              <p className="font-semibold">Condition</p>
              <p>{getWeatherDescription(weather.weatherCode)}</p>
            </div>
            <div className="text-center">
              <p className="font-semibold">Humidity</p>
              <p>{weather.humidity}%</p>
            </div>
            <div className="text-center">
              <p className="font-semibold">Wind Speed</p>
              <p>{weather.windSpeed} km/h</p>
            </div>
            <div className="text-center">
              <p className="font-semibold">Coordinates</p>
              <p className="text-xs">{coordinates.lat.toFixed(4)}, {coordinates.lon.toFixed(4)}</p>
            </div>
          </div>
        </div>
      )}
      
      <div className="mt-4 text-xs text-gray-500 text-center">
        <p>Powered by Open-Meteo & OpenStreetMap</p>
      </div>
    </div>
  );
}

export default WeatherFetcher;