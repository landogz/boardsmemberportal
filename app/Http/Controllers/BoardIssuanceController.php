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
     * Pagination: 10 per page. Optional filters: type, year, keyword.
     */
    public function index(Request $request)
    {
        $type = $request->query('type');
        $year = $request->query('year');
        $keyword = $request->query('keyword');

        $documents = null;
        $regulations = null;

        // Board resolutions (official documents) - only when type is not "regulation"
        if ($type !== 'regulation') {
            $documentsQuery = OfficialDocument::with(['pdf', 'uploader'])
                ->orderBy('approved_date', 'desc');

            if ($year) {
                $documentsQuery->whereYear('approved_date', $year);
            }
            if ($keyword) {
                $documentsQuery->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', '%' . $keyword . '%')
                        ->orWhere('description', 'like', '%' . $keyword . '%')
                        ->orWhere('version', 'like', '%' . $keyword . '%');
                });
            }

            $documents = $documentsQuery->paginate(10)->withQueryString();
        } else {
            $documents = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }

        // Board regulations - only when type is not "resolution"
        if ($type !== 'resolution') {
            $regulationsQuery = BoardRegulation::with(['pdf', 'uploader'])
                ->orderBy('approved_date', 'desc');

            if ($year) {
                $regulationsQuery->whereYear('approved_date', $year);
            }
            if ($keyword) {
                $regulationsQuery->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', '%' . $keyword . '%')
                        ->orWhere('description', 'like', '%' . $keyword . '%')
                        ->orWhere('version', 'like', '%' . $keyword . '%');
                });
            }

            $regulations = $regulationsQuery->paginate(10)->withQueryString();
        } else {
            $regulations = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }

        // Years for filter dropdown (from approved_date)
        $allYears = OfficialDocument::selectRaw('YEAR(approved_date) as y')
            ->whereNotNull('approved_date')
            ->pluck('y')
            ->merge(
                BoardRegulation::selectRaw('YEAR(approved_date) as y')
                    ->whereNotNull('approved_date')
                    ->pluck('y')
            )
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        // Distinct years per type for accordion headers (respect type/year filter)
        $regulationYears = collect();
        if ($type !== 'resolution') {
            $regulationYears = BoardRegulation::selectRaw('YEAR(approved_date) as y')
                ->whereNotNull('approved_date')
                ->when($year, fn ($q) => $q->whereYear('approved_date', $year))
                ->when($keyword, function ($q) use ($keyword) {
                    $q->where(function ($q2) use ($keyword) {
                        $q2->where('title', 'like', '%' . $keyword . '%')
                            ->orWhere('description', 'like', '%' . $keyword . '%')
                            ->orWhere('version', 'like', '%' . $keyword . '%');
                    });
                })
                ->distinct()
                ->pluck('y')
                ->filter()
                ->sortDesc()
                ->values();
        }
        $documentYears = collect();
        if ($type !== 'regulation') {
            $documentYears = OfficialDocument::selectRaw('YEAR(approved_date) as y')
                ->whereNotNull('approved_date')
                ->when($year, fn ($q) => $q->whereYear('approved_date', $year))
                ->when($keyword, function ($q) use ($keyword) {
                    $q->where(function ($q2) use ($keyword) {
                        $q2->where('title', 'like', '%' . $keyword . '%')
                            ->orWhere('description', 'like', '%' . $keyword . '%')
                            ->orWhere('version', 'like', '%' . $keyword . '%');
                    });
                })
                ->distinct()
                ->pluck('y')
                ->filter()
                ->sortDesc()
                ->values();
        }

        return view('board-issuances', [
            'documents' => $documents,
            'regulations' => $regulations,
            'years' => $allYears,
            'regulationYears' => $regulationYears,
            'documentYears' => $documentYears,
        ]);
    }

    /**
     * Return paginated items for one series (year) and type. Used for per-series AJAX pagination (no page reload).
     */
    public function data(Request $request)
    {
        $type = $request->query('type');
        $year = $request->query('year');
        $page = max(1, (int) $request->query('page', 1));
        $keyword = $request->query('keyword');

        if (!in_array($type, ['regulation', 'resolution']) || !$year) {
            return response()->json(['items' => [], 'pagination' => ['current_page' => 1, 'last_page' => 1, 'total' => 0, 'per_page' => 10]]);
        }

        $perPage = 10;

        if ($type === 'regulation') {
            $query = BoardRegulation::with(['pdf', 'uploader'])
                ->whereYear('approved_date', $year)
                ->orderBy('approved_date', 'desc');
            if ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', '%' . $keyword . '%')
                        ->orWhere('description', 'like', '%' . $keyword . '%')
                        ->orWhere('version', 'like', '%' . $keyword . '%');
                });
            }
            $paginator = $query->paginate($perPage, ['*'], 'page', $page);
            $items = $paginator->getCollection()->map(function ($r) {
                $creatorImg = '';
                if ($r->uploader) {
                    $creatorImg = 'https://ui-avatars.com/api/?name=' . urlencode($r->uploader->first_name . ' ' . $r->uploader->last_name) . '&size=64&background=055498&color=fff&bold=true';
                    if ($r->uploader->profile_picture) {
                        $media = \App\Models\MediaLibrary::find($r->uploader->profile_picture);
                        if ($media) {
                            $creatorImg = asset('storage/' . $media->file_path);
                        }
                    }
                }
                return [
                    'id' => $r->id,
                    'title' => $r->title,
                    'type' => 'regulation',
                    'year' => $r->year,
                    'has_pdf' => (bool) $r->pdf,
                    'pdf_url' => $r->pdf ? asset('storage/' . $r->pdf->file_path) : null,
                    'date' => $r->effective_date ? $r->effective_date->format('M d, Y') : '',
                    'description' => $r->description ?? '',
                    'creator' => $r->uploader ? $r->uploader->first_name . ' ' . $r->uploader->last_name : '',
                    'creator_image' => $creatorImg,
                ];
            })->values()->all();
        } else {
            $query = OfficialDocument::with(['pdf', 'uploader'])
                ->whereYear('approved_date', $year)
                ->orderBy('approved_date', 'desc');
            if ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', '%' . $keyword . '%')
                        ->orWhere('description', 'like', '%' . $keyword . '%')
                        ->orWhere('version', 'like', '%' . $keyword . '%');
                });
            }
            $paginator = $query->paginate($perPage, ['*'], 'page', $page);
            $items = $paginator->getCollection()->map(function ($d) {
                $creatorImg = '';
                if ($d->uploader) {
                    $creatorImg = 'https://ui-avatars.com/api/?name=' . urlencode($d->uploader->first_name . ' ' . $d->uploader->last_name) . '&size=64&background=055498&color=fff&bold=true';
                    if ($d->uploader->profile_picture) {
                        $media = \App\Models\MediaLibrary::find($d->uploader->profile_picture);
                        if ($media) {
                            $creatorImg = asset('storage/' . $media->file_path);
                        }
                    }
                }
                return [
                    'id' => $d->id,
                    'title' => $d->title,
                    'type' => 'resolution',
                    'year' => $d->year,
                    'has_pdf' => (bool) $d->pdf,
                    'pdf_url' => $d->pdf ? asset('storage/' . $d->pdf->file_path) : null,
                    'date' => $d->effective_date ? $d->effective_date->format('M d, Y') : '',
                    'description' => $d->description ?? '',
                    'creator' => $d->uploader ? $d->uploader->first_name . ' ' . $d->uploader->last_name : '',
                    'creator_image' => $creatorImg,
                ];
            })->values()->all();
        }

        return response()->json([
            'items' => $items,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
            ],
        ]);
    }
}
