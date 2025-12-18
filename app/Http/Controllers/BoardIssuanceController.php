<?php

namespace App\Http\Controllers;

use App\Models\OfficialDocument;
use App\Models\BoardRegulation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardIssuanceController extends Controller
{
    /**
     * Display the board issuances page (public view)
     */
    public function index(Request $request)
    {
        // Get board resolutions (official documents)
        $documents = OfficialDocument::with(['pdf', 'uploader'])
            ->orderBy('effective_date', 'desc')
            ->get();

        // Get board regulations
        $regulations = BoardRegulation::with(['pdf', 'uploader'])
            ->orderBy('effective_date', 'desc')
            ->get();

        // Combine years from both documents and regulations
        $allYears = $documents->pluck('year')
            ->merge($regulations->pluck('year'))
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        return view('board-issuances', [
            'documents' => $documents,
            'regulations' => $regulations,
            'years' => $allYears,
        ]);
    }
}
