<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Panel - Add Locations</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      text-align: center;
      padding: 20px;
      margin: 0;
    }

    nav {
      background-color: #333;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .nav-links {
      display: flex;
      gap: 10px;
    }

    nav a {
      color: white;
      text-decoration: none;
      padding: 12px 20px;
      font-size: 16px;
      font-weight: bold;
      transition: background 0.3s, transform 0.2s;
      border-radius: 5px;
    }

    nav a:hover {
      background: #575757;
      transform: scale(1.05);
    }

    nav a.selected {
      background-color: rgba(255, 255, 255, 0.3);
      color: #fff;
    }

    #profileButton {
      background-color: #4CAF50;
      color: white;
      padding: 6px 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      font-weight: bold;
      transition: background 0.3s, transform 0.2s;
    }

    #profileButton:hover {
      background-color: #45a049;
    }

    h2 {
      color: #333;
      margin-top: 20px;
    }

    form {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      display: inline-block;
      text-align: left;
      width: 300px;
    }

    label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
    }

    input {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    button {
      margin-top: 15px;
      padding: 10px;
      background: #4CAF50;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      width: 100%;
    }

    button:hover {
      background: #45a049;
    }

    .fetch-btn {
      background: #008CBA;
    }

    .fetch-btn:hover {
      background: #007bb5;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 10;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
      background-color: #fff;
      margin: 10% auto;
      padding: 20px;
      border-radius: 10px;
      width: 50%;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      position: relative;
    }

    .close {
      position: absolute;
      right: 15px;
      top: 10px;
      font-size: 28px;
      font-weight: bold;
      color: #aaa;
      cursor: pointer;
    }

    .close:hover {
      color: black;
    }

    table {
      width: 80%;
      margin: 20px auto;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    th, td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: center;
    }

    th {
      background: #333;
      color: white;
    }

    .delete-btn {
      padding: 5px 10px;
      background: #ff4444;
      color: white;
      border: none;
      cursor: pointer;
      border-radius: 4px;
      transition: background 0.3s;
    }

    .delete-btn:hover {
      background: #cc0000;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav>
    <div class="nav-links">
      <a href="admin" id="addLocationLink" onclick="selectTab(event, 'addLocationLink')">Add location</a>
      <a href="sensors.php" id="manageSensorsLink" onclick="selectTab(event, 'manageSensorsLink')">Manage Sensors</a>
      <a href="http://localhost/BreazyAQI/backend/public/Pdashboard.html" id="logoutLink">Logout</a>
    </div>
    <button id="profileButton">Profile</button>
  </nav>

  <h2>Add New AQI Location</h2>

  <form id="locationForm">
    <label for="location">Location Name:</label>
    <input type="text" id="location" required>

    <label for="lat">Latitude:</label>
    <input type="text" id="lat" required>

    <label for="lon">Longitude:</label>
    <input type="text" id="lon" required>
    

    <label for="aqi">AQI:</label>
    <input type="number" id="aqi" readonly required>

    <button type="button" class="fetch-btn" onclick="fetchAQI()">Fetch AQI</button>
    <button type="submit">Add Location</button>
  </form>

  <!-- Profile Modal -->
  <div id="profileModal" class="modal">
    <div class="modal-content">
      <span class="close" id="closeModal">&times;</span>
      <h2>Admin Profile</h2>
      <p><strong>Name:</strong> <span id="adminName"></span></p>
      <p><strong>Email:</strong> <span id="adminEmail"></span></p>
      <p><strong>Role:</strong> <span id="adminRole"></span></p>
    </div>
  </div>

  <h2>Added Locations</h2>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Location</th>
        <th>Latitude</th>
        <th>Longitude</th>
        <th>AQI</th>
        <th>Created Time</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="locationTable">
      <!-- Populated by JS -->
    </tbody>
  </table>

  <script>
    // Highlight correct nav tab based on current URL
    window.onload = () => {
      const path = window.location.pathname;

      document.querySelectorAll('nav a').forEach(link => link.classList.remove('selected'));

      if (path.includes("admin")) {
        document.getElementById("addLocationLink").classList.add("selected");
      } else if (path.includes("sensors.php")) {
        document.getElementById("manageSensorsLink").classList.add("selected");
      }

      fetchLocations(); // Load locations into table
    };

    // Tab click highlighting (no localStorage)
    function selectTab(event, linkId) {
      const links = document.querySelectorAll('nav a');
      links.forEach(link => link.classList.remove('selected'));

      if (linkId !== 'logoutLink') {
        document.getElementById(linkId).classList.add('selected');
      }
    }

    function fetchAQI() {
      const lat = document.getElementById("lat").value;
      const lon = document.getElementById("lon").value;

      if (!lat || !lon) {
        alert("Please enter Latitude and Longitude first.");
        return;
      }

      const apiKey = "4b98b49468bc4a44cc2df7ac4e0007163f430796";
      const url = `https://api.waqi.info/feed/geo:${lat};${lon}/?token=${apiKey}`;

      fetch(url)
        .then(response => response.json())
        .then(data => {
          if (data.status === "ok") {
            document.getElementById("aqi").value = data.data.aqi;
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

      const location = document.getElementById("location").value;
      const lat = document.getElementById("lat").value;
      const lon = document.getElementById("lon").value;
      const aqi = document.getElementById("aqi").value;

      fetch("http://localhost/BreazyAQI/backend/resources/php/api.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ location, lat, lon, aqi })
      })
      .then(response => response.json())
      .then(data => {
        alert(data.message);
        document.getElementById("locationForm").reset();
        fetchLocations();
      })
      .catch(err => {
        console.error("Add Location Error:", err);
        alert("Error adding location.");
      });
    });

    const profileModal = document.getElementById("profileModal");

    document.getElementById("profileButton").onclick = () => {
      fetch("http://localhost/BreazyAQI/backend/resources/php/getprofile.php")
        .then(response => response.json())
        .then(data => {
          if (data.error) {
            alert("Error: " + data.error);
            return;
          }

          document.getElementById("adminName").textContent = data.name;
          document.getElementById("adminEmail").textContent = data.email;
          document.getElementById("adminRole").textContent = data.role;

          profileModal.style.display = "block";
        })
        .catch(error => {
          console.error("Error fetching profile:", error);
          alert("Failed to load profile info.");
        });
    };

    document.getElementById("closeModal").onclick = () => {
      profileModal.style.display = "none";
    };

    window.onclick = (event) => {
      if (event.target === profileModal) {
        profileModal.style.display = "none";
      }
    };

    function fetchLocations() {
      fetch("http://localhost/BreazyAQI/backend/resources/php/getlocationsadmin.php")
        .then(response => response.json())
        .then(data => {
          data.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

          const tableBody = document.getElementById("locationTable");
          tableBody.innerHTML = "";

          data.forEach(location => {
            const row = document.createElement("tr");

            row.innerHTML = `
              <td>${location.id}</td>
              <td>${location.location}</td>
              <td>${location.latitude}</td>
              <td>${location.longitude}</td>
              <td>${location.aqi}</td>
              <td>${new Date(location.created_at).toLocaleString()}</td>
              <td><button class="delete-btn" onclick="deleteLocation(${location.id})">Delete</button></td>
            `;

            tableBody.appendChild(row);
          });
        })
        .catch(error => console.error("Error fetching locations:", error));
    }

    function deleteLocation(id) {
      if (confirm("Are you sure you want to delete this location?")) {
        fetch(`http://localhost/BreazyAQI/backend/resources/php/dellocationsadmin.php?id=${id}`, {
          method: "DELETE",
        })
        .then(response => response.json())
        .then(data => {
          alert(data.message);
          fetchLocations();
        })
        .catch(error => console.error("Error deleting location:", error));
      }
    }
  </script>
</body>
</html>
