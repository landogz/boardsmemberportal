<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DeploymentInstructionsController extends Controller
{
    /**
     * Public page to view deployment instructions (rendered from DEPLOYMENT_INSTRUCTIONS.md).
     */
    public function show()
    {
        $path = base_path('DEPLOYMENT_INSTRUCTIONS.md');

        if (File::exists($path)) {
            $markdown = File::get($path);
        } else {
            $markdown = "# Deployment Instructions\n\nDEPLOYMENT_INSTRUCTIONS.md file not found.";
        }

        // Use Str::markdown when available; otherwise fall back to a simple escaped view.
        if (method_exists(Str::class, 'markdown')) {
            $html = Str::markdown($markdown);
        } else {
            $html = nl2br(e($markdown));
        }

        return view('deployment-instructions', [
            'html' => $html,
        ]);
    }
}

