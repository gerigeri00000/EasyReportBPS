<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Submission Successful - {{ $activity->title }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 text-center">
                    <!-- Success Icon -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <h2 class="mt-2 text-2xl font-bold text-gray-900">
                        Submission Successful!
                    </h2>

                    <p class="mt-4 text-sm text-gray-600">
                        Thank you for submitting your data for:
                    </p>

                    <p class="mt-2 text-lg font-semibold text-gray-900">
                        {{ $activity->title }}
                    </p>

                    <p class="mt-2 text-sm text-gray-500">
                        {{ $activity->location }} • {{ $activity->activity_date->format('d M Y') }}
                    </p>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <p class="text-sm text-gray-500">
                            Your report has been recorded. If you need to submit another response, you may do so using the same link.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>