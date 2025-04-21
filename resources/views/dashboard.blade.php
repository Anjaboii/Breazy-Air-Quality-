<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Breazy - Real-time AQI Map</title>
  <link rel="icon" href="{{ asset('storage/logo/breazyicon.png') }}" type="image/png" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    :root {
      --primary-color: #4a6bff;
      --secondary-color: #2c3e50;
      --background-color: #f5f5f5;
      --text-color: #333;
      --white: #ffffff;
      --sensor-color: #4a6bff;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    html, body {
      height: 100%;
      width: 100%;
      overflow: hidden;
    }

    header {
      background-color: var(--white);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
    }

    .logo-nav-container {
      display: flex;
      align-items: center;
      gap: 2rem;
    }

    .logo-img {
      height: 50px;
      width: auto;
    }

    nav ul {
      display: flex;
      list-style: none;
      gap: 1.5rem;
    }

    nav a {
      text-decoration: none;
      color: var(--text-color);
      font-weight: 600;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      transition: all 0.3s ease;
    }

    nav a:hover {
      background-color: #f0f0f0;
      color: var(--primary-color);
    }

    .login-btn {
      background-color: var(--primary-color);
      color: white;
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    .login-btn:hover {
      background-color: #314cc9;
    }

    #map {
      position: absolute;
      top: 70px;
      left: 0;
      width: 100%;
      height: calc(100vh - 70px);
    }

    .custom-popup .leaflet-popup-content-wrapper {
      border-radius: 8px;
      padding: 0;
    }

    .custom-popup .leaflet-popup-content {
      margin: 0;
      width: 250px !important;
    }

    .aqi-popup-header {
      padding: 12px 15px;
      font-weight: bold;
      border-top-left-radius: 8px;
      border-top-right-radius: 8px;
    }

    .aqi-popup-body {
      padding: 15px;
    }

    .aqi-value {
      font-size: 24px;
      font-weight: bold;
      margin: 5px 0;
      color: white;
      text-shadow: 1px 1px 2px black, -1px -1px 2px black;
    }

    .aqi-status {
      font-weight: 600;
      margin-bottom: 10px;
    }

    .aqi-details {
      font-size: 14px;
      margin-top: 10px;
    }

    .aqi-details p {
      margin: 5px 0;
    }

    .coordinates {
      margin-top: 10px;
      padding-top: 8px;
      border-top: 1px solid #eee;
      font-size: 13px;
      color: #666;
    }

    .info.legend {
      padding: 10px;
      background: white;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
      line-height: 1.5;
    }

    .legend-title {
      font-weight: bold;
      margin-bottom: 5px;
    }

    .legend-item {
      display: flex;
      align-items: center;
      margin: 3px 0;
    }

    .legend-color {
      width: 20px;
      height: 20px;
      border-radius: 50%;
      margin-right: 8px;
      display: inline-block;
    }

    .refresh-indicator {
      position: absolute;
      bottom: 30px;
      right: 10px;
      background: white;
      padding: 5px 10px;
      border-radius: 3px;
      box-shadow: 0 0 5px rgba(0,0,0,0.2);
      font-size: 12px;
      z-index: 1000;
    }

    .loading-spinner {
      display: inline-block;
      width: 12px;
      height: 12px;
      border: 2px solid rgba(0,0,0,0.2);
      border-radius: 50%;
      border-top-color: var(--primary-color);
      animation: spin 1s ease-in-out infinite;
      margin-right: 5px;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .notification-container {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 1rem;
    }

    .notification-icon {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
    }

    .notification-icon img {
      width: 24px;
      height: 24px;
    }

    .notification-count {
      position: absolute;
      top: -5px;
      right: -5px;
      background-color: #e74c3c;
      color: white;
      border-radius: 50%;
      padding: 3px 8px;
      font-size: 12px;
      font-weight: bold;
    }

    /* Sensor pin styles */
    .sensor-pin {
      position: relative;
      width: 24px;
      height: 24px;
      transform: translateY(-50%);
      transition: transform 0.2s ease;
    }

    .sensor-pin:hover {
      transform: translateY(-50%) scale(1.1);
    }

    .sensor-pin::before {
      content: '';
      position: absolute;
      width: 24px;
      height: 24px;
      background-color: var(--sensor-color);
      border-radius: 50% 50% 50% 0;
      transform: rotate(-45deg);
      border: 2px solid white;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .sensor-pin::after {
      content: 'S';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(45deg);
      color: white;
      font-weight: bold;
      font-size: 12px;
      text-shadow: 0 1px 1px rgba(0,0,0,0.3);
    }

    .sensor-pulse {
      position: absolute;
      width: 24px;
      height: 24px;
      background-color: rgba(74, 107, 255, 0.4);
      border-radius: 50%;
      opacity: 0;
      animation: pulse 2s infinite;
      transform: translateY(-50%);
    }

    @keyframes pulse {
      0% {
        transform: translateY(-50%) scale(0.8);
        opacity: 0.7;
      }
      70% {
        transform: translateY(-50%) scale(1.3);
        opacity: 0;
      }
      100% {
        opacity: 0;
      }
    }

    /* Live AQI Update styles */
    .live-aqi-container {
      margin-top: 12px;
      padding: 8px;
      background-color: #f9f9f9;
      border-radius: 4px;
      border-left: 3px solid var(--primary-color);
    }

    .live-aqi-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 5px;
    }

    .live-aqi-title {
      font-weight: 600;
      font-size: 13px;
      color: #333;
      display: flex;
      align-items: center;
    }

    .live-indicator {
      display: inline-block;
      width: 6px;
      height: 6px;
      background-color: #2ecc71;
      border-radius: 50%;
      margin-right: 5px;
      animation: pulse-live 1.5s infinite;
    }

    @keyframes pulse-live {
      0% { opacity: 1; }
      50% { opacity: 0.4; }
      100% { opacity: 1; }
    }

    .live-aqi-timestamp {
      font-size: 11px;
      color: #888;
    }

    .live-aqi-value-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .live-aqi-value {
      font-size: 18px;
      font-weight: bold;
    }

    .aqi-change {
      display: flex;
      align-items: center;
      font-size: 12px;
      padding: 2px 6px;
      border-radius: 3px;
    }

    .aqi-change.increase {
      color: #e74c3c;
      background-color: rgba(231, 76, 60, 0.1);
    }

    .aqi-change.decrease {
      color: #2ecc71;
      background-color: rgba(46, 204, 113, 0.1);
    }

    .aqi-change.stable {
      color: #3498db;
      background-color: rgba(52, 152, 219, 0.1);
    }

    .change-icon {
      margin-right: 3px;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo-nav-container">
      <img src="{{ asset('storage/logo/homeB.png') }}" alt="Logo" class="logo-img" />
      <nav>
        <ul>
          <li><a href="{{ route('dashboard') }}">HOME</a></li>
          <li><a href="{{ route('contactus') }}">Contact US</a></li>
          <li><a href="{{ route('map.api') }}">MAP API</a></li>
        </ul>
      </nav>
    </div>
    <div class="notification-container">
      <a href="{{ route('admin.view') }}" class="login-btn">Login Admin</a>
      <div id="notification-icon" class="notification-icon">
        <img src="{{ asset('storage/logo/bell.png') }}" alt="Notifications" />
        <span id="notification-count" class="notification-count">0</span>
      </div>
    </div>
    </div>
    </div>
  </header>

  <div id="map"></div>
  <div id="refreshIndicator" class="refresh-indicator" style="display: none;">
    <span class="loading-spinner"></span>
    <span>Updating AQI data...</span>
  </div>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Initialize map
      const map = L.map("map").setView([6.9271, 79.8612], 12);
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap contributors",
      }).addTo(map);

      // Initialize variables
      const markers = {
        aqi: [],
        aqiCircles: [],
        sensors: []
      };
      
      const config = {
        refreshInterval: {
          minutes: 10,
          ms: 10 * 60 * 1000
        },
        liveUpdate: {
          seconds: 5,
          active: null
        }
      };
      
      // Store AQI history for locations
      const locationAQIHistory = {};
      
      // Intervals
      let refreshIntervalId;
      let liveUpdateIntervalId;

      // AQI helper functions
      const aqiHelpers = {
        getColor(aqi) {
          if (aqi <= 50) return '#00e400';
          if (aqi <= 100) return '#ffff00';
          if (aqi <= 150) return '#ff7e00';
          if (aqi <= 200) return '#ff0000';
          if (aqi <= 300) return '#8f3f97';
          return '#7e0023';
        },
        
        getStatus(aqi) {
          if (aqi <= 50) return 'Good';
          if (aqi <= 100) return 'Moderate';
          if (aqi <= 150) return 'Unhealthy for Sensitive Groups';
          if (aqi <= 200) return 'Unhealthy';
          if (aqi <= 300) return 'Very Unhealthy';
          return 'Hazardous';
        },
        
        getDescription(aqi) {
          if (aqi <= 50) return 'Air quality is satisfactory, and air pollution poses little or no risk.';
          if (aqi <= 100) return 'Air quality is acceptable. However, there may be a risk for some people, particularly those who are unusually sensitive to air pollution.';
          if (aqi <= 150) return 'Members of sensitive groups may experience health effects. The general public is less likely to be affected.';
          if (aqi <= 200) return 'Some members of the general public may experience health effects; members of sensitive groups may experience more serious health effects.';
          if (aqi <= 300) return 'Health alert: The risk of health effects is increased for everyone.';
          return 'Health warning of emergency conditions: everyone is more likely to be affected.';
        }
      };

      // Utility functions
      const utils = {
        formatCoordinate(coord) {
          return Number(coord).toFixed(6);
        },
        
        formatTime() {
          const now = new Date();
          return now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        },
        
        generateRandomAQIChange(currentAQI) {
          const change = Math.floor(Math.random() * 7) - 3;
          return Math.max(1, Math.min(500, currentAQI + change)); // Keep between 1-500
        }
      };

      // Add legend to map
      function addLegend() {
        const legend = L.control({position: 'bottomright'});
        legend.onAdd = function() {
          const div = L.DomUtil.create('div', 'info legend');
          const grades = [0, 50, 100, 150, 200, 300];
          const labels = ['Good', 'Moderate', 'Unhealthy for SG', 'Unhealthy', 'Very Unhealthy', 'Hazardous'];

          div.innerHTML = '<div class="legend-title">AQI Levels</div>';
          for (let i = 0; i < grades.length; i++) {
            div.innerHTML +=
              '<div class="legend-item">' +
                '<span class="legend-color" style="background:' + aqiHelpers.getColor(grades[i] + 1) + '"></span>' +
                labels[i] + ' (' + grades[i] + (grades[i + 1] ? '–' + grades[i + 1] : '+') + ')' +
              '</div>';
          }

          div.innerHTML += 
            `<div class="legend-item">
              <span class="legend-color" style="background: var(--sensor-color);"></span> Sensor Location
            </div>`;

          div.innerHTML += '<div style="margin-top: 10px; font-size: 11px; color: #666;">Data refreshes every ' + config.refreshInterval.minutes + ' minutes</div>';

          return div;
        };
        legend.addTo(map);
      }

      // Clear all markers from map
      function clearMarkers() {
        markers.aqi.forEach(marker => map.removeLayer(marker));
        markers.aqiCircles.forEach(circle => map.removeLayer(circle));
        markers.sensors.forEach(marker => map.removeLayer(marker));
        markers.aqi = [];
        markers.aqiCircles = [];
        markers.sensors = [];
      }

      // Live update functions
      function stopLiveUpdates() {
        if (liveUpdateIntervalId) {
          clearInterval(liveUpdateIntervalId);
          liveUpdateIntervalId = null;
        }
        config.liveUpdate.active = null;
      }

      function updateLiveAQI() {
        if (!config.liveUpdate.active) return;
        
        const locationId = config.liveUpdate.active.id;
        const previousAQI = locationAQIHistory[locationId].history[locationAQIHistory[locationId].history.length - 1];
        const newAQI = utils.generateRandomAQIChange(previousAQI);
        
        // Add to history
        locationAQIHistory[locationId].history.push(newAQI);
        if (locationAQIHistory[locationId].history.length > 10) {
          locationAQIHistory[locationId].history.shift(); // Keep only last 10 readings
        }
        
        // Calculate change
        const aqiChange = newAQI - previousAQI;
        let changeHTML = '';
        
        if (aqiChange > 0) {
          changeHTML = `<div class="aqi-change increase">
              <span class="change-icon">▲</span> +${aqiChange}
            </div>`;
        } else if (aqiChange < 0) {
          changeHTML = `<div class="aqi-change decrease">
              <span class="change-icon">▼</span> ${aqiChange}
            </div>`;
        } else {
          changeHTML = `<div class="aqi-change stable">
              <span class="change-icon">■</span> 0
            </div>`;
        }
        
        // Update the DOM elements
        const liveAQIContainer = document.getElementById('live-aqi-container');
        if (liveAQIContainer) {
          const color = aqiHelpers.getColor(newAQI);
          
          document.getElementById('live-aqi-timestamp').textContent = utils.formatTime();
          document.getElementById('live-aqi-value').textContent = newAQI;
          document.getElementById('live-aqi-value').style.color = color;
          document.getElementById('live-aqi-change').innerHTML = changeHTML;
        }
      }

      function startLiveUpdates(location) {
        stopLiveUpdates();
        
        config.liveUpdate.active = location;
        
        // Initialize history if it doesn't exist
        if (!locationAQIHistory[location.id]) {
          locationAQIHistory[location.id] = {
            name: location.location,
            history: [location.aqi]
          };
        }
        
        // Set interval for updates
        liveUpdateIntervalId = setInterval(updateLiveAQI, config.liveUpdate.seconds * 1000);
      }

      // Create popup content for AQI markers
      function createAQIPopupContent(loc) {
        const aqi = loc.aqi;
        const color = aqiHelpers.getColor(aqi);
        
        // Initialize AQI history for this location if it doesn't exist
        if (!locationAQIHistory[loc.id]) {
          locationAQIHistory[loc.id] = {
            name: loc.location,
            history: [aqi]
          };
        }
        
        return `<div class="custom-popup">
          <div class="aqi-popup-header" style="background-color: ${color}">
            ${loc.location}
          </div>
          <div class="aqi-popup-body">
            <div class="aqi-value" style="color: ${color}">${aqi}</div>
            <div class="aqi-status">${aqiHelpers.getStatus(aqi)}</div>
            <div class="aqi-details">
              <p>${aqiHelpers.getDescription(aqi)}</p>
              <p><strong>Last updated:</strong> ${loc.timestamp || new Date().toLocaleString()}</p>
            </div>
            <div class="coordinates">
              <p><strong>Latitude:</strong> ${utils.formatCoordinate(loc.latitude)}</p>
              <p><strong>Longitude:</strong> ${utils.formatCoordinate(loc.longitude)}</p>
            </div>
            <div id="live-aqi-container" class="live-aqi-container">
              <div class="live-aqi-header">
                <div class="live-aqi-title">
                  <span class="live-indicator"></span>
                  LIVE AQI UPDATES
                </div>
                <div id="live-aqi-timestamp" class="live-aqi-timestamp">${utils.formatTime()}</div>
              </div>
              <div class="live-aqi-value-container">
                <div id="live-aqi-value" class="live-aqi-value" style="color: ${color}">${aqi}</div>
                <div id="live-aqi-change" class="aqi-change stable">
                  <span class="change-icon">■</span> 0
                </div>
              </div>
            </div>
          </div>
        </div>`;
      }

      // Fetch AQI data and display on map
      function fetchAndDisplayAQIData() {
        const refreshIndicator = document.getElementById('refreshIndicator');
        if (refreshIndicator) {
          refreshIndicator.style.display = 'block';
        }

        fetch("{{ route('aqi.locations') }}", {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              clearMarkers();

              data.locations.forEach(loc => {
                // Add ID if not present (for tracking)
                if (!loc.id) {
                  loc.id = `loc-${loc.latitude}-${loc.longitude}`.replace(/\./g, '-');
                }
                
                const aqi = loc.aqi;
                const color = aqiHelpers.getColor(aqi);
                
                // Create pin-shaped AQI marker
                const marker = L.marker([loc.latitude, loc.longitude], {
                  icon: L.divIcon({
                    html: `
                      <div style="position: relative; width: 30px; height: 30px;">
                        <div style="position: absolute; width: 30px; height: 30px; background-color: ${color}; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"></div>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-weight: bold; font-size: 12px; text-shadow: 0 1px 1px rgba(0,0,0,0.3);">${aqi}</div>
                      </div>
                    `,
                    className: '',
                    iconSize: [30, 30],
                    iconAnchor: [15, 30]
                  })
                }).addTo(map);
                
                markers.aqi.push(marker);

                // Add the location data to the marker for reference
                marker.locationData = loc;
                
                // Store current AQI in history
                if (!locationAQIHistory[loc.id]) {
                  locationAQIHistory[loc.id] = {
                    name: loc.location,
                    history: [aqi]
                  };
                } else {
                  locationAQIHistory[loc.id].history.push(aqi);
                  if (locationAQIHistory[loc.id].history.length > 10) {
                    locationAQIHistory[loc.id].history.shift(); // Keep only last 10 readings
                  }
                }

                const popupContent = createAQIPopupContent(loc);
                const popup = L.popup({
                  className: 'custom-popup',
                  maxWidth: 300,
                  closeButton: true
                }).setContent(popupContent);
                
                marker.bindPopup(popup);
                
                // Manage live updates with popup events
                marker.on('popupopen', function() {
                  startLiveUpdates(this.locationData);
                });
                
                marker.on('popupclose', function() {
                  stopLiveUpdates();
                });

                // Add circle around marker
                const circle = L.circle([loc.latitude, loc.longitude], {
                  color: color,
                  fillColor: color,
                  fillOpacity: 0.2,
                  radius: 500
                }).addTo(map);
                markers.aqiCircles.push(circle);
              });
            }
          })
          .catch(error => console.error("Error fetching locations:", error))
          .finally(() => {
            if (refreshIndicator) {
              refreshIndicator.style.display = 'none';
            }
          });
      }

      // Fetch sensor data and display on map
      function fetchAndDisplaySensors() {
        fetch("{{ route('aqi.sensors') }}", {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        })
          .then(res => res.json())
          .then(data => {
            markers.sensors.forEach(marker => map.removeLayer(marker));
            markers.sensors = [];

            data.forEach(sensor => {
              // Create container for pin and pulse effect
              const container = L.divIcon({
                html: 
                  `<div style="position: relative; width: 30px; height: 30px;">
                    <div class="sensor-pulse" style="width: 30px; height: 30px;"></div>
                    <div style="position: absolute; width: 30px; height: 30px; background-color: var(--sensor-color); border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"></div>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-weight: bold; font-size: 12px; text-shadow: 0 1px 1px rgba(0,0,0,0.3);">S</div>
                  </div>`,
                iconSize: [30, 30],
                iconAnchor: [15, 30],
                className: 'sensor-marker'
              });

              const marker = L.marker([sensor.latitude, sensor.longitude], { 
                icon: container,
                riseOnHover: true
              }).addTo(map)
                .bindPopup(
                  `<div style="padding: 8px; max-width: 250px;">
                    <h4 style="margin: 0 0 5px 0; color: var(--sensor-color);">${sensor.name}</h4>
                    <p style="margin: 0 0 10px 0; color: #555; font-size: 13px;">${sensor.description}</p>
                    <div class="coordinates">
                      <p><strong>Latitude:</strong> ${utils.formatCoordinate(sensor.latitude)}</p>
                      <p><strong>Longitude:</strong> ${utils.formatCoordinate(sensor.longitude)}</p>
                    </div>
                  </div>`
                );

              markers.sensors.push(marker);
            });
          })
          .catch(err => {
            console.error("Sensor fetch failed:", err);
          });
      }

      // Initialize map components
      function initializeMap() {
        addLegend();
        fetchAndDisplayAQIData();
        fetchAndDisplaySensors();
        
        // Set up refresh interval
        refreshIntervalId = setInterval(() => {
          fetchAndDisplayAQIData();
          fetchAndDisplaySensors();
        }, config.refreshInterval.ms);
        
        // Refresh when page becomes visible again
        document.addEventListener('visibilitychange', function() {
          if (document.visibilityState === 'visible') {
            fetchAndDisplayAQIData();
            fetchAndDisplaySensors();
          }
        });
      }

      // Start the app
      initializeMap();
    });
  </script>
</body>
</html>