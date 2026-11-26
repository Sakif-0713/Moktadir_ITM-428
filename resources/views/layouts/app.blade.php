<!DOCTYPE html>
<html lang="en" class="h-full bg-[#F8FAFC] scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PromptLab — AI Knowledge & RAG Evaluation Workbench')</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='8' fill='%232563EB'/%3E%3Cpath d='M17.5 6L8 18h7l-1.5 8L23 14h-7l1.5-8z' fill='none' stroke='%23ffffff' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        [x-cloak] { display: none !important; }
        @keyframes blobDrift {
            0%, 100% { transform: translate(0px, 0px) scale(1); }
            50% { transform: translate(24px, -18px) scale(1.06); }
        }
        .ambient-blob { animation: blobDrift 14s ease-in-out infinite; }
        .ambient-blob-delay { animation: blobDrift 18s ease-in-out infinite reverse; }
    </style>
</head>
<body class="min-h-full flex flex-col text-slate-800 antialiased relative overflow-x-hidden">
    <!-- Full-Site Ambient Pastel Mesh Background -->
    <div class="fixed inset-0 -z-10 pointer-events-none overflow-hidden bg-[#F8FAFC]">
        <div class="ambient-blob absolute -top-32 -left-24 w-[520px] h-[520px] rounded-full bg-blue-200/40 blur-3xl"></div>
        <div class="ambient-blob-delay absolute top-1/4 -right-24 w-[480px] h-[480px] rounded-full bg-pink-200/35 blur-3xl"></div>
        <div class="ambient-blob absolute bottom-0 left-1/3 w-[540px] h-[420px] rounded-full bg-indigo-100/45 blur-3xl"></div>
    </div>

    <!-- Clean White Glassmorphic SaaS Topbar -->
    <header class="sticky top-0 z-40 bg-white/85 backdrop-blur-md border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-bold text-lg text-slate-900 tracking-tight group">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-600 text-white text-sm font-bold shadow-sm group-hover:scale-105 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </span>
                    <span>PromptLab</span>
                </a>

                <nav class="hidden md:flex items-center gap-1 text-sm font-medium text-slate-600">
                    <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-lg hover:bg-slate-100/80 hover:text-slate-900 transition">Home</a>
                    <a href="{{ route('workspaces.index') }}" class="px-3 py-1.5 rounded-lg hover:bg-slate-100/80 hover:text-slate-900 transition">Workspaces</a>
                    @if(isset($workspace))
                        <a href="{{ route('evaluations.index', $workspace) }}" class="px-3 py-1.5 rounded-lg hover:bg-slate-100/80 hover:text-slate-900 transition">QA Metrics</a>
                        <a href="{{ route('refactor-logs.index', $workspace) }}" class="px-3 py-1.5 rounded-lg hover:bg-slate-100/80 hover:text-slate-900 transition">Refactor Log</a>
                    @endif
                </nav>
            </div>

            <div class="flex items-center gap-3">
                <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                    ZUMS ITM 427/428 Lab
                </span>

                @auth
                    <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
                        <span class="w-7 h-7 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center shadow-xs">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="text-xs font-medium text-slate-700 hidden md:inline">{{ auth()->user()->name }}</span>
                    </div>
                @else
                    <a href="{{ route('demo.guest-login.get') }}"
                       class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm hover:shadow-md transition-all">
                        Launch Live Demo
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if(session('success'))
            <div class="mb-5 rounded-xl bg-emerald-50/90 backdrop-blur border border-emerald-200 px-4 py-3 text-sm text-emerald-800 flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 rounded-xl bg-rose-50/90 backdrop-blur border border-rose-200 px-4 py-3 text-sm text-rose-800 shadow-sm">
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

    <footer class="bg-white/80 backdrop-blur border-t border-slate-200/80 py-5 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span><strong>PromptLab</strong> &mdash; AI-Assisted Knowledge &amp; Prompt/RAG Evaluation Workbench</span>
            <span>Laravel MVC &bull; Facade, Strategy &amp; Memento Patterns &bull; ITM 427/428</span>
        </div>
    </footer>
</body>
</html>
