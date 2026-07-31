<?php

namespace App\Http\Controllers;

use App\Models\OfficialDocument;
use App\Models\BoardRegulation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BoardIssuanceController extends Controller
{
    /**
     * Display the board issuances page (public view)
     * Pagination: 10 per page. Optional filters: type, year, keyword.
     * Year grouping uses SERIES OF YYYY from the title when available.
     */
    public function index(Request $request)
    {
        $type = $request->query('type');
        $year = $request->query('year') ? (int) $request->query('year') : null;
        $keyword = $request->query('keyword');

        $documents = null;
        $regulations = null;

        // Board resolutions (official documents) - only when type is not "regulation"
        if ($type !== 'regulation') {
            $documentsQuery = OfficialDocument::with(['pdf', 'uploader']);
            if ($keyword) {
                $documentsQuery->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', '%' . $keyword . '%')
                        ->orWhere('description', 'like', '%' . $keyword . '%')
                        ->orWhere('version', 'like', '%' . $keyword . '%');
                });
            }

            $documentsCollection = $documentsQuery->get()
                ->when($year, fn (Collection $c) => $c->filter(fn ($d) => $d->belongsToSeriesYear($year))->values())
                ->sort(function ($a, $b) {
                    $numA = (int) ($a->parsed_resolution_number ?? 0);
                    $numB = (int) ($b->parsed_resolution_number ?? 0);
                    if ($numA !== $numB) {
                        return $numB <=> $numA;
                    }

                    return $b->id <=> $a->id;
                })
                ->values();

            $documents = $this->paginateCollection($documentsCollection, 10, $request);
        } else {
            $documents = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }

        // Board regulations - only when type is not "resolution"
        if ($type !== 'resolution') {
            $regulationsQuery = BoardRegulation::with(['pdf', 'uploader']);
            if ($keyword) {
                $regulationsQuery->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', '%' . $keyword . '%')
                        ->orWhere('description', 'like', '%' . $keyword . '%')
                        ->orWhere('version', 'like', '%' . $keyword . '%');
                });
            }

            $regulationsCollection = $regulationsQuery->get()
                ->when($year, fn (Collection $c) => $c->filter(fn ($r) => $r->belongsToSeriesYear($year))->values())
                ->sort(function ($a, $b) {
                    $numA = (int) ($a->parsed_regulation_number ?? 0);
                    $numB = (int) ($b->parsed_regulation_number ?? 0);
                    if ($numA !== $numB) {
                        return $numB <=> $numA;
                    }

                    return $b->id <=> $a->id;
                })
                ->values();

            $regulations = $this->paginateCollection($regulationsCollection, 10, $request);
        } else {
            $regulations = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }

        // Years from SERIES OF YYYY in titles (fallback: approved/effective date via model year)
        $allYears = OfficialDocument::query()
            ->get(['title', 'approved_date'])
            ->map(fn ($d) => $d->year)
            ->merge(
                BoardRegulation::query()
                    ->get(['title', 'effective_date', 'approved_date'])
                    ->map(fn ($r) => $r->year)
            )
            ->filter()
            ->map(fn ($y) => (int) $y)
            ->unique()
            ->sortDesc()
            ->values();

        // Distinct years per type for accordion headers
        $regulationYears = collect();
        if ($type !== 'resolution') {
            $regulationYears = BoardRegulation::query()
                ->when($keyword, function ($q) use ($keyword) {
                    $q->where(function ($q2) use ($keyword) {
                        $q2->where('title', 'like', '%' . $keyword . '%')
                            ->orWhere('description', 'like', '%' . $keyword . '%')
                            ->orWhere('version', 'like', '%' . $keyword . '%');
                    });
                })
                ->get(['title', 'effective_date', 'approved_date'])
                ->map(fn ($r) => $r->year)
                ->filter()
                ->map(fn ($y) => (int) $y)
                ->when($year, fn (Collection $c) => $c->filter(fn ($y) => $y === $year)->values())
                ->unique()
                ->sortDesc()
                ->values();
        }

        $documentYears = collect();
        if ($type !== 'regulation') {
            $documentYears = OfficialDocument::query()
                ->when($keyword, function ($q) use ($keyword) {
                    $q->where(function ($q2) use ($keyword) {
                        $q2->where('title', 'like', '%' . $keyword . '%')
                            ->orWhere('description', 'like', '%' . $keyword . '%')
                            ->orWhere('version', 'like', '%' . $keyword . '%');
                    });
                })
                ->get(['title', 'approved_date'])
                ->map(fn ($d) => $d->year)
                ->filter()
                ->map(fn ($y) => (int) $y)
                ->when($year, fn (Collection $c) => $c->filter(fn ($y) => $y === $year)->values())
                ->unique()
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
     * Year is matched from "SERIES OF YYYY" in the title when present.
     */
    public function data(Request $request)
    {
        $type = $request->query('type');
        $year = (int) $request->query('year');
        $page = max(1, (int) $request->query('page', 1));
        $keyword = $request->query('keyword');

        if (!in_array($type, ['regulation', 'resolution']) || !$year) {
            return response()->json(['items' => [], 'pagination' => ['current_page' => 1, 'last_page' => 1, 'total' => 0, 'per_page' => 10]]);
        }

        $perPage = 10;

        if ($type === 'regulation') {
            $query = BoardRegulation::with(['pdf', 'uploader']);
            if ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', '%' . $keyword . '%')
                        ->orWhere('description', 'like', '%' . $keyword . '%')
                        ->orWhere('version', 'like', '%' . $keyword . '%');
                });
            }

            // Filter by SERIES OF year from title only (not effectivity date); sort by number DESC
            $sorted = $query->get()
                ->filter(fn ($r) => $r->belongsToSeriesYear($year))
                ->sort(function ($a, $b) {
                    $numA = (int) ($a->parsed_regulation_number ?? 0);
                    $numB = (int) ($b->parsed_regulation_number ?? 0);
                    if ($numA !== $numB) {
                        return $numB <=> $numA; // higher number first (18, 17, …, 5)
                    }

                    return $b->id <=> $a->id;
                })
                ->values();

            $total = $sorted->count();
            $lastPage = max(1, (int) ceil($total / $perPage));
            $page = min($page, $lastPage);
            $pageItems = $sorted->forPage($page, $perPage)->values();

            $items = $pageItems->map(function ($r) {
                $creatorLabel = 'Conference Secretariat';
                $creatorImg = 'https://ui-avatars.com/api/?name=' . urlencode($creatorLabel) . '&size=64&background=055498&color=fff&bold=true';
                return [
                    'id' => $r->id,
                    'title' => $r->title,
                    'type' => 'regulation',
                    'year' => $r->year,
                    'series_year' => $r->parsed_series_year,
                    'number' => $r->parsed_regulation_number,
                    'has_pdf' => (bool) $r->pdf,
                    'pdf_url' => $r->pdf ? asset('storage/' . $r->pdf->file_path) : null,
                    'date' => $r->effective_date ? $r->effective_date->format('M d, Y') : '',
                    'date_label' => 'Effectivity Date',
                    'description' => $r->description ?? '',
                    'creator' => $creatorLabel,
                    'creator_image' => $creatorImg,
                ];
            })->values()->all();
        } else {
            $query = OfficialDocument::with(['pdf', 'uploader']);
            if ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', '%' . $keyword . '%')
                        ->orWhere('description', 'like', '%' . $keyword . '%')
                        ->orWhere('version', 'like', '%' . $keyword . '%');
                });
            }

            // Filter by SERIES OF year from title only (not approval date); sort by number DESC
            $sorted = $query->get()
                ->filter(fn ($d) => $d->belongsToSeriesYear($year))
                ->sort(function ($a, $b) {
                    $numA = (int) ($a->parsed_resolution_number ?? 0);
                    $numB = (int) ($b->parsed_resolution_number ?? 0);
                    if ($numA !== $numB) {
                        return $numB <=> $numA; // higher number first
                    }

                    return $b->id <=> $a->id;
                })
                ->values();

            $total = $sorted->count();
            $lastPage = max(1, (int) ceil($total / $perPage));
            $page = min($page, $lastPage);
            $pageItems = $sorted->forPage($page, $perPage)->values();

            $items = $pageItems->map(function ($d) {
                $creatorLabel = 'Conference Secretariat';
                $creatorImg = 'https://ui-avatars.com/api/?name=' . urlencode($creatorLabel) . '&size=64&background=055498&color=fff&bold=true';
                return [
                    'id' => $d->id,
                    'title' => $d->title,
                    'type' => 'resolution',
                    'year' => $d->year,
                    'series_year' => $d->parsed_series_year,
                    'number' => $d->parsed_resolution_number,
                    'has_pdf' => (bool) $d->pdf,
                    'pdf_url' => $d->pdf ? asset('storage/' . $d->pdf->file_path) : null,
                    'date' => $d->approved_date ? $d->approved_date->format('M d, Y') : '',
                    'date_label' => 'Approved Date',
                    'description' => $d->description ?? '',
                    'creator' => $creatorLabel,
                    'creator_image' => $creatorImg,
                ];
            })->values()->all();
        }

        return response()->json([
            'items' => $items,
            'pagination' => [
                'current_page' => $page,
                'last_page' => $lastPage,
                'total' => $total,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Paginate a collection for the index filters (kept for compatibility).
     */
    private function paginateCollection(Collection $items, int $perPage, Request $request): \Illuminate\Pagination\LengthAwarePaginator
    {
        $page = max(1, (int) $request->query('page', 1));
        $total = $items->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }
}
