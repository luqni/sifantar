<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIFANTAR')</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
      @theme {
        --font-sans: "Inter", ui-sans-serif, system-ui, sans-serif;
        
        --color-primary-green: #8EC641;
        --color-primary-orange: #FF8A3D;
        --color-bg-base: #FFFAF0;
        --color-text-dark: #1A1C1E;
        
        --shadow-sifantar: 0 10px 30px -10px rgba(142, 198, 65, 0.2);
        --shadow-orange-glow: 0 10px 30px -10px rgba(255, 138, 61, 0.2);
      }

      @layer base {
        body {
          @apply bg-white text-text-dark font-sans antialiased;
        }
      }

      .bottom-nav-item {
        @apply flex flex-col items-center gap-1 transition-colors text-gray-400;
      }

      .bottom-nav-item.active {
        @apply text-primary-green;
      }

      .card-sifantar {
        @apply bg-white rounded-3xl p-6 shadow-xl shadow-gray-100/50 border border-gray-50;
      }

      .btn-primary {
        @apply bg-primary-green text-white rounded-full py-4 px-6 flex items-center justify-between shadow-lg shadow-green-100 active:scale-95 transition-all;
      }

      .input-field {
        @apply bg-white rounded-2xl p-4 flex items-center gap-3 shadow-sm border border-orange-100 focus-within:border-primary-orange transition-colors;
      }

      .scrollbar-hide::-webkit-scrollbar {
        display: none;
      }

      .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
      }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @stack('styles')
</head>
<body class="bg-white min-h-screen pb-20">
    @yield('content')
    
    @if(!isset($hideNav) || !$hideNav)
        @include('partials.bottom-nav')
    @endif

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
