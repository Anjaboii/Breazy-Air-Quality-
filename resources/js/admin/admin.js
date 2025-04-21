function fetchAQI() {
    const lat = document.getElementById("lat").value;
    const lon = document.getElementById("lon").value;

    if (!lat || !lon) {
        alert("Please enter Latitude and Longitude first.");
        return;
    }

    fetch(`/api/aqi?lat=${lat}&lon=${lon}`)
        .then(response => response.json())
        .then(data => {
            if (data.aqi) {
                document.getElementById("aqi").value = data.aqi;
            } else {
                alert("No AQI data found for this location.");
            }
        })
        .catch(err => {
            console.error("AQI Fetch Error:", err);
            alert("Error fetching AQI data.");
        });
}

document.getElementById("locationForm").addEventListener("submit", function(event) {
    event.preventDefault();

    const formData = {
        location: document.getElementById("location").value,
        lat: document.getElementById("lat").value,
        lon: document.getElementById("lon").value,
        aqi: document.getElementById("aqi").value,
        _token: document.querySelector('input[name="_token"]').value
    };

    fetch("{{ route('admin.locations.store') }}", {
        method: "POST",
        headers: { 
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": formData._token
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        window.location.reload();
    })
    .catch(err => {
        console.error("Add Location Error:", err);
        alert("Error adding location.");
    });
});

// Profile modal functionality
const profileModal = document.getElementById("profileModal");

document.getElementById("profileButton").onclick = () => {
    profileModal.style.display = "block";
};

document.getElementById("closeModal").onclick = () => {
    profileModal.style.display = "none";
};

window.onclick = (event) => {
    if (event.target === profileModal) {
        profileModal.style.display = "none";
    }
};

function deleteLocation(id) {
    if (confirm("Are you sure you want to delete this location?")) {
        fetch(`/admin/locations/${id}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            window.location.reload();
        })
        .catch(error => console.error("Error deleting location:", error));
    }
}