# boardsmemberportal

A modern board member portal built with Laravel 12, Tailwind CSS, Axios, and jQuery. Features a GEN-Z inspired design with comprehensive content management, meeting management, and communication tools.

## 🚀 Tech Stack

- **Backend:** Laravel 12
- **Frontend:** Tailwind CSS v4, HTML5, JavaScript
- **AJAX:** Axios, jQuery
- **Notifications:** SweetAlert2
- **Database:** MySQL

## 📋 Table of Contents

1. [System Workflow](#1-system-workflow-flowchart)
2. [Database Structure](#2-database-structure-erd--tables)
3. [Pages & Routes](#3-pages-needed-full-routes)
4. [Design Guidelines](#4-design-guidelines-modern-gen-z-ui)

---

## 1. SYSTEM WORKFLOW (FLOWCHART)

### A. User Access Flow

```
Start
  └─► Landing Page (User / Admin)
        ├─► Login
        │      ├─► Validate Credentials
        │      ├─► Check Role (User / Admin / Manager)
        │      └─► Redirect to Dashboard
        └─► Register (Authorized Representatives only)
               ├─► Fill Personal & Organization Details
               ├─► Email Verification
               └─► Account Activation
```

### B. Dashboard Flow

```
Dashboard
  ├─► Announcement (view, read more)
  ├─► Calendar (events, meetings, schedules)
  ├─► Chat Facility (direct or group)
  ├─► Meeting Notices → View → Link / Attached Files
  └─► Board Resolution Library → View / Download
```

### C. Content Management Flow

```
Admin / Portal Manager
   ├─► Create / Edit Templates
   ├─► Upload Media (Drag & Drop)
   │         ├─► Images
   │         ├─► Audio / Video
   │         └─► Galleries
   ├─► Send Meeting Notices (email + dashboard)
   └─► Manage Menu Items
```

### D. Attendance Confirmation Flow

```
Admin → Select Meeting
   ├─► Send Attendance Request
   │         ├─► Individual Email
   │         └─► Bulk Email
   └─► Users Click Link → Confirm / Decline
             └─► Update Attendance Table
```

### E. Board Resolution Library Flow

```
Admin → Upload Approved Resolution
   ├─► Tag (Category, Date, Committee)
   ├─► Upload PDF / Attachments
   └─► Publish to Library

User → View Resolutions → Filter / Download
```

### F. Administration Flow

```
Admin Panel
   ├─► User Management (create, edit, deactivate)
   ├─► Access Control List (roles, permissions)
   ├─► Code Library Maintenance
   ├─► CMS Settings (SEO, SSL, menus)
   └─► Browser Compatibility Checks
```

---

## 2. DATABASE STRUCTURE (ERD + TABLES)

### A. Main Entities

- Users
- Roles & Permissions (ACL)
- Announcements
- Calendar Events
- Chats
- Registrations & Authorized Representatives
- Notices / Email Templates
- Attendances
- Board Resolutions
- Media Library
- Audit Logs

### B. ERD Structure (Relationships)

```
Users 1..* Notices
Users 1..* Attendances
Users 1..* Chat Messages
Admin 1..* Announcements
Admin 1..* Board Resolutions
Media 1..* Attachments
```

### C. Tables & Important Fields

#### 1. users
```sql
id (uuid)
first_name
last_name
email (unique)
password_hash
role_id (fk)
is_active (bool)
mobile
created_at
updated_at
```

#### 2. roles
```sql
id
role_name (Admin, Portal Manager, Board Member, Representative)
description
```

#### 3. permissions
```sql
id
permission_code
description
```

#### 4. role_permissions
```sql
role_id
permission_id
```

#### 5. announcements
```sql
id
title
content
created_by (user_id)
publish_date
attachments (media_ids)
created_at
```

#### 6. calendar_events
```sql
id
title
description
event_date
meeting_link
created_by
attachments
```

#### 7. chats
```sql
id
sender_id
receiver_id (or group_id)
message
attachments (media_id)
timestamp
```

#### 8. registrations
```sql
id
board_member_name
representative_name
email
mobile
company
status (pending, approved)
created_at
```

#### 9. notices
```sql
id
subject
content
template_id
sent_by
sent_date
is_bulk (bool)
attachments
```

#### 10. attendance
```sql
id
meeting_id
user_id
status (confirmed, declined, pending)
confirmation_date
```

#### 11. board_resolutions
```sql
id
resolution_number
title
description
pdf_file (media_id)
category
approved_date
uploaded_by
```

#### 12. media_library
```sql
id
file_name
file_type
file_path
uploaded_by
uploaded_at
```

---

## 3. PAGES NEEDED (FULL ROUTES)

### A. Public Pages

- `/` - Landing Page (Modern GEN-Z design)
- `/about` - About
- `/login` - Login
- `/register` - Register
- `/forgot-password` - Forgot Password

### B. User Dashboard

- `/dashboard` - Dashboard (Announcements + Calendar + Chat)
- `/profile` - My Profile
- `/notifications` - Notifications Center
- `/messages` - Messages / Chat Page
- `/meeting-notices` - Meeting Notices
- `/attendance/confirm` - Attendance Confirmation Page
- `/resolutions` - Board Resolution Library
- `/media/{id}` - Media Viewer

### C. Portal Manager / Admin Pages

#### Content Management
- `/admin/announcements` - Announcements
- `/admin/templates` - Templates
- `/admin/notices` - Notices (Send individual / bulk)
- `/admin/meetings` - Meeting links
- `/admin/media` - Media library

#### Attendance Management
- `/admin/meetings/create` - Create Meeting
- `/admin/attendance/send` - Send Attendance Email
- `/admin/attendance/status` - Attendance status table

#### Board Resolution Management
- `/admin/resolutions/upload` - Upload resolution
- `/admin/resolutions/categories` - Category management
- `/admin/resolutions` - View / edit / archive

#### Administration (ACL)
- `/admin/users` - User management
- `/admin/roles` - Role management
- `/admin/permissions` - Permission settings
- `/admin/code-library` - Code library maintenance
- `/admin/menus` - Menu management
- `/admin/settings` - CMS Settings (SEO, SSL, Metadata)

---

## 4. DESIGN GUIDELINES (MODERN GEN-Z UI)

### Tone

**Clean • Neon accents • Smooth gradients • Rounded cards • Micro-animations • Dark + Light Mode**

### Color Palette (GEN-Z Inspired)

```css
Primary: Electric Purple #A855F7
Accent: Neon Blue #3B82F6
Secondary: Mint #10B981
Background Light: #F9FAFB
Background Dark: #0F172A
Text: #0A0A0A / #F1F5F9
```

### Layout Style

- Full-width hero section with big headlines
- Rounded tiles/cards (radius 16–24px)
- Vertical rhythm spacing (8px scale)
- Floating elements & subtle shadows
- Sticky header + slide-out mobile menu

### Components

- Announcement cards with icons
- Calendar with colored event tags
- Chat UI similar to Messenger/Slack
- Drag & drop media uploader with preview
- Modal + drawer views
- Breadcrumbs for admin pages
- Data tables with filters + search

### Gen-Z Landing Page Styles

- Oversized bold typography
- Gradient backgrounds
- Animated blobs or shapes
- Subtle parallax
- Floating neon elements
- Call to action: solid rounded pill buttons

---

## 🛠️ Installation

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL
- XAMPP (for local development)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/landogz/boardsmemberportal.git
   cd boardsmemberportal
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Update `.env` with your database credentials**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=boardsmemberportal
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Create database**
   ```sql
   CREATE DATABASE boardsmemberportal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

7. **Run migrations**
   ```bash
   php artisan migrate
   ```

8. **Build assets**
   ```bash
   npm run dev
   # or for production
   npm run build
   ```

9. **Start development server**
   ```bash
   php artisan serve
   ```

10. **Visit**
    - Application: `http://localhost:8000`
    - Example Page: `http://localhost:8000/example`

---

## 📚 Documentation

- [MySQL Setup Guide](README_MYSQL.md)
- [Git Push Instructions](GIT_PUSH_INSTRUCTIONS.md)

---

## 📝 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 🤝 Contributing

Thank you for considering contributing to the boardsmemberportal project!

---

## 🔒 Security Vulnerabilities

If you discover a security vulnerability, please send an e-mail to the project maintainers. All security vulnerabilities will be promptly addressed.
