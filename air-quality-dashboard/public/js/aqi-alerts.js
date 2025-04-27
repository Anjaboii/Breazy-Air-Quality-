document.addEventListener('DOMContentLoaded', function() {
    // Reference to the alerts button and badge
    const alertsButton = document.getElementById('aqiAlertsButton');
    const alertCountBadge = document.getElementById('aqiAlertCount');
    
    let alertsPopup = null;
    let alertsOverlay = null;
    let alertsData = [];
    
    // Fetch AQI alerts from the server
    function fetchAQIAlerts() {
        fetch('/api/aqi-alerts')
            .then(response => response.json())
            .then(data => {
                alertsData = data;
                updateAlertBadge();
            })
            .catch(error => {
                console.error('Error fetching AQI alerts:', error);
            });
    }
    
    // Update the alert badge count
    function updateAlertBadge() {
        if (alertsData.length > 0) {
            alertCountBadge.style.display = 'inline-block';
            alertCountBadge.textContent = alertsData.length;
            alertCountBadge.classList.add('badge', 'bg-danger');
        } else {
            alertCountBadge.style.display = 'none';
        }
    }
    
    // Create the alerts popup
    function createAlertsPopup() {
        // Create overlay
        alertsOverlay = document.createElement('div');
        alertsOverlay.className = 'aqi-alert-overlay';
        document.body.appendChild(alertsOverlay);
        
        // Create popup
        alertsPopup = document.createElement('div');
        alertsPopup.className = 'aqi-alert-popup';
        
        // Create popup content
        const popupContent = document.createElement('div');
        popupContent.className = 'aqi-alert-popup-content';
        
        // Create header
        const header = document.createElement('div');
        header.className = 'aqi-alert-popup-header';
        
        const title = document.createElement('h5');
        title.textContent = 'AQI Alerts';
        title.style.margin = '0';
        
        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'btn-close';
        closeButton.setAttribute('aria-label', 'Close');
        closeButton.addEventListener('click', closeAlertsPopup);
        
        header.appendChild(title);
        header.appendChild(closeButton);
        
        // Create body
        const body = document.createElement('div');
        body.className = 'aqi-alert-popup-body';
        
        // Create footer
        const footer = document.createElement('div');
        footer.className = 'aqi-alert-popup-footer';
        
        const dismissAllButton = document.createElement('button');
        dismissAllButton.type = 'button';
        dismissAllButton.className = 'btn btn-sm btn-primary';
        dismissAllButton.textContent = 'Dismiss All';
        dismissAllButton.addEventListener('click', dismissAllAlerts);
        
        footer.appendChild(dismissAllButton);
        
        // Assemble popup
        popupContent.appendChild(header);
        popupContent.appendChild(body);
        popupContent.appendChild(footer);
        alertsPopup.appendChild(popupContent);
        
        document.body.appendChild(alertsPopup);
        
        // Populate alerts
        populateAlerts(body);
    }
    
    // Populate the alerts in the popup
    function populateAlerts(bodyElement) {
        bodyElement.innerHTML = '';
        
        if (alertsData.length === 0) {
            const noAlerts = document.createElement('p');
            noAlerts.textContent = 'No alerts at this time.';
            noAlerts.style.textAlign = 'center';
            noAlerts.style.color = '#6c757d';
            noAlerts.style.marginTop = '1rem';
            bodyElement.appendChild(noAlerts);
            return;
        }
        
        alertsData.forEach((alert, index) => {
            const alertItem = document.createElement('div');
            alertItem.className = `aqi-alert-item aqi-${getAQIClass(alert.aqi)}`;
            
            const location = document.createElement('div');
            location.className = 'aqi-alert-location';
            location.textContent = alert.location;
            
            const message = document.createElement('div');
            message.className = 'aqi-alert-message';
            message.textContent = alert.message;
            
            const time = document.createElement('div');
            time.className = 'aqi-alert-time';
            time.textContent = formatAlertTime(alert.timestamp);
            
            const dismissButton = document.createElement('button');
            dismissButton.type = 'button';
            dismissButton.className = 'btn btn-sm btn-outline-secondary float-end mt-2';
            dismissButton.textContent = 'Dismiss';
            dismissButton.dataset.index = index;
            dismissButton.addEventListener('click', function() {
                dismissAlert(index);
            });
            
            alertItem.appendChild(location);
            alertItem.appendChild(message);
            alertItem.appendChild(time);
            alertItem.appendChild(dismissButton);
            
            bodyElement.appendChild(alertItem);
        });
    }
    
    // Get the AQI class based on the value
    function getAQIClass(aqi) {
        if (aqi <= 50) return 'good';
        if (aqi <= 100) return 'moderate';
        if (aqi <= 150) return 'unhealthy-sg';
        if (aqi <= 200) return 'unhealthy';
        if (aqi <= 300) return 'very-unhealthy';
        return 'hazardous';
    }
    
    // Format the alert time
    function formatAlertTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleString();
    }
    
    // Show the alerts popup
    function showAlertsPopup() {
        if (!alertsPopup) {
            createAlertsPopup();
        } else {
            const body = alertsPopup.querySelector('.aqi-alert-popup-body');
            populateAlerts(body);
            alertsOverlay.style.display = 'block';
            alertsPopup.style.display = 'flex';
        }
    }
    
    // Close the alerts popup
    function closeAlertsPopup() {
        if (alertsPopup && alertsOverlay) {
            alertsPopup.style.display = 'none';
            alertsOverlay.style.display = 'none';
        }
    }
    
    // Dismiss a specific alert
    function dismissAlert(index) {
        fetch(`/api/aqi-alerts/${alertsData[index].id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                alertsData.splice(index, 1);
                const body = alertsPopup.querySelector('.aqi-alert-popup-body');
                populateAlerts(body);
                updateAlertBadge();
            }
        })
        .catch(error => {
            console.error('Error dismissing alert:', error);
        });
    }
    
    // Dismiss all alerts
    function dismissAllAlerts() {
        fetch('/api/aqi-alerts/dismiss-all', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                alertsData = [];
                const body = alertsPopup.querySelector('.aqi-alert-popup-body');
                populateAlerts(body);
                updateAlertBadge();
            }
        })
        .catch(error => {
            console.error('Error dismissing all alerts:', error);
        });
    }
    
    // Event listener for alerts button
    if (alertsButton) {
        alertsButton.addEventListener('click', function(event) {
            event.preventDefault();
            showAlertsPopup();
        });
    }
    
    // Close popup when clicking outside
    document.addEventListener('click', function(event) {
        if (alertsPopup && 
            alertsOverlay && 
            alertsPopup.style.display === 'flex' && 
            !alertsPopup.contains(event.target) && 
            event.target !== alertsButton) {
            closeAlertsPopup();
        }
    });
    
    // Initial fetch of alerts
    fetchAQIAlerts();
    
    // Setup periodic refresh of alerts (every 5 minutes)
    setInterval(fetchAQIAlerts, 5 * 60 * 1000);
});