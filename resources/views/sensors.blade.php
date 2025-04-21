<?php require '../resources/php/db.php'; ?> <!-- Correct path to db.php -->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sensor Admin Panel</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
      background-color: #f9f9f9;
    }
    h1, h2 {
      color: #333;
    }
    form {
      margin-bottom: 30px;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px #ddd;
    }
    input, textarea, button {
      display: block;
      margin-bottom: 10px;
      padding: 8px;
      width: 300px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    button {
      background-color: #2196F3;
      color: white;
      border: none;
      cursor: pointer;
    }
    button:hover {
      background-color: #0b7dda;
    }
    table {
  width: 80%;
  margin: 20px auto;
  border-collapse: collapse;
  background: white;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  }

/* Table Header and Cell Styles */
  th, td {
  padding: 10px;
  border: 1px solid #ddd;
  text-align: center;
  }

/* Table Header Specific Styles */
     th {
   background: #333;
   color: white;
    }
    .btn {
      padding: 6px 10px;
      border-radius: 4px;
      text-decoration: none;
      color: white;
      margin: 2px;
      display: inline-block;
    }
    .on {
      background-color: #4CAF50;
    }
    .off {
      background-color: #f44336;
    }
    .delete {
      background-color: #777;
    }
    
    /* Navbar Styles */
    nav {
      background-color: #333;
      padding: 10px 0;
      display: flex;
      justify-content: center;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      margin-bottom: 30px;
    }

    nav a {
      color: white;
      text-decoration: none;
      padding: 12px 20px;
      margin: 0 10px;
      font-size: 16px;
      font-weight: bold;
      transition: background 0.3s, transform 0.2s;
      border-radius: 5px;
    }

    nav a:hover {
      background: #575757;
      transform: scale(1.05);
    }

    /* Selected button effect */
    nav a.selected {
      background-color: rgba(255, 255, 255, 0.3);
      color: #333;
    }
  </style>
</head>
<body>

  <!-- Navigation Bar -->
  <nav>
    <a href="admin" id="addLocationLink">Add location</a>
    <a href="sensors.php" id="manageSensorsLink" class="selected">Manage Sensors</a>
    <a href="http://localhost/BreazyAQI/backend/public/Pdashboard.html" id="logoutLink">Logout</a>
  </nav>

  <h1>Sensor Admin Panel</h1>

  <!-- Add Sensor Form -->
  <h2>Add New Sensor</h2>
  <form id="addSensorForm">
    <input type="text" name="name" placeholder="Sensor Name" required>
    <input type="text" name="latitude" placeholder="Latitude" required>
    <input type="text" name="longitude" placeholder="Longitude" required>
    <textarea name="description" placeholder="Description"></textarea>
    <button type="submit">Add Sensor</button>
  </form>

  <script>
  document.getElementById('addSensorForm').addEventListener('submit', function(e) {
    e.preventDefault(); // prevent full page reload

    const formData = new FormData(this);
    formData.append('action', 'add_sensor');

    fetch('../resources/php/adminsensors.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.text())
    .then(data => {
      alert(data); // ✅ Show success popup
      location.reload(); // 🔁 Refresh to show updated table
    })
    .catch(err => {
      alert("Error submitting form.");
      console.error(err);
    });
  });

  // Tab selection functionality
  function selectTab(event, linkId) {
    // Remove the selected class from all nav links
    const links = document.querySelectorAll('nav a');
    links.forEach(link => link.classList.remove('selected'));

    // Add the selected class to the clicked link
    const clickedLink = document.getElementById(linkId);
    clickedLink.classList.add('selected');

    // Save the selected tab to localStorage
    localStorage.setItem('selectedTab', linkId);
  }

  // Logout function
  function logout() {
    localStorage.removeItem('selectedTab'); // Clear the selected tab from localStorage
    window.location.href = "psignup"; // Redirect to the logout page
  }

  // Initialize tab selection
  document.addEventListener('DOMContentLoaded', function() {
    // Set up click handlers for all nav links
    document.getElementById('addLocationLink').addEventListener('click', function(e) {
      selectTab(e, 'addLocationLink');
    });
    document.getElementById('manageLocationsLink').addEventListener('click', function(e) {
      selectTab(e, 'manageLocationsLink');
    });
    document.getElementById('manageSensorsLink').addEventListener('click', function(e) {
      selectTab(e, 'manageSensorsLink');
    });
    document.getElementById('logoutLink').addEventListener('click', function(e) {
      e.preventDefault();
      logout();
    });

    // Check for previously selected tab
    const selectedTab = localStorage.getItem('selectedTab');
    if (selectedTab) {
      const tab = document.getElementById(selectedTab);
      if (tab) {
        tab.classList.add('selected');
      }
    } else {
      // Default to sensors tab since we're on this page
      document.getElementById('manageSensorsLink').classList.add('selected');
    }
  });
  </script>

  <!-- Display Sensors -->
  <h2>Sensor List</h2>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Latitude</th>
        <th>Longitude</th>
        <th>Description</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $result = $conn->query("SELECT * FROM sensors ORDER BY created_at ASC");
      while ($row = $result->fetch_assoc()) {
        $status = $row['is_active'] ? 'ON' : 'OFF';
        $toggleText = $row['is_active'] ? 'Turn OFF' : 'Turn ON';
        $toggleClass = $row['is_active'] ? 'off' : 'on';

        echo "<tr>
          <td>{$row['id']}</td>
          <td>{$row['name']}</td>
          <td>{$row['latitude']}</td>
          <td>{$row['longitude']}</td>
          <td>{$row['description']}</td>
          <td>$status</td>
          <td>{$row['created_at']}</td>
          <td>
            <a class='btn $toggleClass' href='../resources/php/adminsensors.php?toggle_id={$row['id']}&current={$row['is_active']}'>$toggleText</a>
            <a class='btn delete' href='../resources/php/adminsensors.php?delete_id={$row['id']}' onclick=\"return confirm('Delete this sensor?')\">Delete</a>
          </td>
        </tr>";
      }
      ?>
    </tbody>
  </table>

</body>
</html>