@extends('admin.layout')

@section('title', 'Customize Dashboard')

@php
    $pageTitle = 'Customize Dashboard';
@endphp

@section('content')
<div class="p-4 sm:p-6">
    <div class="flex items-center justify-between mb-4 sm:mb-6">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Customize Dashboard</h2>
            <p class="text-sm text-gray-500 mt-1">
                Configure which widgets are visible, their order, and analytics time ranges. Changes are saved per admin and persist across sessions.
            </p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-3 py-2 text-sm font-semibold rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
    </div>

    <!-- Layout Controls -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Widget List (drag-and-drop) -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-lg" style="background-color: rgba(5, 84, 152, 0.08);">
                        <i class="fas fa-th-large text-[#055498]"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Dashboard Widgets</h3>
                </div>
                <span class="text-xs text-gray-500 hidden sm:inline">Drag to reorder • Use toggles to show/hide</span>
            </div>

            <ul id="dashboardWidgetList" class="space-y-3">
                <!-- Template items; actual state (order/visibility) loaded via AJAX -->
                <li class="widget-item bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 sm:px-4 sm:py-3 flex items-center justify-between cursor-move"
                    data-widget-key="board_members">
                    <div class="flex items-center gap-3">
                        <span class="handle text-gray-400">
                            <i class="fas fa-grip-vertical"></i>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Board Members & Authorized Reps</p>
                            <p class="text-xs text-gray-500">Summary cards showing counts.</p>
                        </div>
                    </div>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="widget-visible-toggle form-checkbox h-4 w-4 text-[#055498]" checked>
                        <span class="ml-2 text-xs text-gray-600">Show</span>
                    </label>
                </li>

                <li class="widget-item bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 sm:px-4 sm:py-3 flex items-center justify-between cursor-move"
                    data-widget-key="attendance">
                    <div class="flex items-center gap-3">
                        <span class="handle text-gray-400">
                            <i class="fas fa-grip-vertical"></i>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Attendance & Pending Confirmations</p>
                            <p class="text-xs text-gray-500">Summary cards for attendance stats.</p>
                        </div>
                    </div>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="widget-visible-toggle form-checkbox h-4 w-4 text-[#055498]" checked>
                        <span class="ml-2 text-xs text-gray-600">Show</span>
                    </label>
                </li>

                <li class="widget-item bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 sm:px-4 sm:py-3 flex items-center justify-between cursor-move"
                    data-widget-key="media_storage">
                    <div class="flex items-center gap-3">
                        <span class="handle text-gray-400">
                            <i class="fas fa-grip-vertical"></i>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Media Files & Storage Usage</p>
                            <p class="text-xs text-gray-500">Summary cards for media and MB storage.</p>
                        </div>
                    </div>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="widget-visible-toggle form-checkbox h-4 w-4 text-[#055498]" checked>
                        <span class="ml-2 text-xs text-gray-600">Show</span>
                    </label>
                </li>

                <li class="widget-item bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 sm:px-4 sm:py-3 flex items-center justify-between cursor-move"
                    data-widget-key="audit_logs">
                    <div class="flex items-center gap-3">
                        <span class="handle text-gray-400">
                            <i class="fas fa-grip-vertical"></i>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Audit Logs & Today’s Activities</p>
                            <p class="text-xs text-gray-500">Summary cards for logs and today’s activities.</p>
                        </div>
                    </div>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="widget-visible-toggle form-checkbox h-4 w-4 text-[#055498]" checked>
                        <span class="ml-2 text-xs text-gray-600">Show</span>
                    </label>
                </li>

                <li class="widget-item bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 sm:px-4 sm:py-3 flex items-center justify-between cursor-move"
                    data-widget-key="activity_over_time">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full">
                        <div class="flex items-center gap-3 flex-1">
                            <span class="handle text-gray-400">
                                <i class="fas fa-grip-vertical"></i>
                            </span>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Activity Over Time</p>
                                <p class="text-xs text-gray-500">Audit log activity chart.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <select class="widget-range-select text-xs border border-gray-300 rounded-md px-2 py-1"
                                    data-default="30_days">
                                <option value="7_days">Last 7 days</option>
                                <option value="30_days" selected>Last 30 days</option>
                                <option value="90_days">Last 90 days</option>
                            </select>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="widget-visible-toggle form-checkbox h-4 w-4 text-[#055498]" checked>
                                <span class="ml-1 text-xs text-gray-600">Show</span>
                            </label>
                        </div>
                    </div>
                </li>

                <li class="widget-item bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 sm:px-4 sm:py-3 flex items-center justify-between cursor-move"
                    data-widget-key="user_distribution">
                    <div class="flex items-center gap-3">
                        <span class="handle text-gray-400">
                            <i class="fas fa-grip-vertical"></i>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-gray-800">User Distribution</p>
                            <p class="text-xs text-gray-500">Pie chart of admins, CONSEC, board members, reps.</p>
                        </div>
                    </div>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="widget-visible-toggle form-checkbox h-4 w-4 text-[#055498]" checked>
                        <span class="ml-2 text-xs text-gray-600">Show</span>
                    </label>
                </li>

                <li class="widget-item bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 sm:px-4 sm:py-3 flex items-center justify-between cursor-move"
                    data-widget-key="messages_activity">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full">
                        <div class="flex items-center gap-3 flex-1">
                            <span class="handle text-gray-400">
                                <i class="fas fa-grip-vertical"></i>
                            </span>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Messages Activity</p>
                                <p class="text-xs text-gray-500">Messaging volume chart.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <select class="widget-range-select text-xs border border-gray-300 rounded-md px-2 py-1"
                                    data-default="7_days">
                                <option value="7_days" selected>Last 7 days</option>
                                <option value="30_days">Last 30 days</option>
                            </select>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="widget-visible-toggle form-checkbox h-4 w-4 text-[#055498]" checked>
                                <span class="ml-1 text-xs text-gray-600">Show</span>
                            </label>
                        </div>
                    </div>
                </li>

                <li class="widget-item bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 sm:px-4 sm:py-3 flex items-center justify-between cursor-move"
                    data-widget-key="announcements_status">
                    <div class="flex items-center gap-3">
                        <span class="handle text-gray-400">
                            <i class="fas fa-grip-vertical"></i>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Announcements Status</p>
                            <p class="text-xs text-gray-500">Published vs draft chart.</p>
                        </div>
                    </div>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="widget-visible-toggle form-checkbox h-4 w-4 text-[#055498]" checked>
                        <span class="ml-2 text-xs text-gray-600">Show</span>
                    </label>
                </li>

                <li class="widget-item bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 sm:px-4 sm:py-3 flex items-center justify-between cursor-move"
                    data-widget-key="content_overview">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full">
                        <div class="flex items-center gap-3 flex-1">
                            <span class="handle text-gray-400">
                                <i class="fas fa-grip-vertical"></i>
                            </span>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Content Creation Overview</p>
                                <p class="text-xs text-gray-500">Resolutions, regulations, announcements, notices.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <select class="widget-range-select text-xs border border-gray-300 rounded-md px-2 py-1"
                                    data-default="6_months">
                                <option value="6_months" selected>Last 6 months</option>
                                <option value="12_months">Last 12 months</option>
                            </select>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="widget-visible-toggle form-checkbox h-4 w-4 text-[#055498]" checked>
                                <span class="ml-1 text-xs text-gray-600">Show</span>
                            </label>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Summary Options -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="p-2 rounded-lg" style="background-color: rgba(251, 209, 22, 0.08);">
                    <i class="fas fa-sliders-h text-[#FBD116]"></i>
                </div>
                <h3 class="text-base sm:text-lg font-semibold text-gray-800">Summary Cards & Options</h3>
            </div>

            <div class="space-y-4">
                <div>
                    <p class="text-sm font-medium text-gray-800 mb-1">Summary Cards</p>
                    <p class="text-xs text-gray-500 mb-2">
                        Enable or disable the top summary cards (counts and totals).
                    </p>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="summaryCardsEnabled" class="form-checkbox h-4 w-4 text-[#055498]" checked>
                        <span class="ml-2 text-xs text-gray-600">Show summary cards section</span>
                    </label>
                </div>

                <div class="pt-2 border-t border-dashed border-gray-200">
                    <p class="text-sm font-medium text-gray-800 mb-1">Apply to Role (Future-ready)</p>
                    <p class="text-xs text-gray-500">
                        Currently, preferences are saved per admin account. Role-based layouts can be added later using the same structure.
                    </p>
                </div>
            </div>

            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
                <button type="button"
                        id="resetDashboardPreferencesBtn"
                        class="inline-flex items-center justify-center px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                    <i class="fas fa-undo mr-2"></i> Reset to Defaults
                </button>
                <button type="button"
                        id="saveDashboardPreferencesBtn"
                        class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg text-white shadow-sm hover:shadow-md transition"
                        style="background: linear-gradient(135deg, #055498 0%, #123a60 100%);">
                    <i class="fas fa-save mr-2"></i> Save Layout
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{-- jQuery UI for drag-and-drop (sortable) --}}
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        $(function () {
            // Make widget list sortable
            $('#dashboardWidgetList').sortable({
                handle: '.handle',
                axis: 'y',
                tolerance: 'pointer'
            });

            // Load existing preferences
            function loadDashboardPreferences() {
                $.ajax({
                    url: '{{ route('admin.dashboard.preferences.show') }}',
                    method: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (!response.success || !response.layout) {
                            return;
                        }

                        const layout = response.layout;
                        const widgets = layout.widgets || [];

                        // Reorder items according to saved layout
                        const $list = $('#dashboardWidgetList');
                        widgets.forEach(function (w) {
                            const $item = $list.find('[data-widget-key="' + w.key + '"]');
                            if ($item.length) {
                                $list.append($item);

                                // Visibility
                                const visible = (typeof w.visible !== 'undefined') ? !!w.visible : true;
                                $item.find('.widget-visible-toggle').prop('checked', visible);

                                // Time ranges (if applicable)
                                if (w.timeRange) {
                                    $item.find('.widget-range-select').val(w.timeRange);
                                }
                            }
                        });

                        // Summary cards
                        if (layout.summaryCardsEnabled !== undefined) {
                            $('#summaryCardsEnabled').prop('checked', !!layout.summaryCardsEnabled);
                        }
                    },
                    error: function () {
                        // Silent fail; dashboard will use defaults
                    }
                });
            }

            loadDashboardPreferences();

            function collectDashboardPreferences() {
                const widgets = [];
                $('#dashboardWidgetList .widget-item').each(function () {
                    const $item = $(this);
                    const key = $item.data('widget-key');
                    const visible = $item.find('.widget-visible-toggle').is(':checked');
                    const rangeSelect = $item.find('.widget-range-select');
                    const timeRange = rangeSelect.length ? rangeSelect.val() : null;

                    const widgetConfig = {
                        key: key,
                        visible: visible
                    };

                    if (timeRange) {
                        widgetConfig.timeRange = timeRange;
                    }

                    // Mark summary cards
                    if (['board_members', 'attendance', 'media_storage', 'audit_logs'].includes(key)) {
                        widgetConfig.summary = true;
                    }

                    widgets.push(widgetConfig);
                });

                return {
                    widgets: widgets,
                    summaryCardsEnabled: $('#summaryCardsEnabled').is(':checked')
                };
            }

            $('#saveDashboardPreferencesBtn').on('click', function (e) {
                e.preventDefault();

                const layout = collectDashboardPreferences();

                $.ajax({
                    url: '{{ route('admin.dashboard.preferences.store') }}',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        layout: layout,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Saved!',
                                text: response.message || 'Dashboard layout has been updated.',
                                confirmButtonColor: '#055498'
                            }).then(function () {
                                // Optionally refresh dashboard in another tab
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to save dashboard preferences.',
                                confirmButtonColor: '#CE2028'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to save dashboard preferences. Please try again.',
                            confirmButtonColor: '#CE2028'
                        });
                    }
                });
            });

            $('#resetDashboardPreferencesBtn').on('click', function (e) {
                e.preventDefault();

                Swal.fire({
                    icon: 'question',
                    title: 'Reset to Defaults?',
                    text: 'This will reset your dashboard layout to the default configuration.',
                    showCancelButton: true,
                    confirmButtonColor: '#055498',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Yes, reset',
                    cancelButtonText: 'Cancel'
                }).then(function (result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    // Clear preferences by sending empty layout (backend will delete preferences)
                    $.ajax({
                        url: '{{ route('admin.dashboard.preferences.store') }}',
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            layout: {}, // backend will treat as minimal
                            _token: '{{ csrf_token() }}'
                        },
                        success: function () {
                            Swal.fire({
                                icon: 'success',
                                title: 'Reset',
                                text: 'Dashboard layout has been reset to defaults.',
                                confirmButtonColor: '#055498'
                            }).then(function () {
                                window.location.reload();
                            });
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to reset dashboard preferences.',
                                confirmButtonColor: '#CE2028'
                            });
                        }
                    });
                });
            });
        });
    </script>
@endpush

