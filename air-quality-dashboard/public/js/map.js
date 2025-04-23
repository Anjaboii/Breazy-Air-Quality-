document.addEventListener('DOMContentLoaded', function() {
    // Initialize map centered on Colombo
    const map = L.map('map').setView([6.9271, 79.8612], 12);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Create layer groups for different types
    const sensorLayer = L.layerGroup().addTo(map);
    const aqiLocationLayer = L.layerGroup().addTo(map);

    // Function to create AQI marker with color coding
    function createAqiMarker(location, layer) {
        const aqi = location.aqi || 0;
        let color;
        
        if (aqi <= 50) color = '#00e400';
        else if (aqi <= 100) color = '#ffff00';
        else if (aqi <= 150) color = '#ff7e00';
        else if (aqi <= 200) color = '#ff0000';
        else if (aqi <= 300) color = '#99004c';
        else color = '#7e0023';
        
        const marker = L.marker([location.latitude, location.longitude], {
            icon: L.divIcon({
                className: 'aqi-marker',
                html: `
                    <div style="background-color: ${color}; 
                                width: 24px; 
                                height: 24px; 
                                border-radius: 50%; 
                                border: 2px solid white; 
                                display: flex; 
                                justify-content: center; 
                                align-items: center; 
                                color: ${aqi > 150 ? 'white' : 'black'}; 
                                font-weight: bold;
                                font-size: 12px;">
                        ${aqi}
                    </div>`,
                iconSize: [24, 24]
            })
        }).addTo(layer);
        
        return marker;
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
        
        return marker;
    }

    // Function to handle fetch errors
    function handleFetchError(error, message) {
        console.error(message, error);
        // You could add visual error feedback here
    }

    // Fetch and display AQI locations with error handling
    fetch('/api/aqi-locations')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(locations => {
            if (!locations || !Array.isArray(locations)) {
                throw new Error('Invalid locations data received');
            }
            
            locations.forEach(location => {
                try {
                    const marker = createAqiMarker(location, aqiLocationLayer);
                    
                    marker.bindPopup(`
                        <div style="min-width: 200px;">
                            <h5 style="margin: 0 0 5px 0;">${location.name || 'Unknown Location'}</h5>
                            <p style="margin: 0 0 5px 0;">AQI: ${location.aqi || 'N/A'}</p>
                            <p style="margin: 0; font-size: 12px;">
                                ${location.latitude}, ${location.longitude}
                            </p>
                        </div>
                    `);
                } catch (e) {
                    handleFetchError(e, 'Error creating AQI marker:');
                }
            });
        })
        .catch(error => handleFetchError(error, 'Error fetching AQI locations:'));

    // Fetch and display sensors with error handling
    fetch('/api/sensors')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(sensors => {
            if (!sensors || !Array.isArray(sensors)) {
                throw new Error('Invalid sensors data received');
            }
            
            sensors.forEach(sensor => {
                try {
                    const marker = createSensorMarker(sensor);
                    
                    // Fetch readings for this sensor
                    fetch(`/api/sensors/${sensor.id}/readings`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(readings => {
                            if (!readings || !Array.isArray(readings)) {
                                throw new Error('Invalid readings data received');
                            }
                            
                            const labels = readings.map(r => r.timestamp ? new Date(r.timestamp).toLocaleTimeString() : 'N/A');
                            const data = readings.map(r => r.aqi || 0);
                            
                            const popupContent = `
                                <div style="min-width: 300px;">
                                    <h5 style="margin: 0 0 10px 0;">${sensor.name || 'Unknown Sensor'}</h5>
                                    <div style="height: 200px;">
                                        <canvas id="chart-${sensor.id}"></canvas>
                                    </div>
                                    <p style="margin: 10px 0 0 0; font-size: 12px;">
                                        ${sensor.latitude}, ${sensor.longitude}
                                    </p>
                                </div>
                            `;
                            
                            marker.bindPopup(popupContent).on('popupopen', () => {
                                try {
                                    const ctx = document.getElementById(`chart-${sensor.id}`).getContext('2d');
                                    new Chart(ctx, {
                                        type: 'line',
                                        data: {
                                            labels: labels.reverse(),
                                            datasets: [{
                                                label: 'AQI (24h)',
                                                data: data.reverse(),
                                                borderColor: '#4a6bff',
                                                backgroundColor: 'rgba(74, 107, 255, 0.1)',
                                                tension: 0.1,
                                                fill: true
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            scales: {
                                                y: {
                                                    beginAtZero: false
                                                }
                                            }
                                        }
                                    });
                                } catch (e) {
                                    handleFetchError(e, 'Error creating chart:');
                                }
                            });
                        })
                        .catch(error => handleFetchError(error, `Error fetching readings for sensor ${sensor.id}:`));
                } catch (e) {
                    handleFetchError(e, 'Error creating sensor marker:');
                }
            });
        })
        .catch(error => handleFetchError(error, 'Error fetching sensors:'));

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