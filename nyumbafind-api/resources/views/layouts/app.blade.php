<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NyumbaFind - Find Your Home in Kenya')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">

    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-xl font-bold text-emerald-600">
                NyumbaFind
            </a>
            <div class="flex gap-4 text-sm">
                <a href="{{ url('/') }}" class="hover:text-emerald-600">Search</a>
                <a href="#" class="hover:text-emerald-600">List Your Property</a>
                <a href="#" class="hover:text-emerald-600">Login</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    <footer class="border-t mt-12 py-6 text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} NyumbaFind. Built for Kenya.
    </footer>

</body>
</html>