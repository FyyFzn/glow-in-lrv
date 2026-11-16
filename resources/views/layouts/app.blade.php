<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Glow-in</title>
    <link rel="stylesheet" href="{{ asset('assets/CSS/growincss.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/CSS/glowin-extra.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <input type="checkbox" id="menu-toggle" class="hidden-checkbox">

    <div class="layout">
        @include('partials.sidebar')

        <main class="main-content">
            {{ $slot }}
        </main>

        @include('partials.rightbar')
    </div>

    <label for="menu-toggle" id="overlay"></label>

    <script>
        // Close any open dropdown when clicking outside of it.
        window.onclick = function (event) {
            if (!event.target.closest('.post-dropdown-btn')) {
                var dropdowns = document.getElementsByClassName('post-dropdown-content');
                for (var i = 0; i < dropdowns.length; i++) {
                    dropdowns[i].classList.remove('show');
                }
            }
        }
    </script>
</body>
</html>
