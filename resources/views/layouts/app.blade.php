<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My Laravel App')</title>
    
    <!-- Laravel Vite (for CSS/JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
    /* === Theme Variables === */
    :root {
        --primary-color: #1671f9ff;
        --primary-hover: #0c47eaff;
        --light-bg: #fff7ed;
        --body-bg: #fdfdfd;
        --text-dark: #333;
        --border-color: #ddd;
        --input-border: #ccc;
        --warning-color: #1671f9ff;
        --danger-color: #0c47eaff;
        --white: #ffffff;
    }

    body {
        background-color: var(--body-bg);
    }

    h1 {
        color: var(--primary-color);
        font-weight: 700;
        text-transform: uppercase;
        border-bottom: 2px solid var(--primary-color);
        padding-bottom: .5rem;
        margin-bottom: 1.5rem;
    }

    .container {
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: 2rem 3rem;
        background-color: var(--white);
        border: none;
        border-radius: 0;
        box-shadow: none;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.5rem 2rem;
    }

    label {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.3rem;
        display: block;
    }

    .form-control {
        width: 100%;
        border-radius: 0;
        border: 1px solid var(--input-border);
        padding: 0.6rem;
        font-size: 1rem;
    }

    .form-control:focus, select:focus, textarea:focus {
        border-color: var(--primary-color);
        box-shadow: none;
        outline: none;
    }

    .btn {
        border-radius: 0;
        border-width: 1px;
        font-weight: 500;
        text-transform: uppercase;
        padding: 0.5rem 1.5rem;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: var(--white);
    }

    .btn-primary:hover {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
    }

    .btn-secondary, .btn-success {
        background-color: var(--white);
        color: var(--primary-color);
        border: 1px solid var(--primary-color);
    }

    .btn-secondary:hover, .btn-success:hover {
        background-color: var(--primary-color);
        color: var(--white);
        border-color: var(--primary-color);
    }

    .btn-warning {
        background-color: var(--white);
        color: var(--warning-color);
        border: 1px solid var(--warning-color);
    }

    .btn-warning:hover {
        background-color: var(--warning-color);
        color: var(--white);
    }

    .btn-danger {
        background-color: var(--white);
        color: var(--danger-color);
        border: 1px solid var(--danger-color);
    }

    .btn-danger:hover {
        background-color: var(--danger-color);
        color: var(--white);
    }

    .form-actions {
        margin-top: 2rem;
        display: flex;
        gap: 1rem;
    }

    .table {
        width: 100%;
        border: 1px solid var(--border-color);
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .table thead {
        background-color: var(--primary-color);
        color: var(--white);
        text-transform: uppercase;
    }

    .table th, .table td {
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background-color: var(--light-bg);
    }

    .pagination .active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: var(--white);
    }

    .pagination .page-link {
        border-radius: 0 !important;
    }

    .gap-2 > * {
        flex: 1;
    }
    .bg-primary{
        background-color: var(--primary-color);
    }
    .navbar-nav {
        padding:20px 10px;
    }
    .header-style{
        background-color: var(--primary-color); 
        color: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    @media (max-width: 768px) {
        .container {
            padding: 1rem;
        }
    }
</style>

</head>
<body class="bg-gray-100 text-gray-900">

<header class="header-style py-4 mb-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center space-x-6">
                <a href="#" class="text-xl font-bold uppercase tracking-wide hover:text-white">
                    ATS Dashboard
                </a>

                <nav class="hidden md:flex space-x-4 navbar-nav">
                    <a href="/openings" class="px-3 py-2 rounded text-white hover:bg-[#ea580c] transition-colors duration-200">Openings</a>
                    <a href="/candidates" class="px-3 py-2 rounded text-white hover:bg-[#ea580c] transition-colors duration-200">Candidates</a>
                    <a href="#" class="px-3 py-2 rounded text-white hover:bg-[#ea580c] transition-colors duration-200">Interviews</a>
                    <a href="#" class="px-3 py-2 rounded text-white hover:bg-[#ea580c] transition-colors duration-200">Reports</a>
                </nav>
            </div>

            <div class="flex items-center space-x-3">
                <span class="hidden sm:inline-block text-sm font-medium">
                    Signed in as <strong>{{ Auth::user()->name }}</strong>
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button 
                        type="submit" 
                        class="bg-primary px-3 py-1.5"
                       >
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="md:hidden bg-[#f97316] border-t border-[#ea580c] navbar-nav">
        <div class="px-4 pt-2 pb-4 space-y-1">
            <a href="/openings" class="block px-3 py-2 rounded text-white hover:bg-[#ea580c] transition-colors duration-200">Openings</a>
            <a href="/candidates" class="block px-3 py-2 rounded text-white hover:bg-[#ea580c] transition-colors duration-200">Candidates</a>
            <a href="#" class="block px-3 py-2 rounded text-white hover:bg-[#ea580c] transition-colors duration-200">Interviews</a>
            <a href="#" class="block px-3 py-2 rounded text-white hover:bg-[#ea580c] transition-colors duration-200">Reports</a>
        </div>
    </div>
</header>


    <!-- Main Content -->
    <main class="container mx-auto mt-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white text-center py-4 mt-10">
        &copy; {{ date('Y') }} MyApp. All rights reserved.
    </footer>

</body>
</html>
