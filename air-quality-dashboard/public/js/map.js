document.addEventListener('DOMContentLoaded', function() {
    // Initialize map centered on Colombo
    const map = L.map('map').setView([6.9271, 79.8612], 12);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Create layer groups
    const sensorLayer = L.layerGroup().addTo(map);
    const aqiLocationLayer = L.layerGroup().addTo(map);

    // WAQI API Token
    const WAQI_TOKEN = "4b98b49468bc4a44cc2df7ac4e0007163f430796";

    // Function to convert PM2.5 to AQI
    function pm25ToAqi(pm25) {
        if (pm25 <= 12) return Math.round((50/12) * pm25);
        if (pm25 <= 35.4) return Math.round(((100-51)/(35.4-12)) * (pm25-12) + 51);
        if (pm25 <= 55.4) return Math.round(((150-101)/(55.4-35.4)) * (pm25-35.4) + 101);
        if (pm25 <= 150.4) return Math.round(((200-151)/(150.4-55.4)) * (pm25-55.4) + 151);
        if (pm25 <= 250.4) return Math.round(((300-201)/(250.4-150.4)) * (pm25-150.4) + 201);
        return Math.round(((500-301)/(500.4-250.4)) * (pm25-250.4) + 301);
    }

    // Function to get AQI level details
    function getAqiLevel(aqi) {
        if (aqi <= 50) return {
            color: 'green',
            status: 'Good',
            message: 'Air quality is satisfactory, and air pollution poses little or no risk.'
        };
        if (aqi <= 100) return {
            color: 'yellow',
            status: 'Moderate',
            message: 'Air quality is acceptable; however, there may be a risk for some people.'
        };
        if (aqi <= 150) return {
            color: 'orange',
            status: 'Unhealthy for Sensitive Groups',
            message: 'Members of sensitive groups may experience health effects.'
        };
        if (aqi <= 200) return {
            color: 'red',
            status: 'Unhealthy',
            message: 'Some members of the general public may experience health effects.'
        };
        if (aqi <= 300) return {
            color: 'violet',
            status: 'Very Unhealthy',
            message: 'Health alert: The risk of health effects is increased for everyone.'
        };
        return {
            color: 'black',
            status: 'Hazardous',
            message: 'Health warning of emergency conditions.'
        };
    }

    // Function to create AQI marker
    function createAqiMarker(location, layer) {
        // Create marker with default gray icon (will update when data loads)
        const marker = L.marker([location.latitude, location.longitude], {
            icon: L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-gray.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            })
        }).addTo(layer);
        
        // Store refresh interval ID
        let refreshInterval;
        
        // Function to update marker data
        const updateMarkerData = async () => {
            try {
                const response = await fetch(`https://api.waqi.info/feed/@${location.id}/?token=${WAQI_TOKEN}`);
                const data = await response.json();
                
                if (data.status !== "ok") throw new Error("API error");
                
                // Get current AQI (use direct AQI if available, otherwise calculate from PM2.5)
                const currentAqi = data.data.aqi || (data.data.iaqi?.pm25?.v ? pm25ToAqi(data.data.iaqi.pm25.v) : null);
                const pm25 = data.data.iaqi?.pm25?.v || null;
                const pm10 = data.data.iaqi?.pm10?.v || null;
                const time = new Date(data.data.time.iso).toLocaleString();
                
                // Get AQI level details
                const aqiLevel = currentAqi !== null ? getAqiLevel(currentAqi) : {
                    color: 'gray',
                    status: 'No Data',
                    message: 'Current air quality data not available'
                };
                
                // Update marker icon color
                marker.setIcon(L.icon({
                    iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-${aqiLevel.color}.png`,
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                }));
                
                // Update popup content if it's open
                if (marker._popup && marker._popup.isOpen()) {
                    const popupElement = marker._popup._contentNode;
                    
                    // Update AQI value
                    const aqiValueElement = popupElement.querySelector('#aqi-value');
                    if (aqiValueElement) {
                        aqiValueElement.textContent = currentAqi !== null ? currentAqi : 'N/A';
                        aqiValueElement.style.color = aqiLevel.color;
                    }
                    
                    // Update AQI status
                    const aqiStatusElement = popupElement.querySelector('#aqi-status');
                    if (aqiStatusElement) {
                        aqiStatusElement.textContent = aqiLevel.status;
                    }
                    
                    // Update AQI message
                    const aqiMessageElement = popupElement.querySelector('#aqi-message');
                    if (aqiMessageElement) {
                        aqiMessageElement.textContent = aqiLevel.message;
                    }
                    
                    // Update PM values
                    const pm25Element = popupElement.querySelector('#pm25-value');
                    if (pm25Element) {
                        pm25Element.textContent = pm25 !== null ? pm25 : 'N/A';
                    }
                    
                    const pm10Element = popupElement.querySelector('#pm10-value');
                    if (pm10Element) {
                        pm10Element.textContent = pm10 !== null ? pm10 : 'N/A';
                    }
                    
                    // Update time
                    const updateTimeElement = popupElement.querySelector('#update-time');
                    if (updateTimeElement) {
                        updateTimeElement.textContent = time;
                    }
                    
                    // Render chart if data exists
                    if (pm25 !== null && data.data.forecast?.daily?.pm25) {
                        renderAqiChart(
                            `aqi-chart-${location.id}`,
                            data.data.forecast.daily.pm25.map(item => ({
                                date: item.day,
                                pm25: item.avg,
                                aqi: pm25ToAqi(item.avg)
                            }))
                        );
                    }
                }
                
                return { currentAqi, pm25, pm10, time, aqiLevel };
                
            } catch (error) {
                console.error("WAQI API Error:", error);
                // Update marker to show error state
                marker.setIcon(L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                }));
                
                if (marker._popup && marker._popup.isOpen()) {
                    const popupElement = marker._popup._contentNode;
                    const errorElement = popupElement.querySelector('.error-message') || document.createElement('div');
                    errorElement.className = 'error-message';
                    errorElement.style.color = 'red';
                    errorElement.style.padding = '10px';
                    errorElement.textContent = 'Failed to load air quality data. Please try again later.';
                    
                    if (!popupElement.querySelector('.error-message')) {
                        popupElement.appendChild(errorElement);
                    }
                }
                
                throw error;
            }
        };
        
        // Initial popup content
        const initialPopupContent = `
            <div style="min-width: 300px; font-family: Arial, sans-serif;">
                <h3 style="margin: 0 0 10px 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                    ${location.name || 'Unknown Location'}
                </h3>
                
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <div id="aqi-value" style="font-size: 28px; font-weight: bold; margin-right: 15px;">Loading...</div>
                    <div>
                        <div id="aqi-status" style="font-weight: bold; font-size: 16px;">Loading...</div>
                        <div id="aqi-message" style="font-size: 13px; color: #666;">Loading air quality data...</div>
                    </div>
                </div>
                
                <div style="display: flex; justify-content: space-around; margin-bottom: 15px;">
                    <div style="text-align: center;">
                        <div style="font-size: 12px; color: #666;">PM<sub>2.5</sub></div>
                        <div id="pm25-value" style="font-size: 18px; font-weight: bold;">Loading...</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 12px; color: #666;">PM<sub>10</sub></div>
                        <div id="pm10-value" style="font-size: 18px; font-weight: bold;">Loading...</div>
                    </div>
                </div>
                
                <div style="height: 200px; margin-bottom: 10px;">
                    <canvas id="aqi-chart-${location.id}"></canvas>
                </div>
                
                <div style="border-top: 1px solid #eee; padding-top: 5px; font-size: 12px;">
                    <p style="margin: 5px 0;"><strong>Last updated:</strong> <span id="update-time">Loading...</span></p>
                    <p style="margin: 5px 0; color: #666;">Source: WAQI</p>
                </div>
            </div>
        `;
        
        // Bind popup
        marker.bindPopup(initialPopupContent);
        
        // Handle popup open/close events
        marker.on('popupopen', async function() {
            // Immediately fetch data when popup opens
            await updateMarkerData();
            
            // Start refreshing data every 6 seconds while popup is open
            refreshInterval = setInterval(async () => {
                try {
                    await updateMarkerData();
                } catch (error) {
                    console.error("Error during refresh:", error);
                }
            }, 6000);
        });
        
        marker.on('popupclose', function() {
            // Clear refresh interval when popup closes
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }
        });
        
        // Initial data load (without waiting for popup to open)
        updateMarkerData().catch(error => console.error("Initial data load failed:", error));
        
        return marker;
    }

    // Render AQI chart
    function renderAqiChart(chartId, historyData) {
        const ctx = document.getElementById(chartId);
        if (!ctx) return;
        
        // Destroy existing chart if it exists
        if (ctx.chart) {
            ctx.chart.destroy();
        }
        
        const labels = historyData.map(item => new Date(item.date).toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric' 
        }));
        
        const aqiValues = historyData.map(item => item.aqi);
        
        ctx.chart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'AQI (from PM2.5)',
                    data: aqiValues,
                    borderColor: '#4a6bff',
                    backgroundColor: 'rgba(74, 107, 255, 0.1)',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: function(context) {
                        const aqi = context.dataset.data[context.dataIndex];
                        if (aqi <= 50) return '#00e400';
                        if (aqi <= 100) return '#ffff00';
                        if (aqi <= 150) return '#ff7e00';
                        if (aqi <= 200) return '#ff0000';
                        if (aqi <= 300) return '#99004c';
                        return '#7e0023';
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'AQI Value'
                        },
                        suggestedMin: 0,
                        suggestedMax: Math.max(...aqiValues) + 20
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `AQI: ${context.raw}`;
                            },
                            afterLabel: function(context) {
                                const pm25 = historyData[context.dataIndex].pm25;
                                return `PM2.5: ${pm25} µg/m³`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Function to create sensor marker
    function createSensorMarker(sensor) {
        const marker = L.marker([sensor.latitude, sensor.longitude], {
            icon: L.divIcon({
                className: 'sensor-marker',
                html: `
                    <div style="background-color: #4a6bff; 
                                width: 20px; 
                                height: 20px; 
                                border-radius: 50%; 
                                border: 2px solid white;
                                display: flex;
                                justify-content: center;
                                align-items: center;
                                color: white;
                                font-weight: bold;
                                font-size: 10px;">
                        S
                    </div>`,
                iconSize: [24, 24]
            })
        }).addTo(sensorLayer);
        
        const popupContent = `
        <div style="min-width: 250px; font-family: Arial, sans-serif; color: #333;">
            <div style="background-color: #4a6bff; padding: 10px; border-radius: 5px; color: white; font-size: 16px; font-weight: bold;">
                ${sensor.name || 'Unknown Sensor'}
            </div>
            <div style="padding: 15px 10px;">
                <p style="margin: 0 0 8px 0; font-size: 14px;">
                    <strong>Latitude:</strong> ${sensor.latitude || 'N/A'}
                </p>
                <p style="margin: 0 0 8px 0; font-size: 14px;">
                    <strong>Longitude:</strong> ${sensor.longitude || 'N/A'}
                </p>
            </div>
            <div style="padding: 10px; background-color: #f9f9f9; border-radius: 5px; font-size: 12px; color: #666;">
                <p style="margin: 0;">This sensor is part of a network providing real-time environmental data.</p>
            </div>
        </div>
        `;

        marker.bindPopup(popupContent);
        return marker;
    }

    // Fetch and display AQI locations
    fetch('/api/aqi-locations')
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(locations => {
            if (!locations || !Array.isArray(locations)) {
                throw new Error('Invalid locations data received');
            }
            
            locations.forEach(location => {
                try {
                    createAqiMarker(location, aqiLocationLayer);
                } catch (e) {
                    console.error('Error creating AQI marker:', e);
                }
            });
        })
        .catch(error => console.error('Error fetching AQI locations:', error));

    // Fetch and display sensors
    fetch('/api/sensors')
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(sensors => {
            if (!sensors || !Array.isArray(sensors)) {
                throw new Error('Invalid sensors data received');
            }
            
            sensors.forEach(sensor => {
                try {
                    createSensorMarker(sensor);
                } catch (e) {
                    console.error('Error creating sensor marker:', e);
                }
            });
        })
        .catch(error => console.error('Error fetching sensors:', error));

    // Add layer control
    const baseLayers = {
        "OpenStreetMap": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png')
    };

    const overlays = {
        "AQI Locations": aqiLocationLayer,
        "Sensors": sensorLayer
    };

    L.control.layers(baseLayers, overlays, {collapsed: false}).addTo(map);
});