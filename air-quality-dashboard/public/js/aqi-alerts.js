document.addEventListener('DOMContentLoaded', async function() {
    // WAQI API Token
    const WAQI_TOKEN = "4b98b49468bc4a44cc2df7ac4e0007163f430796";
    
    // DOM Elements
    const aqiAlertsButton = document.getElementById('aqiAlertsButton');
    const aqiAlertCount = document.getElementById('aqiAlertCount');
    
    // Fetch locations from your database
    async function fetchLocations() {
        try {
            const response = await fetch('/aqi-monitoring-locations');
            if (!response.ok) throw new Error('Network response was not ok');
            return await response.json();
        } catch (error) {
            console.error('Failed to fetch locations:', error);
            return [];
        }
    }

    // Get real-time AQI data from WAQI
    async function fetchAqiData(lat, lng) {
        try {
            const response = await fetch(
                `https://api.waqi.info/feed/geo:${lat};${lng}/?token=${WAQI_TOKEN}`
            );
            const data = await response.json();
            
            if (data.status !== "ok") throw new Error("API error");
            
            return {
                aqi: data.data.aqi,
                pm25: data.data.iaqi?.pm25?.v,
                time: new Date(data.data.time.iso).toLocaleTimeString(),
                city: data.data.city?.name
            };
        } catch (error) {
            console.error("WAQI API Error:", error);
            throw error;
        }
    }

    // Convert PM2.5 to AQI if needed
    function pm25ToAqi(pm25) {
        if (!pm25) return null;
        if (pm25 <= 12) return Math.round((50/12) * pm25);
        if (pm25 <= 35.4) return Math.round(((100-51)/(35.4-12)) * (pm25-12) + 51);
        if (pm25 <= 55.4) return Math.round(((150-101)/(55.4-35.4)) * (pm25-35.4) + 101);
        if (pm25 <= 150.4) return Math.round(((200-151)/(150.4-55.4)) * (pm25-55.4) + 151);
        if (pm25 <= 250.4) return Math.round(((300-201)/(250.4-150.4)) * (pm25-150.4) + 201);
        return Math.round(((500-301)/(500.4-250.4)) * (pm25-250.4) + 301);
    }

    // Get AQI condition info
    function getAqiCondition(aqi) {
        if (!aqi) return null;
        if (aqi <= 50) return { level: 'Good', color: '#00e400' };
        if (aqi <= 100) return { level: 'Moderate', color: '#ffff00' };
        if (aqi <= 150) return { level: 'Unhealthy for Sensitive Groups', color: '#ff7e00' };
        if (aqi <= 200) return { level: 'Unhealthy', color: '#ff0000' };
        if (aqi <= 300) return { level: 'Very Unhealthy', color: '#99004c' };
        return { level: 'Hazardous', color: '#7e0023' };
    }

    // Check for AQI alerts
    async function checkAqiAlerts() {
        const locations = await fetchLocations();
        const alerts = [];
        
        for (const location of locations) {
            try {
                const aqiData = await fetchAqiData(location.latitude, location.longitude);
                const aqi = aqiData.aqi || pm25ToAqi(aqiData.pm25);
                
                if (aqi > 50) { // Only show alerts for AQI > 50
                    const condition = getAqiCondition(aqi);
                    alerts.push({
                        location: location.name,
                        aqi: aqi,
                        condition: condition.level,
                        color: condition.color,
                        pm25: aqiData.pm25,
                        time: aqiData.time
                    });
                }
            } catch (error) {
                console.error(`Error checking AQI for ${location.name}:`, error);
            }
        }
        
        return alerts;
    }

    // Display alerts in popup
    async function showAqiAlerts() {
        const alerts = await checkAqiAlerts();
        aqiAlertCount.textContent = alerts.length;
        aqiAlertCount.style.display = alerts.length > 0 ? 'inline-block' : 'none';
        
        // Create or update popup
        let popup = document.getElementById('aqiAlertPopup');
        if (!popup) {
            popup = document.createElement('div');
            popup.id = 'aqiAlertPopup';
            popup.className = 'aqi-alert-popup';
            document.body.appendChild(popup);
        }
        
        popup.innerHTML = `
            <div class="aqi-popup-header">
                <h4>AQI Alerts</h4>
                <button class="close-btn">&times;</button>
            </div>
            <div class="aqi-popup-content">
                ${alerts.length === 0 ? 
                    '<div class="no-alerts">No current AQI alerts</div>' : 
                    alerts.map(alert => `
                        <div class="aqi-alert" style="border-left-color: ${alert.color}">
                            <h5>${alert.location}</h5>
                            <div class="aqi-value" style="color: ${alert.color}">
                                AQI: ${alert.aqi} (${alert.condition})
                            </div>
                            <div class="aqi-details">
                                PM2.5: ${alert.pm25 || 'N/A'} µg/m³
                                <span class="time">${alert.time}</span>
                            </div>
                        </div>
                    `).join('')
                }
            </div>
        `;
        
        // Position popup near button
        const buttonRect = aqiAlertsButton.getBoundingClientRect();
        popup.style.top = `${buttonRect.bottom + window.scrollY + 5}px`;
        popup.style.left = `${buttonRect.left}px`;
        
        // Close button handler
        popup.querySelector('.close-btn').addEventListener('click', () => {
            popup.remove();
        });
    }

    // Initialize
    aqiAlertsButton.addEventListener('click', async (e) => {
        e.preventDefault();
        showAqiAlerts();
    });

    // Initial check
    await checkAqiAlerts();
});