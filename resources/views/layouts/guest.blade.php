<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PPD Kluang System')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>

<body class="bg-blue-50 min-h-screen flex flex-col items-center justify-center p-4">

    @yield('content')

    <div class="mt-8 text-center text-gray-500 text-xs">
        &copy; {{ date('Y') }} Pejabat Pendidikan Daerah Kluang. All rights reserved.
    </div>

</body>

</html>
