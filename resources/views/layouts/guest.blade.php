<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SimpleBank Login</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    </head>
    <body style="margin: 0; font-family: 'Inter', sans-serif; background: #F9FAFB;">
        <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
            {{ $slot }}
        </div>
    </body>
</html>
