
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">
    <!-- Orange header with menu -->
    <header class="bg-orange-500 text-white shadow" style="background-color: #f97316;">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="#" class="text-lg font-semibold">ATS Dashboard</a>
                    <nav class="hidden md:flex space-x-3">
                        <a href="/openings" class="px-3 py-2 rounded hover:bg-orange-600">Openings</a>
                        <a href="#" class="px-3 py-2 rounded hover:bg-orange-600">Candidates</a>
                        <a href="#" class="px-3 py-2 rounded hover:bg-orange-600">Interviews</a>
                        <a href="#" class="px-3 py-2 rounded hover:bg-orange-600">Reports</a>
                    </nav>
                </div>

                <div class="flex items-center space-x-3">
                    <span class="hidden sm:inline-block">Signed in as {{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-white text-orange-600 px-3 py-1 rounded hover:bg-gray-100" style="background-color: #f97316;">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="md:hidden bg-orange-500">
            <div class="px-4 pt-2 pb-4 space-y-1">
                <a href="/openings" class="block px-3 py-2 rounded hover:bg-orange-600">Openings</a>
                <a href="#" class="block px-3 py-2 rounded hover:bg-orange-600">Candidates</a>
                <a href="#" class="block px-3 py-2 rounded hover:bg-orange-600">Interviews</a>
                <a href="#" class="block px-3 py-2 rounded hover:bg-orange-600">Reports</a>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto mt-10">
        <div class="bg-white p-6 rounded-xl shadow">
            <h1 class="text-2xl font-bold mb-4">Welcome, {{ Auth::user()->name }} 👋</h1>

            <p class="mb-6">You’re now logged in to your ATS Dashboard.</p>

            <!-- logout button removed from here because header contains logout; kept as fallback -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    Logout
                </button>
            </form>
        </div>
    </main>
        <!-- Orange header with menu -->
    <footer class="bg-orange-500 text-white shadow" style="background-color: #f97316;">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
            </div>
        </div>
    </footer>
</body>
</html>
