<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PromptLab — AI Knowledge &amp; Prompt/RAG Evaluation Workbench</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .hero-mesh {
            background:
                radial-gradient(at 15% 20%, rgba(191, 219, 254, 0.75) 0px, transparent 55%),
                radial-gradient(at 85% 25%, rgba(251, 207, 232, 0.65) 0px, transparent 55%),
                radial-gradient(at 50% 85%, rgba(224, 231, 255, 0.75) 0px, transparent 60%),
                #f8fafc;
        }
    </style>
</head>
<body class="bg-white text-slate-900 antialiased">

    <!-- Top Navigation (Tailgrids Style) -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-bold text-xl tracking-tight text-slate-900">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-blue-600 text-white shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </span>
                <span>PromptLab</span>
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                <a href="#what-is-it" class="text-blue-600 font-semibold">Home</a>
                <a href="#what-is-it" class="hover:text-slate-900 transition">Features</a>
                <a href="#how-it-works" class="hover:text-slate-900 transition">How It Works</a>
                <a href="#architecture" class="hover:text-slate-900 transition">Architecture</a>
                <a href="{{ route('workspaces.index') }}" class="hover:text-slate-900 transition">Workspaces</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ route('workspaces.index') }}" class="hidden sm:inline-block text-sm font-medium text-slate-600 hover:text-slate-900">
                    All Workspaces
                </a>
                <a href="{{ route('demo.guest-login.get') }}"
                   class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm shadow-blue-600/20 transition">
                    Get Started
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section with Soft Pastel Mesh & Embedded Dashboard Preview -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-16">
        <div class="hero-mesh rounded-3xl border border-slate-200/70 px-6 pt-16 pb-10 sm:px-12 lg:px-16 text-center overflow-hidden shadow-sm">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/80 border border-slate-200 text-xs font-semibold text-blue-700 mb-6 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                ZUMS ITM 427 / ITM 428 &bull; 1-Click Public Evaluator Demo Ready
            </span>

            <h1 class="max-w-3xl mx-auto text-4xl sm:text-5xl lg:text-[54px] font-extrabold tracking-tight text-slate-900 leading-[1.12]">
                All your RAG &amp; Prompt evaluation tools in one powerful platform.
            </h1>

            <p class="max-w-2xl mx-auto mt-5 text-base sm:text-lg text-slate-600 leading-relaxed">
                A simple, visual workbench for uploading knowledge documents, versioning AI prompts, and testing which prompt or retrieval strategy gives the most accurate, hallucination-free answers.
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('demo.guest-login.get') }}"
                   class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-md shadow-blue-600/20 transition">
                    Launch 1-Click Live Demo
                </a>
                <a href="#how-it-works"
                   class="px-6 py-3 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold border border-slate-200 shadow-xs transition">
                    Learn how it works
                </a>
            </div>

            <!-- Embedded TailAdmin-style Dashboard Preview Mockup -->
            <div class="mt-12 max-w-5xl mx-auto bg-white rounded-2xl border border-slate-200/90 shadow-2xl shadow-slate-900/10 overflow-hidden text-left">
                <div class="flex flex-col md:flex-row">
                    <!-- Mock Sidebar -->
                    <div class="hidden md:flex md:w-56 flex-col border-r border-slate-100 bg-white p-4 space-y-6">
                        <div class="flex items-center gap-2 font-bold text-slate-900 text-sm">
                            <span class="w-6 h-6 rounded-md bg-blue-600 text-white flex items-center justify-center text-xs">P</span>
                            <span>PromptLab Admin</span>
                        </div>
                        <div class="space-y-1 text-xs">
                            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 px-2 mb-2">Menu</div>
                            <div class="px-2.5 py-2 rounded-lg bg-blue-50 text-blue-700 font-semibold">Evaluation Dashboard</div>
                            <div class="px-2.5 py-2 rounded-lg text-slate-600">Knowledge Base &amp; Chunks</div>
                            <div class="px-2.5 py-2 rounded-lg text-slate-600">Prompt Memento Studio</div>
                            <div class="px-2.5 py-2 rounded-lg text-slate-600 flex items-center justify-between">
                                <span>Side-by-Side Runs</span>
                                <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-semibold">LIVE</span>
                            </div>
                            <div class="px-2.5 py-2 rounded-lg text-slate-600">QA &amp; Rubric Inspector</div>
                            <div class="px-2.5 py-2 rounded-lg text-slate-600">SE Refactor Log</div>
                        </div>
                    </div>

                    <!-- Mock Main Dashboard Content -->
                    <div class="flex-1 bg-[#F8FAFC] p-5 sm:p-6">
                        <div class="flex items-center justify-between mb-5">
                            <div class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-xs text-slate-400 w-64">
                                Search documents, prompts, runs...
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold border border-emerald-200">
                                    14/14 QA Tests Passing
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Card 1 -->
                            <div class="bg-white rounded-xl border border-slate-200/80 p-4">
                                <div class="text-xs text-slate-500 font-medium">Knowledge Chunks</div>
                                <div class="mt-2 flex items-baseline justify-between">
                                    <span class="text-2xl font-bold text-slate-900">2 Docs</span>
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">Boundary-Aware</span>
                                </div>
                            </div>
                            <!-- Card 2 -->
                            <div class="bg-white rounded-xl border border-slate-200/80 p-4">
                                <div class="text-xs text-slate-500 font-medium">Prompt Snapshots</div>
                                <div class="mt-2 flex items-baseline justify-between">
                                    <span class="text-2xl font-bold text-slate-900">v1 &rarr; v2</span>
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">Memento</span>
                                </div>
                            </div>
                            <!-- Card 3: Target Gauge -->
                            <div class="bg-white rounded-xl border border-slate-200/80 p-4 sm:row-span-2 flex flex-col justify-between">
                                <div>
                                    <div class="text-xs font-semibold text-slate-900">Grounding Score</div>
                                    <div class="text-[11px] text-slate-500">Strict Context Faithfulness</div>
                                </div>
                                <div class="my-4 text-center">
                                    <div class="inline-flex items-center justify-center w-28 h-28 rounded-full border-[10px] border-blue-600 border-b-slate-100">
                                        <div>
                                            <div class="text-xl font-extrabold text-slate-900">85.7%</div>
                                            <div class="text-[10px] font-semibold text-emerald-600">+3.2% vs v1</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-[11px] text-center text-slate-500">
                                    Passed regression threshold (65%) with deterministic fallback ready.
                                </div>
                            </div>

                            <!-- Card 4: Bar comparison -->
                            <div class="bg-white rounded-xl border border-slate-200/80 p-4 sm:col-span-2">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="text-xs font-semibold text-slate-900">Side-by-Side Prompt Evaluation</div>
                                    <span class="text-[11px] text-slate-400">Latency: 18 ms</span>
                                </div>
                                <div class="grid grid-cols-6 items-end gap-2 h-20 pt-2">
                                    <div class="bg-slate-100 rounded-t h-10"></div>
                                    <div class="bg-slate-100 rounded-t h-14"></div>
                                    <div class="bg-blue-600 rounded-t h-20"></div>
                                    <div class="bg-slate-100 rounded-t h-12"></div>
                                    <div class="bg-blue-400 rounded-t h-16"></div>
                                    <div class="bg-slate-100 rounded-t h-11"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- What is this platform? (Plain English for Anyone) -->
    <section id="what-is-it" class="py-16 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto text-center mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600">What Is PromptLab?</span>
                <h2 class="mt-2 text-3xl font-extrabold text-slate-900">Understand AI Quality Without Guesswork</h2>
                <p class="mt-3 text-slate-600 text-sm sm:text-base">
                    When building AI assistants that read your documents (known as <strong>RAG — Retrieval-Augmented Generation</strong>), small changes to how you split text or write instructions can drastically change answer quality. PromptLab lets anyone test, compare, and measure those changes scientifically.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-2xl border border-slate-200/80 bg-slate-50/50">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold mb-4">1</div>
                    <h3 class="text-base font-bold text-slate-900">Organize Knowledge Bases</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        Paste or upload any handbook, policy, or guide. The system automatically breaks it into searchable chunks using selectable splitting strategies.
                    </p>
                </div>
                <div class="p-6 rounded-2xl border border-slate-200/80 bg-slate-50/50">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold mb-4">2</div>
                    <h3 class="text-base font-bold text-slate-900">Version Prompts Safely</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        Write reusable AI prompt templates with dynamic <code class="px-1 py-0.5 rounded bg-amber-100 text-amber-800 text-xs">@{{variables}}</code>. Every edit saves an immutable snapshot you can roll back to anytime.
                    </p>
                </div>
                <div class="p-6 rounded-2xl border border-slate-200/80 bg-slate-50/50">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold mb-4">3</div>
                    <h3 class="text-base font-bold text-slate-900">Compare Side-by-Side</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        Run two prompt versions on the exact same question and inspect grounding scores, completeness, latency (ms), and Pass/Fail regression status.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works in 4 Steps -->
    <section id="how-it-works" class="py-16 bg-[#F8FAFC] border-t border-slate-200/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Simple Workflow</span>
                <h2 class="mt-2 text-3xl font-extrabold text-slate-900">How Anyone Can Use This Site in 60 Seconds</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-xs font-bold text-blue-600">STEP 01</span>
                    <h3 class="mt-1 font-bold text-slate-900">Click Live Demo</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        No account setup needed. Click <strong>Launch 1-Click Live Demo</strong> to enter a pre-loaded workspace with real documents and test runs.
                    </p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-xs font-bold text-blue-600">STEP 02</span>
                    <h3 class="mt-1 font-bold text-slate-900">Inspect or Add Docs</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Open the <strong>Documents &amp; Chunks</strong> tab to paste text or upload a file, then switch between Fixed-Size and Paragraph-Boundary chunking.
                    </p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-xs font-bold text-blue-600">STEP 03</span>
                    <h3 class="mt-1 font-bold text-slate-900">Run Side-by-Side Test</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Pick Prompt v1 and v2, choose a Retrieval &amp; Rubric Strategy, enter a question, and click <strong>Run Evaluation</strong>.
                    </p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-xs font-bold text-blue-600">STEP 04</span>
                    <h3 class="mt-1 font-bold text-slate-900">Inspect Scores &amp; Logs</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Review Grounding %, Completeness %, Latency, and Pass/Fail badges—even offline via our deterministic fallback engine.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Architecture & Design Patterns (ITM 427/428) -->
    <section id="architecture" class="py-16 bg-white border-t border-slate-200/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Software Engineering Architecture</span>
                    <h2 class="mt-2 text-3xl font-extrabold text-slate-900">Explicit Design Patterns Under the Hood</h2>
                </div>
                <a href="{{ route('demo.guest-login.get') }}" class="text-sm font-semibold text-blue-600 hover:underline">
                    Explore Live Workspace &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-2xl border border-slate-200 bg-white shadow-xs">
                    <span class="px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-mono font-semibold">Facade Pattern</span>
                    <h3 class="mt-3 text-lg font-bold text-slate-900">EvaluationFacade</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        Unifies chunking, top-k retrieval, variable interpolation, Gemini API calls, deterministic offline fallback, and rubric scoring behind one clean interface.
                    </p>
                </div>
                <div class="p-6 rounded-2xl border border-slate-200 bg-white shadow-xs">
                    <span class="px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 text-xs font-mono font-semibold">Strategy Pattern</span>
                    <h3 class="mt-3 text-lg font-bold text-slate-900">Interchangeable Algorithms</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        Swap Chunking (Fixed vs. Boundary), Retrieval (Lexical vs. Hybrid), and Evaluation Rubrics (Grounding vs. Completeness) at runtime per test run.
                    </p>
                </div>
                <div class="p-6 rounded-2xl border border-slate-200 bg-white shadow-xs">
                    <span class="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 text-xs font-mono font-semibold">Memento Pattern</span>
                    <h3 class="mt-3 text-lg font-bold text-slate-900">Immutable Snapshots</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        Every prompt and document edit creates an immutable version snapshot via <code class="text-xs">MementoService</code>, supporting one-click rollback and regression tracking.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-slate-50 border-t border-slate-200 py-8 text-center text-xs text-slate-500">
        PromptLab &bull; AI-Assisted Knowledge &amp; Prompt/RAG Evaluation Workbench &bull; ZUMS ITM 427 / ITM 428 Lab
    </footer>

</body>
</html>
