async function getWeather() {
    const apiKey = "cf8b56bcf8ecb228cc4abae0346663be";
    const city = "La Garde";
    const url = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric&lang=fr`;

    try {
        const response = await fetch(url);
        const data = await response.json();
        displayWeather(data);
    } catch (error) {
        console.error("Erreur lors de la récupération des données météo:", error);
    }
}

function displayWeather(data) {
    const mTemp = document.getElementById('meteo-temperature');
    mTemp.innerHTML = data.main.temp;

    const mHum = document.getElementById('meteo-humidity');
    mHum.innerHTML = data.main.humidity;

    const mCity = document.getElementById('meteo-city');
    mCity.innerHTML = data.name;

    const mDesc = document.getElementById('meteo-description');
    mDesc.innerHTML = data.weather[0].description;
}

// Appel de la fonction pour récupérer les données
getWeather();