<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-shield-alt me-2"></i>Admin Panel
        </a>
        
        <div class="d-flex align-items-center">
            <span class="text-light me-3 d-none d-md-inline">
                <i class="fas fa-user-circle me-1"></i>
                {{ Auth::user()->name }}
            </span>
            
            <button id="addLocationBtn" class="btn btn-success me-2">
                <i class="fas fa-plus me-1"></i> Add Location
            </button>
            
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary me-2">
                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
            </a>
            
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>