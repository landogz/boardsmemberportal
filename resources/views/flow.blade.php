<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Role-Based Access Flowchart</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --sysad:#7C3AED;--sysad-light:#F5F3FF;--sysad-mid:#DDD6FE;
  --consec:#0369A1;--consec-light:#F0F9FF;--consec-mid:#BAE6FD;
  --bm:#0D9488;--bm-light:#F0FDFA;--bm-mid:#99F6E4;
  --start:#1E293B;--border:#E2E8F0;--bg:#F8FAFC;--text:#0F172A;--muted:#64748B;
}
body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);padding:32px 24px;min-width:1200px}
.page-header{text-align:center;margin-bottom:32px}
.page-header h1{font-size:26px;font-weight:700;letter-spacing:-.5px}
.page-header p{font-size:14px;color:var(--muted);margin-top:6px}
.legend{display:flex;justify-content:center;gap:20px;margin-bottom:32px;flex-wrap:wrap}
.legend-item{display:flex;align-items:center;gap:7px;font-size:12px;font-weight:500}
.legend-dot{width:13px;height:13px;border-radius:50%}
.start-node{display:flex;justify-content:center;margin-bottom:0}
.node-start{background:var(--start);color:white;padding:12px 40px;border-radius:999px;font-size:15px;font-weight:700;box-shadow:0 4px 16px rgba(15,23,42,.25)}
.decision-wrap{display:flex;justify-content:center}
.decision-diamond{background:white;border:2.5px solid #475569;transform:rotate(45deg);width:110px;height:110px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 12px rgba(0,0,0,.08)}
.decision-diamond span{transform:rotate(-45deg);font-size:13px;font-weight:700;text-align:center;line-height:1.3;color:var(--text)}
.branch-row{display:flex;justify-content:center;gap:28px;margin-top:0;align-items:flex-start}
.role-col{display:flex;flex-direction:column;align-items:center;width:315px;flex-shrink:0}
.role-header{width:100%;border-radius:14px;padding:14px 16px;display:flex;align-items:center;gap:10px;box-shadow:0 4px 16px rgba(0,0,0,.1);position:relative;z-index:1}
.role-header.sysad{background:var(--sysad);color:white}
.role-header.consec{background:var(--consec);color:white}
.role-header.bm{background:var(--bm);color:white}
.role-icon{width:38px;height:38px;border-radius:9px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.role-title{font-size:14px;font-weight:700}
.role-sub{font-size:10px;opacity:.8;margin-top:2px}
.module-list{width:100%;display:flex;flex-direction:column;gap:7px;margin-top:8px}
.module-card{width:100%;border-radius:10px;border:1.5px solid;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.05)}
.module-card.sysad{border-color:var(--sysad-mid);background:var(--sysad-light)}
.module-card.consec{border-color:var(--consec-mid);background:var(--consec-light)}
.module-card.bm{border-color:var(--bm-mid);background:var(--bm-light)}
.module-header{padding:7px 12px;display:flex;align-items:center;gap:7px;font-size:11px;font-weight:700;letter-spacing:.04em;text-transform:uppercase}
.module-header.sysad{background:var(--sysad-mid);color:var(--sysad)}
.module-header.consec{background:var(--consec-mid);color:var(--consec)}
.module-header.bm{background:var(--bm-mid);color:var(--bm)}
.module-body{padding:9px 12px}
.access-item{display:flex;align-items:flex-start;gap:6px;font-size:11.5px;color:#334155;line-height:1.5;margin-bottom:4px}
.access-item:last-child{margin-bottom:0}
.badge{font-size:9.5px;font-weight:700;padding:2px 6px;border-radius:999px;flex-shrink:0;margin-top:1px;font-family:'JetBrains Mono',monospace;white-space:nowrap}
.badge-full{background:#DCFCE7;color:#15803D}
.badge-read{background:#DBEAFE;color:#1D4ED8}
.badge-limit{background:#FEF9C3;color:#A16207}
.badge-none{background:#F1F5F9;color:#64748B}
.badge-cond{background:#FCE7F3;color:#9D174D}
.badge-manage{background:#EDE9FE;color:#6D28D9}
.access-level-pill{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:999px;font-size:10.5px;font-weight:700;margin-bottom:6px}
.pill-full{background:#DCFCE7;color:#15803D;border:1px solid #86EFAC}
.pill-partial{background:#FEF9C3;color:#A16207;border:1px solid #FDE68A}
.pill-limited{background:#DBEAFE;color:#1D4ED8;border:1px solid #93C5FD}
.final-row{display:flex;justify-content:center;margin-top:0}
.final-node{background:var(--start);color:white;padding:14px 48px;border-radius:14px;font-size:14px;font-weight:700;text-align:center;box-shadow:0 4px 20px rgba(15,23,42,.2)}
.badge-ref{display:flex;flex-wrap:wrap;gap:10px;margin-top:48px;padding:20px 24px;background:white;border-radius:14px;border:1.5px solid #E2E8F0;max-width:680px;margin-left:auto;margin-right:auto}
</style>
</head>
<body>

<div class="page-header">
  <h1>🔐 Role-Based Access Control Flowchart</h1>
  <p>Board Member Portal — Access permissions by user role</p>
</div>

<div class="legend">
  <div class="legend-item"><div class="legend-dot" style="background:#7C3AED"></div>SysAd</div>
  <div class="legend-item"><div class="legend-dot" style="background:#0369A1"></div>CONSEC</div>
  <div class="legend-item"><div class="legend-dot" style="background:#0D9488"></div>Board Member</div>
  <div class="legend-item"><div class="legend-dot" style="background:#DCFCE7;border:1.5px solid #86EFAC"></div>Full Access</div>
  <div class="legend-item"><div class="legend-dot" style="background:#FEF9C3;border:1.5px solid #FDE68A"></div>Conditional</div>
  <div class="legend-item"><div class="legend-dot" style="background:#DBEAFE;border:1.5px solid #93C5FD"></div>Read-Only</div>
  <div class="legend-item"><div class="legend-dot" style="background:#F1F5F9;border:1.5px solid #CBD5E1"></div>No Access</div>
</div>

<!-- START NODE -->
<div class="start-node"><div class="node-start">👤 User Login</div></div>

<!-- DOWN ARROW -->
<div style="display:flex;justify-content:center;margin:8px 0">
  <div style="display:flex;flex-direction:column;align-items:center">
    <div style="width:2px;height:24px;background:#94A3B8"></div>
    <div style="width:0;height:0;border-left:6px solid transparent;border-right:6px solid transparent;border-top:8px solid #94A3B8"></div>
  </div>
</div>

<!-- DECISION -->
<div class="decision-wrap">
  <div class="decision-diamond"><span>What is<br>user role?</span></div>
</div>

<!-- BRANCH SVG -->
<div style="position:relative;height:56px;display:flex;justify-content:center">
  <svg width="1010" height="56" viewBox="0 0 1010 56" style="overflow:visible">
    <line x1="505" y1="0" x2="505" y2="28" stroke="#94A3B8" stroke-width="2"/>
    <line x1="157" y1="28" x2="853" y2="28" stroke="#94A3B8" stroke-width="2"/>
    <line x1="157" y1="28" x2="157" y2="56" stroke="#7C3AED" stroke-width="2"/>
    <polygon points="151,50 163,50 157,58" fill="#7C3AED"/>
    <line x1="505" y1="28" x2="505" y2="56" stroke="#0369A1" stroke-width="2"/>
    <polygon points="499,50 511,50 505,58" fill="#0369A1"/>
    <line x1="853" y1="28" x2="853" y2="56" stroke="#0D9488" stroke-width="2"/>
    <polygon points="847,50 859,50 853,58" fill="#0D9488"/>
    <rect x="107" y="16" width="100" height="22" rx="5" fill="white" stroke="#E2E8F0"/>
    <text x="157" y="31" text-anchor="middle" font-family="Inter" font-size="11" font-weight="700" fill="#7C3AED">SysAd</text>
    <rect x="455" y="16" width="100" height="22" rx="5" fill="white" stroke="#E2E8F0"/>
    <text x="505" y="31" text-anchor="middle" font-family="Inter" font-size="11" font-weight="700" fill="#0369A1">CONSEC</text>
    <rect x="797" y="16" width="112" height="22" rx="5" fill="white" stroke="#E2E8F0"/>
    <text x="853" y="31" text-anchor="middle" font-family="Inter" font-size="11" font-weight="700" fill="#0D9488">Board Member</text>
  </svg>
</div>

<!-- THREE COLUMNS -->
<div class="branch-row">

  <!-- SYSAD -->
  <div class="role-col">
    <div class="role-header sysad">
      <div class="role-icon">🛡️</div>
      <div><div class="role-title">System Administrator</div><div class="role-sub">Full access · /admin/*</div></div>
    </div>
    <div style="width:2px;height:10px;background:#7C3AED"></div>
    <div style="width:100%;display:flex;justify-content:center;margin-bottom:6px">
      <span class="access-level-pill pill-full">✦ Full Access — All Modules</span>
    </div>
    <div class="module-list">
      <div class="module-card sysad">
        <div class="module-header sysad">👥 User Management</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-full">FULL</span>Create, edit, deactivate Board Members &amp; CONSEC</div>
          <div class="access-item"><span class="badge badge-manage">MANAGE</span>Assign roles &amp; permissions to all users</div>
        </div>
      </div>
      <div class="module-card sysad">
        <div class="module-header sysad">📢 Announcements</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-full">FULL</span>Create, edit, delete all announcements</div>
          <div class="access-item"><span class="badge badge-manage">MANAGE</span>Choose allowed users per announcement</div>
          <div class="access-item"><span class="badge badge-full">FULL</span>Send email &amp; push notifications</div>
        </div>
      </div>
      <div class="module-card sysad">
        <div class="module-header sysad">📋 Notices &amp; Attendance</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-full">FULL</span>Full CRUD of meeting notices</div>
          <div class="access-item"><span class="badge badge-full">FULL</span>View all attendance confirmations</div>
          <div class="access-item"><span class="badge badge-manage">MANAGE</span>View agenda requests &amp; reference materials</div>
          <div class="access-item"><span class="badge badge-manage">MANAGE</span>Re-invite users to meetings</div>
        </div>
      </div>
      <div class="module-card sysad">
        <div class="module-header sysad">🗳️ Referendums</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-full">FULL</span>Full CRUD of all referendums</div>
          <div class="access-item"><span class="badge badge-manage">MANAGE</span>Control who can vote &amp; comment</div>
          <div class="access-item"><span class="badge badge-full">FULL</span>View all results &amp; comments</div>
        </div>
      </div>
      <div class="module-card sysad">
        <div class="module-header sysad">📁 Board Issuances &amp; Reports</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-full">FULL</span>Full document management</div>
          <div class="access-item"><span class="badge badge-full">FULL</span>Generate &amp; export all reports</div>
          <div class="access-item"><span class="badge badge-full">FULL</span>Manage landing page banners</div>
        </div>
      </div>
      <div class="module-card sysad">
        <div class="module-header sysad">🔍 Audit Logs</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-read">VIEW</span>Full audit trail of all user actions</div>
        </div>
      </div>
      <div class="module-card sysad">
        <div class="module-header sysad">💬 Messages · Notifs · Calendar</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-full">FULL</span>Highest privilege — all features enabled</div>
        </div>
      </div>
    </div>
  </div>

  <!-- CONSEC -->
  <div class="role-col">
    <div class="role-header consec">
      <div class="role-icon">📋</div>
      <div><div class="role-title">CONSEC</div><div class="role-sub">Admin dashboard · Privileged user</div></div>
    </div>
    <div style="width:2px;height:10px;background:#0369A1"></div>
    <div style="width:100%;display:flex;justify-content:center;margin-bottom:6px">
      <span class="access-level-pill pill-partial">◆ Partial — Permission-Dependent</span>
    </div>
    <div class="module-list">
      <div class="module-card consec">
        <div class="module-header consec">👥 User Management</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-limit">LIMITED</span>Cannot freely manage all users</div>
          <div class="access-item"><span class="badge badge-read">VIEW</span>Sees user info for assigned modules only</div>
        </div>
      </div>
      <div class="module-card consec">
        <div class="module-header consec">📢 Announcements</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-read">VIEW</span>Receives if in announcement_user_access</div>
          <div class="access-item"><span class="badge badge-cond">COND</span>May create content if role grants permission</div>
          <div class="access-item"><span class="badge badge-read">VIEW</span>Admin-side view where permissions allow</div>
        </div>
      </div>
      <div class="module-card consec">
        <div class="module-header consec">📋 Notices &amp; Attendance</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-read">VIEW</span>Receives notices if in notice_user_access</div>
          <div class="access-item"><span class="badge badge-full">ACTION</span>Accept / decline invitations</div>
          <div class="access-item"><span class="badge badge-full">ACTION</span>Submit agenda inclusion requests</div>
          <div class="access-item"><span class="badge badge-cond">COND</span>Admin view of notices/attendance if granted</div>
        </div>
      </div>
      <div class="module-card consec">
        <div class="module-header consec">🗳️ Referendums</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-cond">COND</span>Vote &amp; comment if in referendum_user_access</div>
          <div class="access-item"><span class="badge badge-cond">COND</span>Create &amp; manage if role permits</div>
        </div>
      </div>
      <div class="module-card consec">
        <div class="module-header consec">📁 Board Issuances &amp; Reports</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-cond">COND</span>Access based on role permissions granted</div>
        </div>
      </div>
      <div class="module-card consec">
        <div class="module-header consec">🔍 Audit Logs</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-none">NONE</span>No access to audit logs</div>
        </div>
      </div>
      <div class="module-card consec">
        <div class="module-header consec">💬 Messages · Notifs · Calendar</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-full">PRIV</span>Treated as privileged user (admin/consec)</div>
          <div class="access-item"><span class="badge badge-none">NOTE</span>No full system-wide control</div>
        </div>
      </div>
    </div>
  </div>

  <!-- BOARD MEMBER -->
  <div class="role-col">
    <div class="role-header bm">
      <div class="role-icon">👔</div>
      <div><div class="role-title">Board Member</div><div class="role-sub">Member portal · privilege = 'user'</div></div>
    </div>
    <div style="width:2px;height:10px;background:#0D9488"></div>
    <div style="width:100%;display:flex;justify-content:center;margin-bottom:6px">
      <span class="access-level-pill pill-limited">● Limited — Assignment-Based</span>
    </div>
    <div class="module-list">
      <div class="module-card bm">
        <div class="module-header bm">👥 User Management</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-none">NONE</span>No user management access</div>
          <div class="access-item"><span class="badge badge-read">VIEW</span>Own profile management only</div>
        </div>
      </div>
      <div class="module-card bm">
        <div class="module-header bm">📢 Announcements</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-cond">COND</span>Only sees if in announcement_user_access</div>
          <div class="access-item"><span class="badge badge-read">READ</span>Read-only — no management access</div>
        </div>
      </div>
      <div class="module-card bm">
        <div class="module-header bm">📋 Notices &amp; Attendance</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-cond">COND</span>Sees /notices only if in notice_user_access</div>
          <div class="access-item"><span class="badge badge-full">ACTION</span>Accept / decline invitations</div>
          <div class="access-item"><span class="badge badge-full">ACTION</span>Submit agenda requests &amp; ref materials</div>
          <div class="access-item"><span class="badge badge-none">BLOCK</span>After decline → no meeting materials access</div>
        </div>
      </div>
      <div class="module-card bm">
        <div class="module-header bm">🗳️ Referendums</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-cond">COND</span>View &amp; vote only if in referendum_user_access</div>
          <div class="access-item"><span class="badge badge-cond">COND</span>Comment only if in referendum_user_access</div>
          <div class="access-item"><span class="badge badge-none">NONE</span>No creation or management</div>
        </div>
      </div>
      <div class="module-card bm">
        <div class="module-header bm">📁 Board Issuances</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-read">VIEW</span>Public &amp; assigned issuances only</div>
          <div class="access-item"><span class="badge badge-none">NONE</span>No management or upload access</div>
        </div>
      </div>
      <div class="module-card bm">
        <div class="module-header bm">🔍 Audit Logs</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-none">NONE</span>No access to audit logs</div>
        </div>
      </div>
      <div class="module-card bm">
        <div class="module-header bm">💬 Messages · Notifs · Calendar</div>
        <div class="module-body">
          <div class="access-item"><span class="badge badge-read">VIEW</span>View assigned calendar events only</div>
          <div class="access-item"><span class="badge badge-full">USE</span>Messaging &amp; notifications as end-user</div>
          <div class="access-item"><span class="badge badge-none">NONE</span>No manager-level controls</div>
        </div>
      </div>
    </div>
  </div>

</div>

<!-- CONVERGE SVG -->
<div style="position:relative;height:56px;display:flex;justify-content:center;margin-top:16px">
  <svg width="1010" height="56" viewBox="0 0 1010 56" style="overflow:visible">
    <line x1="157" y1="0" x2="157" y2="28" stroke="#7C3AED" stroke-width="2"/>
    <line x1="505" y1="0" x2="505" y2="28" stroke="#0369A1" stroke-width="2"/>
    <line x1="853" y1="0" x2="853" y2="28" stroke="#0D9488" stroke-width="2"/>
    <line x1="157" y1="28" x2="853" y2="28" stroke="#94A3B8" stroke-width="2"/>
    <line x1="505" y1="28" x2="505" y2="56" stroke="#1E293B" stroke-width="2"/>
    <polygon points="499,50 511,50 505,58" fill="#1E293B"/>
  </svg>
</div>

<!-- FINAL NODE -->
<div class="final-row">
  <div class="final-node">
    ✅ Render Permitted UI &amp; Data
    <div style="font-size:11px;font-weight:400;opacity:.7;margin-top:4px">Access enforced server-side via role &amp; permission middleware</div>
  </div>
</div>

<!-- BADGE REFERENCE -->
<div class="badge-ref">
  <div style="width:100%;font-size:13px;font-weight:700;color:#0F172A;margin-bottom:6px">Access Badge Reference</div>
  <div style="display:flex;align-items:center;gap:6px;font-size:12px"><span class="badge badge-full">FULL</span>Complete unrestricted access</div>
  <div style="display:flex;align-items:center;gap:6px;font-size:12px"><span class="badge badge-manage">MANAGE</span>Administrative control</div>
  <div style="display:flex;align-items:center;gap:6px;font-size:12px"><span class="badge badge-read">VIEW/READ</span>Read-only access</div>
  <div style="display:flex;align-items:center;gap:6px;font-size:12px"><span class="badge badge-cond">COND</span>Conditional — requires assignment</div>
  <div style="display:flex;align-items:center;gap:6px;font-size:12px"><span class="badge badge-limit">LIMITED</span>Restricted scope</div>
  <div style="display:flex;align-items:center;gap:6px;font-size:12px"><span class="badge badge-none">NONE/BLOCK</span>No access or actively blocked</div>
  <div style="display:flex;align-items:center;gap:6px;font-size:12px"><span class="badge badge-full">ACTION</span>Can perform specific actions</div>
  <div style="display:flex;align-items:center;gap:6px;font-size:12px"><span class="badge badge-full">PRIV</span>Privileged user-level access</div>
</div>

</body>
</html>