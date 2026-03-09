# Security

## SQL injection

- **Status: No vulnerabilities found.** The codebase uses Laravel’s query builder and Eloquent. User input is not concatenated into raw SQL.
- All `where('column', 'like', $value)` usages pass the value as a bound parameter.
- Raw usage is limited to:
  - Migrations and seeders (fixed strings).
  - `whereRaw('LOWER(name) != ?', ['consec'])` (bound parameter).
  - `orderByRaw(...)` with literal SQL (no user input).
  - `selectRaw('YEAR(approved_date) as y')` (column names only).

## Hardening applied

1. **Address API** (`Api/AddressController`): `region_code`, `province_code`, `city_code` are validated (nullable, string, max 32, alphanumeric/dash/underscore) so only safe values are used in queries.
2. **Report generation** (`Admin/ReportGenerationController`): `report_type` is validated against an allowlist; `search`, dates, `notice_id`, `user_id`, `status`, `year` are validated and length-capped where relevant.
3. **Address settings** (`Admin/AddressSettingsController`): `type` allowlisted; `search` limited to 255 characters.
4. **Message search** (`MessageController`): `search` validated as nullable string, max 255.
5. **Media library** (`MediaLibraryController`): `search` and `type` validated (search max 255; type allowlisted where used).

## Mass assignment

- **User model**: `privilege` and `password_hash` are in `$fillable` but are only set in code (registration sets `privilege` to `'user'`, admin/CONSEC flows set both explicitly). No controller uses `$request->all()` or unvalidated request keys for `User::create()`/`update()` in a way that would allow privilege escalation. Keep this in mind when adding new endpoints.

## XSS

- Blade uses `{{ }}` for most output (escaped).
- `{!! !!}` is used for rich content (announcement description, referendum content, notice description). That content is created by CONSEC/admin. To reduce risk if that content is ever edited by less-trusted users, consider sanitizing HTML on input (e.g. a package like `mews/purifier`) and/or restricting allowed tags.

## CSRF and auth

- Web routes use Laravel’s default CSRF protection for state-changing requests.
- Session expiry / token mismatch is handled in `bootstrap/app.php` (logout, invalidate session, redirect to login).
- Admin/CONSEC areas are protected by auth and permission checks; ensure every admin route uses the appropriate middleware and permission checks.

## Recommendations

1. Keep validating and length-limiting all user input used in queries or displayed in HTML.
2. Do not pass user-controlled values into `DB::raw()`, `whereRaw()`, or similar without using bound parameters.
3. When adding new endpoints, use request validation (e.g. `$request->validate([...])`) and avoid mass assignment of sensitive fields (`privilege`, `password_hash`, etc.) from the request.
4. For rich HTML from users, sanitize on input and/or restrict allowed tags before storing and rendering with `{!! !!}`.
5. Run `php artisan route:list` and audit that admin/API routes have the intended middleware and authorization.
