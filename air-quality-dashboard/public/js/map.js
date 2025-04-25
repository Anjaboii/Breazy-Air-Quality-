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

    // Function to create AQI marker with color coding
    function createAqiMarker(location, layer) {
        const aqi = location.aqi || 0;
        let color, status, message;
        
        // Determine color and status based on AQI value
        if (aqi <= 50) {
            color = 'green';
            status = 'Good';
            message = 'Air quality is satisfactory, and air pollution poses little or no risk.';
        } else if (aqi <= 100) {
            color = 'yellow';
            status = 'Moderate';
            message = 'Air quality is acceptable; however, there may be a risk for some people.';
        } else if (aqi <= 150) {
            color = 'orange';
            status = 'Unhealthy for Sensitive Groups';
            message = 'Members of sensitive groups may experience health effects.';
        } else if (aqi <= 200) {
            color = 'red';
            status = 'Unhealthy';
            message = 'Some members of the general public may experience health effects.';
        } else if (aqi <= 300) {
            color = 'violet';
            status = 'Very Unhealthy';
            message = 'Health alert: The risk of health effects is increased for everyone.';
        } else {
            color = 'black';
            status = 'Hazardous';
            message = 'Health warning of emergency conditions.';
        }
        
        // Create marker with colored icon
        const marker = L.marker([location.latitude, location.longitude], {
            icon: L.icon({
                iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-${color}.png`,
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            })
        }).addTo(layer);
        
        // Create popup content with chart placeholder
        const popupContent = `
            <div style="min-width: 300px; font-family: Arial, sans-serif;">
                <h3 style="margin: 0 0 10px 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                    ${location.name || 'Unknown Location'}
                </h3>
                
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <div style="font-size: 28px; font-weight: bold; margin-right: 15px; color: ${color};">${aqi}</div>
                    <div>
                        <div style="font-weight: bold; font-size: 16px;">${status}</div>
                        <div style="font-size: 13px; color: #666;">${message}</div>
                    </div>
                </div>
                
                <div style="height: 200px; margin-bottom: 10px;">
                    <canvas id="aqi-chart-${location.id}"></canvas>
                </div>
                
                <div style="border-top: 1px solid #eee; padding-top: 5px; font-size: 12px;">
                    <p style="margin: 5px 0;"><strong>Coordinates:</strong> ${location.latitude}, ${location.longitude}</p>
                    <p style="margin: 5px 0;"><strong>Last updated:</strong> ${new Date().toLocaleString()}</p>
                </div>
            </div>
        `;
        
        // Bind popup with loading state
        marker.bindPopup(popupContent);
        
        // Fetch historical data when popup opens
        marker.on('popupopen', function() {
            fetchHistoricalAQIData(location.id, `aqi-chart-${location.id}`);
        });
        
        return marker;
    }

    // Function to fetch historical AQI data and render chart
    function fetchHistoricalAQIData(locationId, chartId) {
        // Replace with your actual API endpoint for historical data
        fetch(`/api/aqi-locations/${locationId}/history`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (!data || !Array.isArray(data)) {
                    throw new Error('Invalid data format');
                }
                
                // Process data for chart
                const labels = data.map(item => 
                    new Date(item.timestamp).toLocaleDateString('en-US', { 
                        month: 'short', 
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })
                );
                
                const aqiValues = data.map(item => item.aqi);
                
                // Get canvas context
                const ctx = document.getElementById(chartId);
                if (!ctx) return;
                
                // Create or update chart
                new Chart(ctx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'AQI History',
                            data: aqiValues,
                            borderColor: '#4a6bff',
                            backgroundColor: 'rgba(74, 107, 255, 0.1)',
                            tension: 0.1,
                            fill: true,
                            pointRadius: 3,
                            pointBackgroundColor: function(context) {
                                const value = context.dataset.data[context.dataIndex];
                                if (value <= 50) return '#00e400';
                                if (value <= 100) return '#ffff00';
                                if (value <= 150) return '#ff7e00';
                                if (value <= 200) return '#ff0000';
                                if (value <= 300) return '#99004c';
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
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `AQI: ${context.raw}`;
                                    },
                                    afterLabel: function(context) {
                                        const value = context.raw;
                                        if (value <= 50) return 'Good';
                                        if (value <= 100) return 'Moderate';
                                        if (value <= 150) return 'Unhealthy for Sensitive Groups';
                                        if (value <= 200) return 'Unhealthy';
                                        if (value <= 300) return 'Very Unhealthy';
                                        return 'Hazardous';
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching historical AQI data:', error);
                const errorElement = document.createElement('div');
                errorElement.style.color = 'red';
                errorElement.style.padding = '10px';
                errorElement.textContent = 'Failed to load historical data';
                document.getElementById(chartId).replaceWith(errorElement);
            });
    }

    // Function to create sensor marker (unchanged from your original code)
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
        
        // Add popup with sensor information
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

    // Bind the refined popup to the marker
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