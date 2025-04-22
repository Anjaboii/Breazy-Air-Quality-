document.addEventListener('DOMContentLoaded', function() {
    // Initialize map centered on Colombo
    const map = L.map('map').setView([6.9271, 79.8612], 12);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Fetch sensors data
    fetch('/api/sensors')
        .then(response => response.json())
        .then(sensors => {
            sensors.forEach(sensor => {
                const marker = L.marker([sensor.latitude, sensor.longitude]).addTo(map);
                
                // Set marker color based on AQI
                const aqi = sensor.aqi || 0;
                let color;
                
                if (aqi <= 50) color = '#00e400';
                else if (aqi <= 100) color = '#ffff00';
                else if (aqi <= 150) color = '#ff7e00';
                else if (aqi <= 200) color = '#ff0000';
                else if (aqi <= 300) color = '#99004c';
                else color = '#7e0023';
                
                marker.setIcon(L.divIcon({
                    className: 'aqi-marker',
                    html: `<div style="background-color: ${color}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; display: flex; justify-content: center; align-items: center; color: ${aqi > 150 ? 'white' : 'black'}; font-weight: bold;">${aqi}</div>`,
                    iconSize: [24, 24]
                }));
                
                // Fetch readings for this sensor
                fetch(`/api/readings/${sensor.id}`)
                    .then(response => response.json())
                    .then(readings => {
                        const labels = readings.map(r => new Date(r.timestamp).toLocaleTimeString());
                        const data = readings.map(r => r.aqi);
                        
                        // Create popup with chart
                        const popupContent = `
                            <div>
                                <h5>${sensor.name}</h5>
                                <p>Current AQI: ${aqi}</p>
                                <div style="width: 300px; height: 200px;">
                                    <canvas id="chart-${sensor.id}"></canvas>
                                </div>
                            </div>
                        `;
                        
                        marker.bindPopup(popupContent).on('popupopen', () => {
                            const ctx = document.getElementById(`chart-${sensor.id}`).getContext('2d');
                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: labels.reverse(),
                                    datasets: [{
                                        label: 'AQI (24h)',
                                        data: data.reverse(),
                                        borderColor: color,
                                        backgroundColor: `${color}33`,
                                        tension: 0.1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false
                                }
                            });
                        });
                    });
            });
        });
});