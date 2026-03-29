@extends('admin.layout')

@section('title', 'Database Backup')

@php
    $pageTitle = 'Database Backup';
    $pageDescription = 'Create and download database backups (admin only)';
@endphp

@section('content')
<div class="p-4 sm:p-6 max-w-5xl mx-auto space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Backup database</h2>
                <p class="text-sm text-gray-600 mt-1">
                    Backups are stored securely under <code class="text-xs bg-gray-100 px-1 rounded">storage/app/backups/database</code>.
                    MySQL/MariaDB uses <span class="font-medium">mysqldump</span>; SQLite copies the database file.
                </p>
            </div>
            <div class="flex flex-col items-stretch sm:items-end gap-2">
                <button
                    type="button"
                    id="backupNowBtn"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-white font-medium text-sm transition-colors shadow-sm"
                    style="background-color: #055498;"
                >
                    <span id="backupSpinner" class="hidden inline-block h-4 w-4 border-2 border-white border-t-transparent rounded-full animate-spin" role="status" aria-hidden="true"></span>
                    <i id="backupIcon" class="fas fa-cloud-download-alt"></i>
                    <span id="backupBtnLabel">Backup now</span>
                </button>
                <p id="backupStatusText" class="text-xs text-gray-500 text-center sm:text-right min-h-[1rem]"></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-800">Backup history</h3>
            <p class="text-xs text-gray-500 mt-0.5">Date, size, status, and download</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date &amp; time</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Download</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($backups as $b)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-gray-800">
                                {{ \Carbon\Carbon::parse($b['modified_at'])->timezone(config('app.timezone'))->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-4 py-3 text-gray-700 font-mono text-xs break-all max-w-xs">{{ $b['filename'] }}</td>
                            <td class="px-4 py-3 text-right text-gray-700">
                                @php
                                    $bytes = (int) $b['size'];
                                    if ($bytes >= 1048576) {
                                        $sizeLabel = number_format($bytes / 1048576, 2).' MB';
                                    } elseif ($bytes >= 1024) {
                                        $sizeLabel = number_format($bytes / 1024, 2).' KB';
                                    } else {
                                        $sizeLabel = $bytes.' B';
                                    }
                                @endphp
                                {{ $sizeLabel }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Success</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a
                                    href="{{ route('admin.database-backup.download', ['filename' => $b['filename']]) }}"
                                    class="inline-flex items-center gap-1 text-sm font-medium text-[#055498] hover:underline"
                                >
                                    <i class="fas fa-download text-xs"></i>
                                    Download
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 text-sm">
                                No backups yet. Use <strong>Backup now</strong> to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]');
    if (csrf) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf.getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    }

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true
    });

    const btn = document.getElementById('backupNowBtn');
    const spinner = document.getElementById('backupSpinner');
    const icon = document.getElementById('backupIcon');
    const label = document.getElementById('backupBtnLabel');
    const statusText = document.getElementById('backupStatusText');

    function setLoading(loading) {
        if (!btn) return;
        btn.disabled = loading;
        if (spinner) spinner.classList.toggle('hidden', !loading);
        if (icon) icon.classList.toggle('hidden', loading);
        if (label) label.textContent = loading ? 'Backing up…' : 'Backup now';
        if (statusText) statusText.textContent = loading ? 'Please wait — do not close this page.' : '';
    }

    if (btn) {
        btn.addEventListener('click', async function () {
            setLoading(true);
            try {
                const res = await axios.post('{{ route('admin.database-backup.run') }}');
                if (res.data && res.data.status) {
                    Toast.fire({ icon: 'success', title: res.data.message || 'Backup completed.' });
                    window.location.reload();
                } else {
                    Toast.fire({ icon: 'error', title: (res.data && res.data.message) ? res.data.message : 'Backup failed.' });
                }
            } catch (e) {
                const msg = e.response && e.response.data && e.response.data.message
                    ? e.response.data.message
                    : (e.message || 'Backup failed.');
                Toast.fire({ icon: 'error', title: msg });
            } finally {
                setLoading(false);
            }
        });
    }
})();
</script>
@endsection
