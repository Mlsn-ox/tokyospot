import { escapeHtml } from "./functions.js";
const openWeather = document.getElementById("meteo-tokyo");
const horaire = document.getElementById("heure-tokyo");
const toast = document.querySelector(".toast");
const acceptBtn = document.getElementById("accept-cookies");
const apiKeyOpenW = "e1edbd55f7fda615ba1c2906bf6454e9";
let petalInterval;

updateTime();
setInterval(updateTime, 1000);

window.addEventListener("resize", handlePetalEffect);
handlePetalEffect();

if (document.cookie.includes("cookies_accepted=true")) {
  const toastBootstrap = new bootstrap.Toast(toast, {
    autohide: false,
  });
  toastBootstrap.show();
}
acceptBtn.addEventListener("click", () => {
  document.cookie = "cookies_accepted=true; path=/; max-age=" + 60 * 60; // 1 heure
  toast.classList.add("fade");
  setTimeout(() => {
    toast.classList.add("hide");
  }, 500);
});

/**
 * Crée une pétale animée qui tombe depuis une position aléatoire du haut de l’écran.
 */
function createPetal() {
  const petal = document.createElement("div");
  petal.classList.add("petal");
  let startPosition = Math.random() * (window.innerWidth - 30);
  let duration = Math.random() * 5 + 5;
  let size = Math.random() * 10 + 10;
  petal.style.left = `${startPosition}px`;
  petal.style.animationDuration = `${duration}s`;
  petal.style.width = `${size}px`;
  petal.style.height = `${size}px`;
  document.body.appendChild(petal);
  setTimeout(() => {
    petal.remove();
  }, duration * 1000);
}

/**
 * Active ou désactive l’effet de pétales en fonction de la largeur de l’écran.
 */
function handlePetalEffect() {
  if (window.innerWidth > 992) {
    if (!petalInterval) {
      petalInterval = setInterval(createPetal, 1000); // Créer une pétale toutes les secondes
    }
  } else {
    if (petalInterval) {
      clearInterval(petalInterval);
      petalInterval = null;
    }
  }
}

/**
 * Met à jour dynamiquement l’heure actuelle à Tokyo, au format `HH:MM:SS`,
 * Fonction réactualisée toutes les secondes.
 */
function updateTime() {
  const options = {
    timeZone: "Asia/Tokyo",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
  };
  const time = new Date().toLocaleTimeString("fr-FR", options);
  horaire.textContent = time;
}

/**
 * Récupère les données météorologiques actuelles de Tokyo via l'API OpenWeatherMap.
 * - Affiche la température arrondie en °C
 * - Affiche la description météo (capitalisée)
 * - Affiche une icône correspondante
 */
const urlOpenW = `https://api.openweathermap.org/data/2.5/weather?q=Tokyo&appid=${apiKeyOpenW}&units=metric&lang=fr`;
fetch(urlOpenW)
  .then((response) => response.json())
  .then((data) => {
    const temperature = escapeHtml(Math.round(data.main.temp));
    let meteo = escapeHtml(data.weather[0].description);
    let meteoCapital = escapeHtml(
      meteo.charAt(0).toUpperCase() + meteo.slice(1)
    );
    const iconCode = escapeHtml(data.weather[0].icon);
    let iconUrl = "";
    if (iconCode === "01n") {
      iconUrl = `../assets/logo_category/clear-night.png`;
    } else if (iconCode === "02n") {
      iconUrl = `../assets/logo_category/partly-cloudy-night.png`;
    } else {
      iconUrl = `https://openweathermap.org/img/wn/${iconCode}@2x.png`;
    }
    openWeather.innerHTML = `<p>🌡️ ${temperature}°c &nbsp;</p><p> - &nbsp; ${meteoCapital} &nbsp;</p>
      <img src="${iconUrl}" alt="${meteo}" class="icon-meteo">`;
  })
  .catch((error) => console.error("Erreur :", error));
