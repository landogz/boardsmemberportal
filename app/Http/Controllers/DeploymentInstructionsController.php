<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DeploymentInstructionsController extends Controller
{
    /**
     * Public page: Deployment Guide with tabs (Live | Localhost Laragon | Localhost Mac).
     * Rendered from DEPLOYMENT.md.
     */
    public function show()
    {
        $path = base_path('DEPLOYMENT.md');

        if (File::exists($path)) {
            $markdown = File::get($path);
            // Make "See DEPLOYMENT_INSTRUCTIONS.md" work on the web
            $markdown = str_replace(
                '](DEPLOYMENT_INSTRUCTIONS.md)',
                '](' . url('/deployment-instructions/live') . ')',
                $markdown
            );
        } else {
            $markdown = "# Deployment Guide\n\nDEPLOYMENT.md file not found.";
        }

        if (method_exists(Str::class, 'markdown')) {
            $html = Str::markdown($markdown);
        } else {
            $html = nl2br(e($markdown));
        }

        // Ensure H2 headings have id attributes so table-of-contents links work
        $html = $this->addHeadingIds($html);

        return view('deployment-instructions', [
            'html' => $html,
            'title' => 'Deployment Guide',
            'description' => 'Install prerequisites, deploy to production (separate database server, email, cron), or run locally on Laragon or Mac. Use the full live guide for copy-paste server commands and troubleshooting.',
        ]);
    }

    /**
     * Add id attributes to h2 headings that don't have one (slug from text).
     */
    private function addHeadingIds(string $html): string
    {
        return preg_replace_callback(
            '/<h2(?:\s+[^>]*)?>(.*?)<\/h2>/is',
            function (array $m) {
                $text = trim(strip_tags($m[1]));
                $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $text));
                $slug = trim($slug, '-');
                if ($slug === '') {
                    return $m[0];
                }
                // If the tag already has an id, keep it
                if (strpos($m[0], ' id=') !== false) {
                    return $m[0];
                }
                return '<h2 id="' . $slug . '">' . $m[1] . '</h2>';
            },
            $html
        );
    }

    /**
     * Full live deployment instructions (rendered from DEPLOYMENT_INSTRUCTIONS.md).
     */
    public function showLive()
    {
        $path = base_path('DEPLOYMENT_INSTRUCTIONS.md');

        if (File::exists($path)) {
            $markdown = File::get($path);
        } else {
            $markdown = "# Live Deployment Instructions\n\nDEPLOYMENT_INSTRUCTIONS.md file not found.";
        }

        if (method_exists(Str::class, 'markdown')) {
            $html = Str::markdown($markdown);
        } else {
            $html = nl2br(e($markdown));
        }

        return view('deployment-instructions', [
            'html' => $html,
            'title' => 'Live Deployment Instructions',
            'description' => 'Full step-by-step guide for deploying to a production server.',
            'sourceFile' => 'DEPLOYMENT_INSTRUCTIONS.md',
            'showBackLink' => true,
        ]);
    }
}

