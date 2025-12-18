<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * Create a new audit log entry.
     *
     * @param string $action        Short action name, e.g. 'government_agency.created'
     * @param string|null $description  Human-readable description
     * @param \Illuminate\Database\Eloquent\Model|null $model  Related model (optional)
     * @param array $meta           Extra data to store as JSON
     * @return \App\Models\AuditLog
     */
    public static function log(string $action, ?string $description = null, ?Model $model = null, array $meta = []): AuditLog
    {
        $request = request();

        return AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'description'=> $description,
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null,
            'url'        => $request ? $request->fullUrl() : null,
            'method'     => $request ? $request->method() : null,
            'model_type' => $model ? get_class($model) : null,
            'model_id'   => $model ? $model->getKey() : null,
            'meta'       => $meta,
        ]);
    }
}


