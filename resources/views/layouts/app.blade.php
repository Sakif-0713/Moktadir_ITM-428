<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RAG Evaluation Workbench — ITM 427/428')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-full flex flex-col text-slate-800 antialiased">
    <header class="bg-slate-900 text-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('workspaces.index') }}" class="flex items-center space-x-2 font-bold text-lg tracking-tight">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-500 text-white text-sm font-mono">RAG</span>
                    <span>Prompt &amp; RAG Workbench</span>
                </a>
                <span class="hidden sm:inline-block text-xs px-2 py-0.5 rounded bg-slate-800 text-indigo-300 border border-slate-700">
                    ZUMS ITM 427/428 Lab
                </span>
            </div>

            <div class="flex items-center space-x-3">
                @auth
                    <span class="text-xs text-slate-300 hidden md:inline">
                        Signed in as <strong class="text-white">{{ auth()->user()->name }}</strong> ({{ auth()->user()->role }})
                    </span>
                @else
                    <a href="{{ route('demo.guest-login.get') }}"
                       class="px-3 py-1.5 rounded-md bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold transition">
                        1-Click Evaluator Demo
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if(session('success'))
            <div class="mb-5 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800 flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 rounded-lg bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-800">
                <p class="font-semibold mb-1">Please fix the following errors:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500">
        AI-Assisted Knowledge &amp; Prompt/RAG Evaluation Workbench &bull; Laravel MVC &bull; Facade, Strategy &amp; Memento Patterns
    </footer>
</body>
</html>
