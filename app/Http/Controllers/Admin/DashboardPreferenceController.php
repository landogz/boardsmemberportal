<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DashboardPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardPreferenceController extends Controller
{
    /**
     * Show the customize dashboard page (admin-only).
     */
    public function edit()
    {
        $user = Auth::user();

        // Restrict strictly to admin-level or CONSEC users
        if (
            !$user ||
            !(
                $user->hasRole('admin') ||
                $user->hasRole('consec') ||
                in_array($user->privilege, ['admin', 'consec'])
            )
        ) {
            abort(403, 'You do not have permission to customize the dashboard.');
        }

        return view('admin.dashboard-customize');
    }

    /**
     * Return current dashboard preferences for the logged-in admin.
     */
    public function show()
    {
        $user = Auth::user();

        if (
            !$user ||
            !(
                $user->hasRole('admin') ||
                $user->hasRole('consec') ||
                in_array($user->privilege, ['admin', 'consec'])
            )
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        $pref = DashboardPreference::where('user_id', $user->id)->first();

        // Provide sensible defaults if no preferences exist yet
        $defaultLayout = [
            'widgets' => [
                [
                    'key' => 'board_members',
                    'visible' => true,
                    'summary' => true,
                ],
                [
                    'key' => 'attendance',
                    'visible' => true,
                    'summary' => true,
                ],
                [
                    'key' => 'media_storage',
                    'visible' => true,
                    'summary' => true,
                ],
                [
                    'key' => 'audit_logs',
                    'visible' => true,
                    'summary' => true,
                ],
                [
                    'key' => 'activity_over_time',
                    'visible' => true,
                    'timeRange' => '30_days',
                ],
                [
                    'key' => 'user_distribution',
                    'visible' => true,
                ],
                [
                    'key' => 'messages_activity',
                    'visible' => true,
                    'timeRange' => '7_days',
                ],
                [
                    'key' => 'announcements_status',
                    'visible' => true,
                ],
                [
                    'key' => 'content_overview',
                    'visible' => true,
                    'timeRange' => '6_months',
                ],
            ],
        ];

        return response()->json([
            'success' => true,
            'layout' => $pref ? $pref->layout : $defaultLayout,
        ]);
    }

    /**
     * Store / update dashboard preferences for the logged-in admin.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (
            !$user ||
            !(
                $user->hasRole('admin') ||
                $user->hasRole('consec') ||
                in_array($user->privilege, ['admin', 'consec'])
            )
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        // If layout is empty or widgets are not provided, treat this as a reset to defaults
        if (!$request->has('layout.widgets') || empty($request->input('layout.widgets'))) {
            DashboardPreference::where('user_id', $user->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dashboard preferences reset to defaults.',
            ]);
        }

        $data = $request->validate([
            'layout' => 'required|array',
            'layout.widgets' => 'required|array',
            'layout.widgets.*.key' => 'required|string',
            // visible/summary will be cast to boolean manually to avoid checkbox \"on\" issues
            'layout.widgets.*.visible' => 'required',
            'layout.widgets.*.summary' => 'sometimes',
            'layout.widgets.*.timeRange' => 'sometimes|string|in:7_days,30_days,90_days,6_months,12_months',
            'layout.summaryCardsEnabled' => 'sometimes',
        ]);

        $layout = $data['layout'];

        // Normalize booleans for widgets
        if (!empty($layout['widgets']) && is_array($layout['widgets'])) {
            foreach ($layout['widgets'] as $index => $widget) {
                // visible
                $layout['widgets'][$index]['visible'] = filter_var(
                    $widget['visible'],
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                ) ?? false;

                // summary (optional)
                if (array_key_exists('summary', $widget)) {
                    $layout['widgets'][$index]['summary'] = filter_var(
                        $widget['summary'],
                        FILTER_VALIDATE_BOOLEAN,
                        FILTER_NULL_ON_FAILURE
                    ) ?? false;
                }
            }
        }

        // Normalize summaryCardsEnabled
        if (array_key_exists('summaryCardsEnabled', $layout)) {
            $layout['summaryCardsEnabled'] = filter_var(
                $layout['summaryCardsEnabled'],
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            ) ?? false;
        }

        DashboardPreference::updateOrCreate(
            ['user_id' => $user->id],
            ['layout' => $layout]
        );

        return response()->json([
            'success' => true,
            'message' => 'Dashboard preferences saved successfully.',
        ]);
    }
}

