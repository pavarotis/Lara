<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Dynamic Site Name --}}
    <title>@yield('title', $siteConfig['site_name'] ?? 'My Store')</title>

    {{-- Dynamic Meta Description --}}
    @if(isset($siteConfig['seo']['meta_description']) && !empty($siteConfig['seo']['meta_description']))
        <meta name="description" content="{{ $siteConfig['seo']['meta_description'] }}">
    @endif

    {{-- Dynamic Theme CSS --}}
    <x-dynamic-theme />

    {{-- Google Analytics --}}
    @if(isset($siteConfig['seo']['google_analytics_id']) && !empty($siteConfig['seo']['google_analytics_id']))
        <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $siteConfig['seo']['google_analytics_id'] }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $siteConfig['seo']['google_analytics_id'] }}');
        </script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased" style="--color-primary: var(--color-primary, #3b82f6);">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        {{-- Header --}}
        <header class="bg-white dark:bg-gray-800 shadow" style="background-color: var(--color-primary);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-white">
                        {{ $siteConfig['site_name'] ?? 'My Store' }}
                    </h1>
                    <nav>
                        {{-- Navigation items --}}
                    </nav>
                </div>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="bg-gray-800 text-white mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Contact</h3>
                        <p>Email: {{ $siteConfig['contact_email'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Follow Us</h3>
                        <div class="flex space-x-4">
                            @if(isset($siteConfig['social']['facebook']) && !empty($siteConfig['social']['facebook']))
                                <a href="{{ $siteConfig['social']['facebook'] }}" target="_blank" class="hover:text-gray-300">Facebook</a>
                            @endif
                            @if(isset($siteConfig['social']['twitter']) && !empty($siteConfig['social']['twitter']))
                                <a href="{{ $siteConfig['social']['twitter'] }}" target="_blank" class="hover:text-gray-300">Twitter</a>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Currency</h3>
                        <p>{{ $siteConfig['currency'] ?? 'EUR' }}</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
