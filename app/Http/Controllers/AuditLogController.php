<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs (admin only).
     */
    public function index(Request $request)
    {
        if (!Auth::check() || !Auth::user()->hasPermission('view audit logs')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view audit logs.');
        }

        // Get all logs (DataTables will handle pagination client-side)
        // Exclude logs for landogzwebsolutions@landogzwebsolutions.com
        $logs = AuditLog::with('user')
            ->where(function($query) {
                $query->whereHas('user', function($userQuery) {
                    $userQuery->where('email', '!=', 'landogzwebsolutions@landogzwebsolutions.com');
                })
                ->orWhereNull('user_id'); // Include system/guest logs
            })
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.audit-logs.index', compact('logs'));
    }

    /**
     * Export audit logs as PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            if (!Auth::check() || !Auth::user()->hasPermission('view audit logs')) {
                return redirect()->route('dashboard')->with('error', 'You do not have permission to view audit logs.');
            }

            // Start with base query - exclude logs for landogzwebsolutions@landogzwebsolutions.com
            $query = AuditLog::with('user')
                ->where(function($q) {
                    $q->whereHas('user', function($userQuery) {
                        $userQuery->where('email', '!=', 'landogzwebsolutions@landogzwebsolutions.com');
                    })
                    ->orWhereNull('user_id'); // Include system/guest logs
                });

            // Apply search filter if provided
            // Log the request for debugging
            \Log::info('PDF Export Request', [
                'has_search' => $request->has('search'),
                'search_value' => $request->input('search'),
                'all_params' => $request->all()
            ]);

            if ($request->has('search') && !empty(trim($request->input('search')))) {
                $searchTerm = trim($request->input('search'));
                
                \Log::info('Applying search filter', ['search_term' => $searchTerm]);
                
                $query->where(function($q) use ($searchTerm) {
                    // Search in action
                    $q->where('action', 'like', '%' . $searchTerm . '%')
                      // Search in description
                      ->orWhere('description', 'like', '%' . $searchTerm . '%')
                      // Search in IP address
                      ->orWhere('ip_address', 'like', '%' . $searchTerm . '%')
                      // Search in URL
                      ->orWhere('url', 'like', '%' . $searchTerm . '%')
                      // Search in method
                      ->orWhere('method', 'like', '%' . $searchTerm . '%')
                      // Search in model_type
                      ->orWhere('model_type', 'like', '%' . $searchTerm . '%')
                      // Search in user's name or email (via relationship)
                      ->orWhereHas('user', function($userQuery) use ($searchTerm) {
                          $userQuery->where('first_name', 'like', '%' . $searchTerm . '%')
                                    ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                                    ->orWhere('email', 'like', '%' . $searchTerm . '%');
                      })
                      // Search in created_at date
                      ->orWhereRaw("DATE_FORMAT(created_at, '%M %d, %Y') LIKE ?", ['%' . $searchTerm . '%'])
                      ->orWhereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') LIKE ?", ['%' . $searchTerm . '%']);
                });
            }

            // Get filtered logs ordered by id descending
            // For PDF export, we'll limit to prevent memory issues but allow more records
            $logs = $query->orderBy('id', 'desc')->limit(5000)->get();
            
            // Check if logs exist
            if ($logs->isEmpty()) {
                return redirect()->route('admin.audit-logs.index')
                    ->with('error', 'No audit logs found to export.');
            }
            
            // Generate PDF with error handling
            try {
                // Increase memory limit for PDF generation
                ini_set('memory_limit', '512M');
                set_time_limit(300); // 5 minutes timeout
                
                $pdf = Pdf::loadView('admin.audit-logs.pdf', compact('logs'));
                $pdf->setPaper('A4', 'landscape');
                $pdf->setOption('enable-local-file-access', true);
                
                $filename = 'audit-logs-' . now()->format('Y-m-d') . '.pdf';
                if ($request->has('search') && !empty(trim($request->search))) {
                    $filename = 'audit-logs-filtered-' . now()->format('Y-m-d') . '.pdf';
                }
                
                return $pdf->download($filename);
            } catch (\Exception $pdfException) {
                \Log::error('PDF Generation Error: ' . $pdfException->getMessage(), [
                    'trace' => $pdfException->getTraceAsString(),
                    'logs_count' => $logs->count(),
                    'file' => $pdfException->getFile(),
                    'line' => $pdfException->getLine()
                ]);
                
                throw $pdfException; // Re-throw to be caught by outer catch
            }
        } catch (\Exception $e) {
            \Log::error('PDF Export Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->route('admin.audit-logs.index')
                ->with('error', 'Failed to export PDF: ' . $e->getMessage() . '. Please check the logs for more details.');
        }
    }
}


