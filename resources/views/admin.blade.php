@extends('layouts.admin')

@section('title', 'Admin Panel - Add Locations')

@section('styles')
    @vite(['resources/css/admin.css'])
@endsection

@section('content')
  <!-- Navbar -->
  <nav>
    <div class="nav-links">
      <a href="{{ route('admin.locations') }}" class="{{ request()->routeIs('admin.locations') ? 'selected' : '' }}">Add location</a>
      <a href="{{ route('admin.sensors') }}" class="{{ request()->routeIs('admin.sensors') ? 'selected' : '' }}">Manage Sensors</a>
      <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
      </form>
    </div>
    <button id="profileButton">Profile</button>
  </nav>

  <h2>Add New AQI Location</h2>

  <form id="locationForm">
    @csrf
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
      <p><strong>Name:</strong> <span id="adminName">{{ Auth::user()->name }}</span></p>
      <p><strong>Email:</strong> <span id="adminEmail">{{ Auth::user()->email }}</span></p>
      <p><strong>Role:</strong> <span id="adminRole">Administrator</span></p>
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
      @foreach($locations as $location)
        <tr>
          <td>{{ $location->id }}</td>
          <td>{{ $location->location }}</td>
          <td>{{ $location->latitude }}</td>
          <td>{{ $location->longitude }}</td>
          <td>{{ $location->aqi }}</td>
          <td>{{ $location->created_at->format('Y-m-d H:i') }}</td>
          <td>
            <button class="delete-btn" onclick="deleteLocation({{ $location->id }})">Delete</button>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endsection

@section('scripts')
  @vite(['resources/js/admin/locations.js'])
@endsection