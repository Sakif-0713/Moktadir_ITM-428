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
                radial-gradient(at 15% 20%, rgba(191, 219, 254, 0.78) 0px, transparent 55%),
                radial-gradient(at 85% 25%, rgba(251, 207, 232, 0.68) 0px, transparent 55%),
                radial-gradient(at 50% 85%, rgba(224, 231, 255, 0.78) 0px, transparent 60%),
                #f8fafc;
        }
        .perspective-stage {
            perspective: 1400px;
        }
        .preserve-3d {
            transform-style: preserve-3d;
        }
        .hero-3d-card {
            transform: rotateX(10deg) scale(0.97);
            transition: transform 0.15s ease-out, box-shadow 0.3s ease;
            will-change: transform;
        }
        .float-3d-badge {
            transition: transform 0.2s ease-out;
            will-change: transform;
        }
        .scroll-reveal {
            opacity: 0;
            transform: perspective(900px) translateY(32px) rotateX(8deg);
            transition: opacity 0.65s cubic-bezier(0.16, 1, 0.3, 1), transform 0.65s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .scroll-reveal.is-visible {
            opacity: 1;
            transform: perspective(900px) translateY(0) rotateX(0deg);
        }
        .tilt-card {
            transition: transform 0.25s ease-out, box-shadow 0.25s ease-out;
            transform-style: preserve-3d;
        }
        .stage-panel {
            transition: opacity 0.45s ease, transform 0.55s cubic-bezier(0.16, 1, 0.3, 1);
        }
    </style>
</head>
<body class="bg-white text-slate-900 antialiased overflow-x-hidden">

    <!-- Top Navigation (Tailgrids Style) -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-bold text-xl tracking-tight text-slate-900">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-blue-600 text-white shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </span>
                <span>PromptLab</span>
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                <a href="#top" class="text-blue-600 font-semibold">Home</a>
                <a href="#interactive-3d" class="hover:text-slate-900 transition">3D Pipeline</a>
                <a href="#what-is-it" class="hover:text-slate-900 transition">Features</a>
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

    <!-- Hero Section with Soft Pastel Mesh & 3D Scroll-Tilting Dashboard -->
    <section id="top" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-16">
        <div class="hero-mesh rounded-3xl border border-slate-200/70 px-6 pt-16 pb-12 sm:px-12 lg:px-16 text-center overflow-hidden shadow-sm relative">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/85 border border-slate-200 text-xs font-semibold text-blue-700 mb-6 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
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
                <a href="#interactive-3d"
                   class="px-6 py-3 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold border border-slate-200 shadow-xs transition">
                    Scroll to Explore 3D Pipeline &darr;
                </a>
            </div>

            <!-- 3D Perspective Dashboard Stage -->
            <div class="perspective-stage mt-12 max-w-5xl mx-auto relative">
                <!-- Floating 3D Badges that parallax on scroll -->
                <div id="badge-left" class="float-3d-badge hidden lg:flex items-center gap-2 absolute -left-6 top-12 z-20 bg-white/95 backdrop-blur px-3.5 py-2.5 rounded-xl border border-slate-200 shadow-lg text-xs font-semibold text-slate-800">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    <span>Memento Snapshot v2</span>
                </div>
                <div id="badge-right" class="float-3d-badge hidden lg:flex items-center gap-2 absolute -right-6 bottom-16 z-20 bg-white/95 backdrop-blur px-3.5 py-2.5 rounded-xl border border-emerald-200 shadow-lg text-xs font-semibold text-emerald-700">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span>85.7% Grounding PASS</span>
                </div>

                <div id="hero-3d-card" class="hero-3d-card preserve-3d bg-white rounded-2xl border border-slate-200/90 shadow-2xl shadow-slate-900/10 overflow-hidden text-left">
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
                                <div class="bg-white rounded-xl border border-slate-200/80 p-4">
                                    <div class="text-xs text-slate-500 font-medium">Knowledge Chunks</div>
                                    <div class="mt-2 flex items-baseline justify-between">
                                        <span class="text-2xl font-bold text-slate-900">2 Docs</span>
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">Boundary-Aware</span>
                                    </div>
                                </div>
                                <div class="bg-white rounded-xl border border-slate-200/80 p-4">
                                    <div class="text-xs text-slate-500 font-medium">Prompt Snapshots</div>
                                    <div class="mt-2 flex items-baseline justify-between">
                                        <span class="text-2xl font-bold text-slate-900">v1 &rarr; v2</span>
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">Memento</span>
                                    </div>
                                </div>
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
        </div>
    </section>

    <!-- EXCITING 3D SCROLL-DRIVEN PIPELINE SHOWCASE -->
    <section id="interactive-3d" class="py-16 bg-[#F8FAFC] border-t border-slate-200/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Interactive 3D Scroll Walkthrough</span>
                <h2 class="mt-2 text-3xl sm:text-4xl font-extrabold text-slate-900">Watch How PromptLab Evaluates AI Step by Step</h2>
                <p class="mt-3 text-sm sm:text-base text-slate-600">
                    Scroll down to rotate through the 4 live stages of our RAG evaluation pipeline.
                </p>
            </div>

            <div id="scroll-story-container" class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start relative">
                <!-- Left Column: Scroll-Triggered Story Steps -->
                <div class="lg:col-span-5 space-y-6 lg:py-8">
                    <div data-step="0" class="story-step p-6 rounded-2xl border-2 border-blue-600 bg-white shadow-md transition-all duration-300 cursor-pointer">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Stage 01 &bull; Chunking Strategy</span>
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">1</span>
                        </div>
                        <h3 class="mt-2 text-lg font-bold text-slate-900">Ingest &amp; Split Documents</h3>
                        <p class="mt-1.5 text-sm text-slate-600 leading-relaxed">
                            Raw handbooks or policies are split into clean, token-counted chunks using either <strong>Fixed-Size</strong> or <strong>Paragraph-Boundary</strong> strategies.
                        </p>
                    </div>

                    <div data-step="1" class="story-step p-6 rounded-2xl border border-slate-200 bg-white/70 hover:bg-white transition-all duration-300 cursor-pointer">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Stage 02 &bull; Retrieval Strategy</span>
                            <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center">2</span>
                        </div>
                        <h3 class="mt-2 text-lg font-bold text-slate-900">Rank Top-K Relevant Chunks</h3>
                        <p class="mt-1.5 text-sm text-slate-600 leading-relaxed">
                            When a query arrives, <strong>Lexical Overlap</strong> or <strong>Hybrid N-Gram Similarity</strong> scores every chunk and selects the top matches.
                        </p>
                    </div>

                    <div data-step="2" class="story-step p-6 rounded-2xl border border-slate-200 bg-white/70 hover:bg-white transition-all duration-300 cursor-pointer">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-purple-600 uppercase tracking-wider">Stage 03 &bull; Memento Pattern</span>
                            <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center">3</span>
                        </div>
                        <h3 class="mt-2 text-lg font-bold text-slate-900">Inject Prompt Versions (v1 vs v2)</h3>
                        <p class="mt-1.5 text-sm text-slate-600 leading-relaxed">
                            Immutable <strong>PromptVersion</strong> snapshots inject <code class="px-1 py-0.5 rounded bg-amber-100 text-amber-800 text-xs">@{{context}}</code> and <code class="px-1 py-0.5 rounded bg-amber-100 text-amber-800 text-xs">@{{query}}</code> safely, even if variables are missing.
                        </p>
                    </div>

                    <div data-step="3" class="story-step p-6 rounded-2xl border border-slate-200 bg-white/70 hover:bg-white transition-all duration-300 cursor-pointer">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Stage 04 &bull; EvaluationFacade</span>
                            <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center">4</span>
                        </div>
                        <h3 class="mt-2 text-lg font-bold text-slate-900">Side-by-Side Score &amp; Fallback</h3>
                        <p class="mt-1.5 text-sm text-slate-600 leading-relaxed">
                            Outputs are compared side-by-side and graded on <strong>Grounding %</strong>, <strong>Completeness %</strong>, and <strong>Latency</strong>—with zero-crash deterministic fallback.
                        </p>
                    </div>
                </div>

                <!-- Right Column: Sticky 3D Transforming Stage -->
                <div class="lg:col-span-7 lg:sticky lg:top-24 perspective-stage">
                    <div id="stage-3d-box"
                         class="preserve-3d hero-mesh rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-2xl shadow-blue-900/10 min-h-[420px] flex flex-col justify-between transition-transform duration-500 ease-out">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-400"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                                <span id="stage-label" class="ml-2 text-xs font-mono font-semibold text-slate-600 bg-white/80 px-2.5 py-1 rounded-md border border-slate-200">
                                    Stage 01 / 04 — Chunking Engine
                                </span>
                            </div>
                            <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full border border-blue-200">
                                3D Live View
                            </span>
                        </div>

                        <!-- Dynamic 3D Stage Panels -->
                        <div class="relative flex-1 flex items-center">
                            <!-- Panel 0: Chunking -->
                            <div id="panel-0" class="stage-panel w-full space-y-3">
                                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
                                    <div class="text-xs font-semibold text-slate-500 mb-1">Source Document: Software Testing &amp; QA Handbook</div>
                                    <div class="text-xs text-slate-700 font-mono bg-slate-50 p-2.5 rounded-lg">
                                        # Boundary Value Analysis... Defects cluster at edges...
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-3 pt-2">
                                    <div class="bg-white rounded-xl p-3 border-2 border-blue-500 shadow-md transform -translate-y-1">
                                        <div class="text-[11px] font-bold text-blue-600">Chunk #0</div>
                                        <div class="text-[11px] text-slate-600 mt-1">BVA edge cases &amp; zero-length inputs (28 tokens)</div>
                                    </div>
                                    <div class="bg-white rounded-xl p-3 border border-indigo-300 shadow-md transform translate-y-1">
                                        <div class="text-[11px] font-bold text-indigo-600">Chunk #1</div>
                                        <div class="text-[11px] text-slate-600 mt-1">RAG pipeline boundary guards (24 tokens)</div>
                                    </div>
                                    <div class="bg-white rounded-xl p-3 border border-purple-300 shadow-md transform -translate-y-1">
                                        <div class="text-[11px] font-bold text-purple-600">Chunk #2</div>
                                        <div class="text-[11px] text-slate-600 mt-1">Regression &amp; smoke suites (22 tokens)</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Panel 1: Retrieval -->
                            <div id="panel-1" class="stage-panel hidden w-full space-y-3">
                                <div class="bg-white rounded-xl p-3.5 border border-slate-200 shadow-sm flex items-center justify-between">
                                    <span class="text-xs font-semibold text-slate-700">Query: "How does BVA handle missing variables?"</span>
                                    <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-indigo-50 text-indigo-700">HybridSimilarity</span>
                                </div>
                                <div class="space-y-2.5">
                                    <div class="bg-white rounded-xl p-3.5 border-2 border-emerald-500 shadow-md flex items-center justify-between">
                                        <div>
                                            <div class="text-xs font-bold text-slate-900">Chunk #1 &mdash; RAG Boundary Guards</div>
                                            <div class="text-[11px] text-slate-500">Matched: "boundary", "missing", "variables"</div>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold">94.2%</span>
                                    </div>
                                    <div class="bg-white rounded-xl p-3.5 border border-slate-200 shadow-sm flex items-center justify-between opacity-85">
                                        <div>
                                            <div class="text-xs font-bold text-slate-900">Chunk #0 &mdash; BVA Edge Cases</div>
                                            <div class="text-[11px] text-slate-500">Matched: "BVA", "edge"</div>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-bold">78.5%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Panel 2: Prompt Memento -->
                            <div id="panel-2" class="stage-panel hidden w-full grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm opacity-80">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-bold text-slate-700">Memento v1 (Baseline)</span>
                                        <span class="text-[10px] px-2 py-0.5 rounded bg-slate-100 text-slate-600">Snapshot</span>
                                    </div>
                                    <pre class="text-[11px] font-mono text-slate-600 whitespace-pre-wrap bg-slate-50 p-2.5 rounded-lg">Answer based on context:
@{{context}}
Q: @{{query}}</pre>
                                </div>
                                <div class="bg-white rounded-xl p-4 border-2 border-blue-600 shadow-lg">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-bold text-blue-700">Memento v2 (Strict)</span>
                                        <span class="text-[10px] px-2 py-0.5 rounded bg-blue-100 text-blue-700 font-semibold">Active</span>
                                    </div>
                                    <pre class="text-[11px] font-mono text-slate-700 whitespace-pre-wrap bg-blue-50/40 p-2.5 rounded-lg">Use ONLY context. Tone: @{{tone}}.
@{{context}}
Q: @{{query}}</pre>
                                </div>
                            </div>

                            <!-- Panel 3: Evaluation Score -->
                            <div id="panel-3" class="stage-panel hidden w-full grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-slate-700">Prompt v1 Result</span>
                                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[11px] font-bold">82.5% PASS</span>
                                    </div>
                                    <div class="mt-3 grid grid-cols-2 gap-2 text-center text-xs">
                                        <div class="p-2 rounded bg-slate-50">Grounding: <strong>84.5%</strong></div>
                                        <div class="p-2 rounded bg-slate-50">Latency: <strong>19 ms</strong></div>
                                    </div>
                                </div>
                                <div class="bg-white rounded-xl p-4 border-2 border-emerald-500 shadow-lg">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-emerald-700">Prompt v2 Result</span>
                                        <span class="px-2 py-0.5 rounded bg-emerald-500 text-white text-[11px] font-bold">85.7% WINNER</span>
                                    </div>
                                    <div class="mt-3 grid grid-cols-2 gap-2 text-center text-xs">
                                        <div class="p-2 rounded bg-emerald-50">Grounding: <strong>88.0%</strong></div>
                                        <div class="p-2 rounded bg-emerald-50">Latency: <strong>18 ms</strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Dots -->
                        <div class="mt-6 pt-4 border-t border-slate-200/70 flex items-center justify-between text-xs text-slate-500">
                            <div class="flex items-center gap-2">
                                <span data-dot="0" class="stage-dot w-6 h-2 rounded-full bg-blue-600 transition-all"></span>
                                <span data-dot="1" class="stage-dot w-2 h-2 rounded-full bg-slate-300 transition-all"></span>
                                <span data-dot="2" class="stage-dot w-2 h-2 rounded-full bg-slate-300 transition-all"></span>
                                <span data-dot="3" class="stage-dot w-2 h-2 rounded-full bg-slate-300 transition-all"></span>
                            </div>
                            <a href="{{ route('demo.guest-login.get') }}" class="font-semibold text-blue-600 hover:underline">
                                Try this live now &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- What is this platform? -->
    <section id="what-is-it" class="py-16 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto text-center mb-12 scroll-reveal">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600">What Is PromptLab?</span>
                <h2 class="mt-2 text-3xl font-extrabold text-slate-900">Understand AI Quality Without Guesswork</h2>
                <p class="mt-3 text-slate-600 text-sm sm:text-base">
                    When building AI assistants that read your documents (known as <strong>RAG — Retrieval-Augmented Generation</strong>), small changes to how you split text or write instructions can drastically change answer quality. PromptLab lets anyone test, compare, and measure those changes scientifically.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="scroll-reveal tilt-card p-6 rounded-2xl border border-slate-200/80 bg-slate-50/50 hover:shadow-lg">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold mb-4">1</div>
                    <h3 class="text-base font-bold text-slate-900">Organize Knowledge Bases</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        Paste or upload any handbook, policy, or guide. The system automatically breaks it into searchable chunks using selectable splitting strategies.
                    </p>
                </div>
                <div class="scroll-reveal tilt-card p-6 rounded-2xl border border-slate-200/80 bg-slate-50/50 hover:shadow-lg">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold mb-4">2</div>
                    <h3 class="text-base font-bold text-slate-900">Version Prompts Safely</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        Write reusable AI prompt templates with dynamic <code class="px-1 py-0.5 rounded bg-amber-100 text-amber-800 text-xs">@{{variables}}</code>. Every edit saves an immutable snapshot you can roll back to anytime.
                    </p>
                </div>
                <div class="scroll-reveal tilt-card p-6 rounded-2xl border border-slate-200/80 bg-slate-50/50 hover:shadow-lg">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold mb-4">3</div>
                    <h3 class="text-base font-bold text-slate-900">Compare Side-by-Side</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        Run two prompt versions on the exact same question and inspect grounding scores, completeness, latency (ms), and Pass/Fail regression status.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Architecture & Design Patterns (ITM 427/428) -->
    <section id="architecture" class="py-16 bg-[#F8FAFC] border-t border-slate-200/70">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4 scroll-reveal">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Software Engineering Architecture</span>
                    <h2 class="mt-2 text-3xl font-extrabold text-slate-900">Explicit Design Patterns Under the Hood</h2>
                </div>
                <a href="{{ route('demo.guest-login.get') }}" class="text-sm font-semibold text-blue-600 hover:underline">
                    Explore Live Workspace &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="scroll-reveal tilt-card p-6 rounded-2xl border border-slate-200 bg-white shadow-xs hover:shadow-lg">
                    <span class="px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-mono font-semibold">Facade Pattern</span>
                    <h3 class="mt-3 text-lg font-bold text-slate-900">EvaluationFacade</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        Unifies chunking, top-k retrieval, variable interpolation, Gemini API calls, deterministic offline fallback, and rubric scoring behind one clean interface.
                    </p>
                </div>
                <div class="scroll-reveal tilt-card p-6 rounded-2xl border border-slate-200 bg-white shadow-xs hover:shadow-lg">
                    <span class="px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 text-xs font-mono font-semibold">Strategy Pattern</span>
                    <h3 class="mt-3 text-lg font-bold text-slate-900">Interchangeable Algorithms</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        Swap Chunking (Fixed vs. Boundary), Retrieval (Lexical vs. Hybrid), and Evaluation Rubrics (Grounding vs. Completeness) at runtime per test run.
                    </p>
                </div>
                <div class="scroll-reveal tilt-card p-6 rounded-2xl border border-slate-200 bg-white shadow-xs hover:shadow-lg">
                    <span class="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 text-xs font-mono font-semibold">Memento Pattern</span>
                    <h3 class="mt-3 text-lg font-bold text-slate-900">Immutable Snapshots</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        Every prompt and document edit creates an immutable version snapshot via <code class="text-xs">MementoService</code>, supporting one-click rollback and regression tracking.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-white border-t border-slate-200 py-8 text-center text-xs text-slate-500">
        PromptLab &bull; AI-Assisted Knowledge &amp; Prompt/RAG Evaluation Workbench &bull; ZUMS ITM 427 / ITM 428 Lab
    </footer>

    <!-- Hardware-Accelerated 3D Scroll & Interaction Engine -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const heroCard = document.getElementById('hero-3d-card');
            const badgeLeft = document.getElementById('badge-left');
            const badgeRight = document.getElementById('badge-right');
            const stageBox = document.getElementById('stage-3d-box');
            const stageLabel = document.getElementById('stage-label');
            const steps = document.querySelectorAll('.story-step');
            const dots = document.querySelectorAll('.stage-dot');
            const labels = [
                'Stage 01 / 04 — Chunking Engine',
                'Stage 02 / 04 — Retrieval & Ranking',
                'Stage 03 / 04 — Memento Snapshots',
                'Stage 04 / 04 — Side-by-Side Score'
            ];
            const rotations = [
                'rotateY(-3deg) rotateX(2deg)',
                'rotateY(3deg) rotateX(-2deg)',
                'rotateY(-2deg) rotateX(-2deg)',
                'rotateY(0deg) rotateX(0deg)'
            ];

            let activeStep = 0;

            function setStage(index) {
                if (index === activeStep && document.getElementById(`panel-${index}`)?.classList.contains('hidden') === false) return;
                activeStep = index;

                steps.forEach((el, i) => {
                    if (i === index) {
                        el.classList.add('border-2', 'border-blue-600', 'bg-white', 'shadow-md');
                        el.classList.remove('border-slate-200', 'bg-white/70');
                    } else {
                        el.classList.remove('border-2', 'border-blue-600', 'bg-white', 'shadow-md');
                        el.classList.add('border-slate-200', 'bg-white/70');
                    }
                });

                dots.forEach((dot, i) => {
                    if (i === index) {
                        dot.classList.add('w-6', 'bg-blue-600');
                        dot.classList.remove('w-2', 'bg-slate-300');
                    } else {
                        dot.classList.remove('w-6', 'bg-blue-600');
                        dot.classList.add('w-2', 'bg-slate-300');
                    }
                });

                for (let i = 0; i < 4; i++) {
                    const panel = document.getElementById(`panel-${i}`);
                    if (!panel) continue;
                    if (i === index) {
                        panel.classList.remove('hidden');
                    } else {
                        panel.classList.add('hidden');
                    }
                }

                if (stageLabel) stageLabel.textContent = labels[index];
                if (stageBox) stageBox.style.transform = rotations[index];
            }

            // Clickable story steps
            steps.forEach((step, idx) => {
                step.addEventListener('click', () => setStage(idx));
            });

            // Smooth 3D Hero Tilt & Scroll-Driven Pipeline Updates
            let ticking = false;
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        const scrollY = window.scrollY;
                        // Hero 3D flattening as user scrolls down
                        const progress = Math.min(1, scrollY / 420);
                        const rotX = 10 - (progress * 10);
                        const scale = 0.97 + (progress * 0.03);
                        if (heroCard) {
                            heroCard.style.transform = `rotateX(${rotX.toFixed(2)}deg) scale(${scale.toFixed(3)})`;
                        }
                        if (badgeLeft) {
                            badgeLeft.style.transform = `translateY(${(-progress * 28).toFixed(1)}px) translateZ(30px)`;
                        }
                        if (badgeRight) {
                            badgeRight.style.transform = `translateY(${(progress * 24).toFixed(1)}px) translateZ(30px)`;
                        }

                        // Determine which story step is closest to viewport center
                        const viewportCenter = window.innerHeight * 0.48;
                        let closestIdx = activeStep;
                        let closestDist = Infinity;
                        steps.forEach((step, idx) => {
                            const rect = step.getBoundingClientRect();
                            const stepCenter = rect.top + rect.height / 2;
                            const dist = Math.abs(stepCenter - viewportCenter);
                            if (rect.top < window.innerHeight * 0.85 && rect.bottom > 80 && dist < closestDist) {
                                closestDist = dist;
                                closestIdx = idx;
                            }
                        });
                        setStage(closestIdx);
                        ticking = false;
                    });
                    ticking = true;
                }
            }, { passive: true });

            // Scroll Reveal for cards
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            }, { threshold: 0.12 });

            document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));

            // Subtle 3D mouse-tilt on feature/pattern cards
            document.querySelectorAll('.tilt-card').forEach(card => {
                card.addEventListener('mousemove', (e) => {
                    const rect = card.getBoundingClientRect();
                    const x = (e.clientX - rect.left) / rect.width - 0.5;
                    const y = (e.clientY - rect.top) / rect.height - 0.5;
                    card.style.transform = `perspective(900px) rotateY(${(x * 7).toFixed(2)}deg) rotateX(${(-y * 7).toFixed(2)}deg) translateY(-3px)`;
                });
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'perspective(900px) rotateY(0deg) rotateX(0deg) translateY(0px)';
                });
            });
        });
    </script>

</body>
</html>
