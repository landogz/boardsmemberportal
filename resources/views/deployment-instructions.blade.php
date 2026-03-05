<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Deployment Instructions - Board Member Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('components.header-footer-styles')
    <style>
        :root {
            --deploy-bg: #f8fafc;
            --deploy-card: #ffffff;
            --deploy-border: #e2e8f0;
            --deploy-text: #1e293b;
            --deploy-text-muted: #64748b;
            --deploy-accent: #055498;
            --deploy-accent-soft: rgba(5, 84, 152, 0.08);
            --deploy-code-bg: #f1f5f9;
            --deploy-pre-bg: #0f172a;
            --deploy-pre-text: #e2e8f0;
        }
        .dark {
            --deploy-bg: #0f172a;
            --deploy-card: #1e293b;
            --deploy-border: #334155;
            --deploy-text: #f1f5f9;
            --deploy-text-muted: #94a3b8;
            --deploy-accent-soft: rgba(5, 84, 152, 0.2);
            --deploy-code-bg: #334155;
            --deploy-pre-bg: #020617;
            --deploy-pre-text: #cbd5e1;
        }
        body {
            font-family: 'DM Sans', system-ui, -apple-system, sans-serif;
        }
        .deploy-page {
            min-height: 100vh;
            background: var(--deploy-bg);
            padding: 2rem 1rem 4rem;
        }
        .deploy-container {
            max-width: 720px;
            margin: 0 auto;
        }
        .deploy-hero {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem 0.5rem;
        }
        .deploy-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--deploy-accent);
            margin-bottom: 1rem;
        }
        .deploy-hero h1 {
            font-size: clamp(1.75rem, 4vw, 2.25rem);
            font-weight: 700;
            color: var(--deploy-text);
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin: 0 0 0.5rem 0;
        }
        .deploy-hero p {
            font-size: 1rem;
            color: var(--deploy-text-muted);
            margin: 0;
            max-width: 36ch;
            margin-left: auto;
            margin-right: auto;
        }
        .deploy-card {
            background: var(--deploy-card);
            border: 1px solid var(--deploy-border);
            border-radius: 1rem;
            padding: 2.5rem clamp(1.5rem, 4vw, 2.5rem);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        .dark .deploy-card {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }
        .deploy-content {
            font-size: 0.9375rem;
            line-height: 1.7;
            color: var(--deploy-text);
        }
        .deploy-content > *:first-child { margin-top: 0; }
        .deploy-content > *:last-child { margin-bottom: 0; }
        .deploy-content h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--deploy-text);
            margin: 2.25rem 0 0.75rem 0;
            padding-bottom: 0.375rem;
            letter-spacing: -0.01em;
        }
        .deploy-content h1:first-child { margin-top: 0; }
        .deploy-content h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--deploy-text);
            margin: 2rem 0 0.625rem 0;
            padding-bottom: 0.25rem;
            border-bottom: 1px solid var(--deploy-border);
        }
        .deploy-content h3, .deploy-content h4 {
            font-size: 1.0625rem;
            font-weight: 600;
            color: var(--deploy-text);
            margin: 1.5rem 0 0.5rem 0;
        }
        .deploy-content p {
            margin: 0 0 1rem 0;
            color: var(--deploy-text);
        }
        .deploy-content ul, .deploy-content ol {
            margin: 0 0 1rem 0;
            padding-left: 1.5rem;
        }
        .deploy-content li {
            margin-bottom: 0.375rem;
        }
        .deploy-content li:last-child { margin-bottom: 0; }
        .deploy-content code {
            font-family: 'JetBrains Mono', ui-monospace, monospace;
            font-size: 0.8125rem;
            font-weight: 500;
            background: var(--deploy-code-bg);
            color: var(--deploy-text);
            padding: 0.2em 0.45em;
            border-radius: 0.375rem;
        }
        .deploy-content pre {
            font-family: 'JetBrains Mono', ui-monospace, monospace;
            font-size: 0.8125rem;
            line-height: 1.6;
            background: var(--deploy-pre-bg);
            color: var(--deploy-pre-text);
            padding: 1.25rem 1.25rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            margin: 1rem 0;
            border-left: 3px solid var(--deploy-accent);
        }
        .deploy-content pre code {
            background: transparent;
            padding: 0;
            color: inherit;
        }
        .deploy-content blockquote {
            margin: 1rem 0;
            padding: 0.75rem 1rem 0.75rem 1rem;
            border-left: 4px solid var(--deploy-accent);
            background: var(--deploy-accent-soft);
            border-radius: 0 0.375rem 0.375rem 0;
            color: var(--deploy-text-muted);
        }
        .deploy-content blockquote p:last-child { margin-bottom: 0; }
        .deploy-content hr {
            border: 0;
            height: 1px;
            background: var(--deploy-border);
            margin: 2rem 0;
        }
        .deploy-content a {
            color: var(--deploy-accent);
            text-decoration: none;
            font-weight: 500;
        }
        .deploy-content a:hover {
            text-decoration: underline;
        }
        .deploy-content strong {
            font-weight: 600;
        }
        .deploy-meta {
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--deploy-border);
            font-size: 0.8125rem;
            color: var(--deploy-text-muted);
        }
        .deploy-meta p {
            margin: 0;
        }
        .deploy-meta code {
            font-size: 0.75rem;
        }
    </style>
</head>
<body class="bg-[#F9FAFB] dark:bg-[#0F172A] text-[#0A0A0A] dark:text-[#F1F5F9] transition-colors duration-300">
    @include('components.header')
    @include('components.theme-toggle-script')

    <main class="deploy-page">
        <div class="deploy-container">
            <header class="deploy-hero">
                <div class="deploy-badge">
                    <i class="fas fa-book"></i>
                    <span>Documentation</span>
                </div>
                <h1>Deployment Instructions</h1>
                <p>Technical guide for deploying the Board Member Portal to production.</p>
            </header>

            <section class="deploy-card">
                <article class="deploy-content">
                    {!! $html !!}
                </article>
                <footer class="deploy-meta">
                    <p>Generated from <code>DEPLOYMENT_INSTRUCTIONS.md</code>. Keep your codebase up to date for the latest steps.</p>
                </footer>
            </section>
        </div>
    </main>

    @include('components.footer')
</body>
</html>
