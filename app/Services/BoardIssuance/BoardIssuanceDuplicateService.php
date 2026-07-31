<?php

namespace App\Services\BoardIssuance;

use App\Models\BoardRegulation;
use App\Models\OfficialDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class BoardIssuanceDuplicateService
{
    public const TYPE_REGULATION = 'regulation';
    public const TYPE_RESOLUTION = 'resolution';

    /**
     * Ensure no other issuance shares the same number + series year (from title).
     * Falls back to case-insensitive exact title match when number/series cannot be parsed.
     *
     * On update, if the number + series year is unchanged, allow the save even when
     * another duplicate already exists (so isolated legacy duplicates remain editable).
     *
     * @throws ValidationException
     */
    public function assertUnique(string $type, string $title, ?int $excludeId = null, ?string $previousTitle = null): void
    {
        if ($excludeId && $previousTitle !== null) {
            $oldNumber = $this->parseNumber($type, $previousTitle);
            $oldSeries = $this->parseSeriesYear($previousTitle);
            $newNumber = $this->parseNumber($type, $title);
            $newSeries = $this->parseSeriesYear($title);

            if ($oldNumber && $oldSeries && $oldNumber === $newNumber && $oldSeries === $newSeries) {
                return;
            }

            if (!$newNumber && !$newSeries && $this->normalizeTitle($previousTitle) === $this->normalizeTitle($title)) {
                return;
            }
        }

        $duplicate = $this->findDuplicate($type, $title, $excludeId);

        if (!$duplicate) {
            return;
        }

        $label = $type === self::TYPE_REGULATION ? 'Board Regulation' : 'Board Resolution';
        $number = $this->parseNumber($type, $title);
        $series = $this->parseSeriesYear($title);

        if ($number && $series) {
            $message = sprintf(
                'A %s with NO. %d, SERIES OF %d already exists. Duplicate entries are not allowed.',
                $label,
                $number,
                $series
            );
        } else {
            $message = sprintf(
                'A %s with this title already exists. Duplicate entries are not allowed.',
                $label
            );
        }

        throw ValidationException::withMessages([
            'title' => [$message],
        ]);
    }

    public function findDuplicate(string $type, string $title, ?int $excludeId = null): ?Model
    {
        $number = $this->parseNumber($type, $title);
        $series = $this->parseSeriesYear($title);

        $candidates = $this->candidateQuery($type, $title, $number, $series, $excludeId)->get();

        if ($number && $series) {
            return $candidates->first(function (Model $item) use ($type, $number, $series) {
                return $this->itemNumber($type, $item) === $number
                    && $this->parseSeriesYear($item->title ?? '') === $series;
            });
        }

        $normalized = $this->normalizeTitle($title);

        return $candidates->first(function (Model $item) use ($normalized) {
            return $this->normalizeTitle($item->title ?? '') === $normalized;
        });
    }

    protected function candidateQuery(string $type, string $title, ?int $number, ?int $series, ?int $excludeId)
    {
        $query = $type === self::TYPE_REGULATION
            ? BoardRegulation::query()
            : OfficialDocument::query();

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($number && $series) {
            $query->where(function ($q) use ($number) {
                $q->where('title', 'like', '%NO. ' . $number . '%')
                    ->orWhere('title', 'like', '%NO.' . $number . '%')
                    ->orWhere('title', 'like', '%No. ' . $number . '%')
                    ->orWhere('title', 'like', '%No.' . $number . '%');
            })->where(function ($q) use ($series) {
                $q->where('title', 'like', '%SERIES OF ' . $series . '%')
                    ->orWhere('title', 'like', '%SERIES OF YEAR ' . $series . '%')
                    ->orWhere('title', 'like', '%Series of ' . $series . '%');
            });
        } else {
            $query->whereRaw('LOWER(TRIM(title)) = ?', [$this->normalizeTitle($title)]);
        }

        return $query;
    }

    protected function parseNumber(string $type, string $title): ?int
    {
        $pattern = $type === self::TYPE_REGULATION
            ? '/board\s+regulation\s+no\.?\s*(\d+)/i'
            : '/board\s+resolution\s+no\.?\s*(\d+)/i';

        if (preg_match($pattern, $title, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    protected function parseSeriesYear(string $title): ?int
    {
        if (preg_match('/series\s+of\s+(?:year\s+)?(\d{4})/i', $title, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    protected function itemNumber(string $type, Model $item): ?int
    {
        if ($type === self::TYPE_REGULATION) {
            return $item->parsed_regulation_number;
        }

        return $item->parsed_resolution_number;
    }

    protected function normalizeTitle(string $title): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/', ' ', $title) ?? $title));
    }
}
