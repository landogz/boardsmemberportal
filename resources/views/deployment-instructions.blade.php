<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Deployment Instructions - Board Member Portal</title>
    <!-- Fonts and CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/gotham-fonts@1.0.3/css/gotham-rounded.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.header-footer-styles')
    <style>
        .deploy-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1rem 3rem;
        }
        .deploy-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .deploy-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #055498;
        }
        .deploy-header p {
            margin-top: 0.5rem;
            font-size: 0.9rem;
            color: #6b7280;
        }
        .dark .deploy-header p {
            color: #9ca3af;
        }
        .deploy-card {
            background-color: #ffffff;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            padding: 1.75rem 1.5rem;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        }
        .dark .deploy-card {
            background-color: #0f172a;
            border-color: #1f2937;
        }
        .deploy-content {
            font-size: 0.9rem;
            line-height: 1.6;
            color: #374151;
        }
        .dark .deploy-content {
            color: #e5e7eb;
        }
        .deploy-content h1,
        .deploy-content h2,
        .deploy-content h3,
        .deploy-content h4 {
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            color: #111827;
        }
        .dark .deploy-content h1,
        .dark .deploy-content h2,
        .dark .deploy-content h3,
        .dark .deploy-content h4 {
            color: #f9fafb;
        }
        .deploy-content code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 0.85rem;
            background-color: #f3f4f6;
            padding: 0.15rem 0.3rem;
            border-radius: 0.25rem;
        }
        .dark .deploy-content code {
            background-color: #111827;
        }
        .deploy-content pre {
            background-color: #0b1120;
            color: #e5e7eb;
            padding: 1rem;
            border-radius: 0.75rem;
            overflow-x: auto;
            font-size: 0.85rem;
            margin: 1rem 0;
        }
        .deploy-content pre code {
            background: transparent;
            padding: 0;
        }
        .deploy-content ul,
        .deploy-content ol {
            margin-left: 1.25rem;
            margin-bottom: 0.75rem;
        }
        .deploy-content li {
            margin-bottom: 0.25rem;
        }
        .deploy-meta {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px dashed #e5e7eb;
            font-size: 0.8rem;
            color: #6b7280;
        }
        .dark .deploy-meta {
            border-top-color: #374151;
            color: #9ca3af;
        }
    </style>
</head>
<body class="bg-[#F9FAFB] dark:bg-[#020617] text-[#0A0A0A] dark:text-[#F1F5F9] transition-colors duration-300">
    @include('components.header')
    @include('components.theme-toggle-script')

    <main class="deploy-container">
        <div class="deploy-header">
            <h1>Deployment Instructions</h1>
            <p>Technical guide for deploying the Board Member Portal to production.</p>
        </div>

        <section class="deploy-card">
            <article class="deploy-content">
                {!! $html !!}
            </article>
            <div class="deploy-meta">
                <p>
                    This page is generated from the <code>DEPLOYMENT_INSTRUCTIONS.md</code> file in the repository.
                    For the latest updates, ensure your codebase is up to date.
                </p>
            </div>
        </section>
    </main>

    @include('components.footer')
</body>
</html>

