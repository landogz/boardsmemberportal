# boardsmemberportal

A modern board member portal built with Laravel 12, Tailwind CSS, Axios, and jQuery. Features a comprehensive role-based access control system, document management, user management, and real-time communication tools.

## 🚀 Tech Stack

- **Backend:** Laravel 12
- **Frontend:** Tailwind CSS v4, HTML5, JavaScript
- **AJAX:** Axios, jQuery
- **Notifications:** SweetAlert2
- **Database:** MySQL
- **RBAC:** Spatie Laravel Permission
- **PDF Generation:** barryvdh/laravel-dompdf
- **Icons:** Font Awesome

## ✨ Key Features

### 🔐 Authentication & Authorization
- **Role-Based Access Control (RBAC)** using Spatie Laravel Permission
- Dynamic role creation and permission management
- Permission matrix interface for role assignment
- Individual user permission override system
- Single-device login enforcement
- Online status tracking with auto-logout after 30 minutes of inactivity
- Session management and activity tracking

### 👥 User Management
- **CONSEC Account Management** - Full CRUD for CONSEC accounts with individual permission control
- **Board Member Management** - Complete management system for board members
- **Pending Registrations** - Review and approve/disapprove new user registrations
- Multi-step registration forms with PSGC (Philippine Standard Geographic Code) address fields
- Profile picture upload and management
- Account activation/deactivation with audit logging

### 📄 Document Management
- **Board Resolutions** - Create, edit, view, and manage board resolutions with version history
- **Board Regulations** - Complete CRUD with versioning and change notes
- **Board Issuances** - Public-facing page displaying both resolutions and regulations
- PDF document viewer with full-screen modal
- Document history tracking with version comparison
- Change notes for document edits

### 🏛️ Government Agencies
- Government agency management with CRUD operations
- Bulk operations (activate, deactivate, delete)
- Status toggle functionality
- DataTables with search and filtering

### 📚 Media Library
- Drag & drop file upload
- Support for images, PDFs, audio, and video files
- File preview and download
- Attachment details modal
- PDF preview with download and open in new tab options

### 📊 Audit Trail
- Comprehensive audit logging for all system actions
- Searchable audit logs with DataTables
- PDF export with filtered results
- Detailed action tracking (create, update, delete, login, logout, etc.)
- IP address and user agent tracking

### 🔔 Notifications System
- In-app notification system
- Real-time notification badges
- Notification dropdown in admin header
- Unread notification count
- Notification filtering (all, unread, read)
- Auto-notification for pending registrations

### 💬 Messaging & Chat
- Real-time chat popup interface
- Image sharing with full-screen viewer
- Zoom and pan functionality for images
- Download images from chat
- Online status indicators
- Voice clip support (UI ready)

### 📋 Roles & Permissions
- Dynamic role creation
- Permission matrix interface with expand/collapse categories
- Role-based permission assignment
- Individual user permission management
- Permission categories:
  - User Management
  - Board Member Management
  - CONSEC Account Management
  - Board Resolutions
  - Board Regulations
  - Government Agencies
  - Media Library
  - Audit Logs
  - Roles & Permissions
  - Content Management
  - Attendance Confirmation
  - Reference Materials
  - Request for Inclusion in the Agenda
  - Report Generation
  - Referendum

### 📱 Responsive Design
- Fully responsive across all devices (mobile, tablet, desktop)
- Mobile-first approach
- Touch-friendly interfaces
- Responsive DataTables
- Mobile-optimized navigation
- Adaptive layouts for all screen sizes

### 🎨 UI/UX Features
- Modern, clean design with brand colors
- Smooth animations and transitions
- Dropdown action menus for tables
- Multi-step forms with progress indicators
- Tooltips for action buttons
- Loading states and feedback
- Consistent branding throughout

## 📋 Table of Contents

1. [System Workflow](#1-system-workflow)
2. [Database Structure](#2-database-structure)
3. [Pages & Routes](#3-pages--routes)
4. [Design Guidelines](#4-design-guidelines)
5. [Installation](#installation)

---

## 1. SYSTEM WORKFLOW

### System Flowchart

```mermaid
flowchart TD
    Start([User Visits Portal]) --> Landing[Landing Page]
    Landing --> AuthChoice{Authentication}
    
    AuthChoice -->|Login| Login[Login Form]
    AuthChoice -->|Register| Register[Registration Form]
    
    Login --> ValidateCreds{Validate Credentials}
    ValidateCreds -->|Invalid| LoginError[Show Error]
    LoginError --> Login
    ValidateCreds -->|Valid| CheckRole{Check Role & Permissions}
    
    Register --> FillForm[Fill Personal & Organization Details]
    FillForm --> PSGC[Multi-step Form with PSGC Address]
    PSGC --> PendingStatus[Status: Pending]
    PendingStatus --> AdminReview[Admin Approval Required]
    AdminReview --> Approve{Approve?}
    Approve -->|Yes| ActivateAccount[Activate Account]
    Approve -->|No| DeleteAccount[Delete Account]
    ActivateAccount --> CheckRole
    
    CheckRole -->|Admin| AdminDash[Admin Dashboard]
    CheckRole -->|User/CONSEC| UserDash[User Dashboard]
    
    AdminDash --> AdminFeatures{Admin Features}
    AdminFeatures --> UserMgmt[User Management]
    AdminFeatures --> DocMgmt[Document Management]
    AdminFeatures --> RoleMgmt[Role & Permission Management]
    AdminFeatures --> AuditLogs[Audit Logs]
    AdminFeatures --> MediaLib[Media Library]
    AdminFeatures --> GovAgencies[Government Agencies]
    
    UserMgmt --> CONSEC[CONSEC Accounts]
    UserMgmt --> BoardMembers[Board Members]
    UserMgmt --> PendingReg[Pending Registrations]
    
    DocMgmt --> Resolutions[Board Resolutions]
    DocMgmt --> Regulations[Board Regulations]
    Resolutions --> VersionHistory[Version History]
    Regulations --> VersionHistory
    
    RoleMgmt --> CreateRole[Create/Edit Roles]
    RoleMgmt --> PermMatrix[Permission Matrix]
    
    UserDash --> UserFeatures{User Features}
    UserFeatures --> ViewAnnounce[View Announcements]
    UserFeatures --> ViewCalendar[Activities Calendar]
    UserFeatures --> Chat[Chat Facility]
    UserFeatures --> MeetingNotices[Meeting Notices]
    UserFeatures --> BoardIssuances[Board Issuances]
    
    BoardIssuances --> FilterDocs[Filter by Type]
    FilterDocs --> ViewPDF[View/Download PDF]
    
    AdminDash --> AuditSystem[Audit System]
    UserDash --> AuditSystem
    UserMgmt --> AuditSystem
    DocMgmt --> AuditSystem
    RoleMgmt --> AuditSystem
    
    AuditSystem --> LogAction[Log Action]
    LogAction --> StoreData[Store User, IP, Timestamp]
    StoreData --> DisplayLogs[Display in Audit Logs]
    
    style Start fill:#055498,stroke:#123a60,color:#fff
    style AdminDash fill:#CE2028,stroke:#8b1519,color:#fff
    style UserDash fill:#055498,stroke:#123a60,color:#fff
    style AuditSystem fill:#F9FAFB,stroke:#0F172A,stroke-width:2px
```

### A. User Access Flow

#### Landing Page Structure
- Public homepage with announcements and activities calendar
- Login and registration access
- Public sections: Announcements, Activities Calendar, Vision & Mission, About Us, Contact Us

#### Authentication Flow
```
Start
  └─► Landing Page
        ├─► Login
        │      ├─► Validate Credentials
        │      ├─► Check Role & Permissions
        │      ├─► Track Activity
        │      └─► Redirect to Dashboard (User/Admin)
        └─► Register
               ├─► Fill Personal & Organization Details
               ├─► Multi-step Form with PSGC Address
               ├─► Status: Pending
               └─► Admin Approval Required
```

### B. Dashboard Flow

#### User Dashboard
```
Dashboard
  ├─► Announcements (view, read more)
  ├─► Calendar (events, meetings, schedules)
  ├─► Chat Facility (direct messages)
  ├─► Meeting Notices → View → Link / Attached Files
  └─► Board Issuances → View / Download (Resolutions & Regulations)
```

#### Admin Dashboard
```
Admin Dashboard
  ├─► Statistics & Overview
  ├─► Quick Actions
  ├─► Recent Activities
  └─► System Notifications
```

### C. Document Management Flow

```
Admin → Create/Edit Document
   ├─► Board Resolution
   │      ├─► Fill Details (Title, Number, Date, Version)
   │      ├─► Upload PDF
   │      ├─► Save → Create Version History
   │      └─► Publish
   │
   └─► Board Regulation
          ├─► Fill Details (Title, Number, Effective Date, Version)
          ├─► Upload PDF
          ├─► Save → Create Version History
          └─► Publish

User → View Board Issuances
   ├─► Filter by Type (Resolution/Regulation)
   ├─► View Details
   ├─► Download PDF
   └─► View in Modal
```

### D. User Management Flow

```
Admin → User Management
   ├─► CONSEC Accounts
   │      ├─► Create/Edit Account
   │      ├─► Set Individual Permissions
   │      ├─► Activate/Deactivate
   │      └─► View Profile
   │
   ├─► Board Members
   │      ├─► Create/Edit Account
   │      ├─► Assign Government Agency
   │      ├─► Set Representative Type
   │      ├─► Activate/Deactivate
   │      └─► View Profile
   │
   └─► Pending Registrations
          ├─► Review Registration Details
          ├─► Approve → Activate Account
          └─► Disapprove → Delete Account
```

### E. Role & Permission Management Flow

```
Admin → Role & Permission Manager
   ├─► Roles Tab
   │      ├─► Create/Edit Role
   │      ├─► Assign Permissions
   │      └─► Delete Role
   │
   └─► Permissions Matrix Tab
          ├─► View All Permissions by Category
          ├─► Assign/Revoke Permissions per Role
          └─► Expand/Collapse Categories
```

### F. Audit Trail Flow

```
System Actions → Audit Logger
   ├─► Log Action (Create, Update, Delete, Login, etc.)
   ├─► Store User, IP, URL, Method, Timestamp
   └─► Display in Audit Logs Page
          ├─► Search & Filter
          ├─► View Details
          └─► Export to PDF (with filters)
```

---

## 2. DATABASE STRUCTURE

### A. Main Entities

- Users (with UUID primary keys)
- Roles & Permissions (Spatie Laravel Permission)
- Board Resolutions & Versions
- Board Regulations & Versions
- Government Agencies
- Media Library
- Audit Logs
- Notifications
- Chats/Messages

### B. Key Tables

#### 1. users
```sql
id (uuid, primary key)
first_name
last_name
middle_initial
email (unique)
username (unique)
password
privilege (admin, consec, user)
government_agency_id (foreign key)
representative_type (Board Member, Authorized Representative)
pre_nominal_title (Mr., Ms.)
post_nominal_title (Sr., Jr., I, II, III, Others)
post_nominal_title_custom
designation
sex
gender
birth_date
profile_picture
mobile
landline
office_address (JSON - PSGC fields)
home_address (JSON - PSGC fields)
is_active (boolean)
status (pending, approved, disapproved)
last_activity (timestamp)
is_online (boolean)
session_id
revoked_permissions (JSON)
created_at
updated_at
```

#### 2. roles (Spatie)
```sql
id
name (unique)
guard_name
created_at
updated_at
```

#### 3. permissions (Spatie)
```sql
id
name (unique)
guard_name
created_at
updated_at
```

#### 4. model_has_roles (Spatie)
```sql
role_id
model_type
model_id
```

#### 5. role_has_permissions (Spatie)
```sql
permission_id
role_id
```

#### 6. model_has_permissions (Spatie)
```sql
permission_id
model_type
model_id
```

#### 7. board_resolutions (official_documents)
```sql
id
resolution_number
title
description
version
effective_date
approved_date
pdf_file_path
change_notes (nullable)
created_by
updated_by
created_at
updated_at
```

#### 8. official_document_versions
```sql
id
official_document_id
version
effective_date
approved_date
pdf_file_path
change_notes (nullable)
created_at
```

#### 9. board_regulations
```sql
id
regulation_number
title
description
version
effective_date
approved_date
pdf_file_path
created_by
updated_by
created_at
updated_at
```

#### 10. board_regulation_versions
```sql
id
board_regulation_id
version
effective_date
approved_date
pdf_file_path
change_notes (nullable)
created_at
```

#### 11. government_agencies
```sql
id
name
code
description
is_active
created_at
updated_at
```

#### 12. media_library
```sql
id
file_name
original_name
file_type
file_size
file_path
mime_type
uploaded_by
created_at
updated_at
```

#### 13. audit_logs
```sql
id
user_id (foreign key)
action (string)
description (text)
model_type (nullable)
model_id (nullable)
ip_address
url
method
metadata (JSON)
created_at
```

#### 14. notifications
```sql
id
user_id (foreign key)
type (string)
title (string)
message (text)
data (JSON)
is_read (boolean)
read_at (timestamp)
created_at
updated_at
```

---

## 3. PAGES & ROUTES

### A. Public Pages

- `/` - Landing Page (Public Announcements, Activities Calendar)
- `/login` - Login Page
- `/register` - Registration Page (Multi-step form)
- `/forgot-password` - Forgot Password

### B. User Dashboard Pages

- `/dashboard` - User Dashboard (redirects to landing)
- `/profile/edit` - Edit Profile
- `/profile/view/{id}` - View Profile
- `/notifications` - Notifications Center
- `/messages` - Messages / Chat Page
- `/board-issuances` - Board Resolutions & Regulations (Public View)

### C. Admin Pages

#### Dashboard
- `/admin/dashboard` - Admin Dashboard

#### User Management
- `/admin/consec` - CONSEC Account Management
  - `/admin/consec/create` - Create CONSEC Account
  - `/admin/consec/{id}/edit` - Edit CONSEC Account
  - `/admin/consec/{id}/permissions` - Manage Individual Permissions
  - `/admin/consec/{id}/toggle-status` - Activate/Deactivate

- `/admin/board-members` - Board Member Management
  - `/admin/board-members/create` - Create Board Member
  - `/admin/board-members/{id}/edit` - Edit Board Member
  - `/admin/board-members/{id}/toggle-status` - Activate/Deactivate

- `/admin/pending-registrations` - Pending Registrations
  - `/admin/pending-registrations/{id}` - View Registration Details
  - `/admin/pending-registrations/{id}/approve` - Approve Registration
  - `/admin/pending-registrations/{id}/disapprove` - Disapprove Registration

#### Document Management
- `/admin/board-resolutions` - Board Resolutions Management
  - `/admin/board-resolutions/create` - Create Resolution
  - `/admin/board-resolutions/{id}/edit` - Edit Resolution
  - `/admin/board-resolutions/{id}/history` - View Version History
  - `/admin/board-resolutions/{id}` - Delete Resolution

- `/admin/board-regulations` - Board Regulations Management
  - `/admin/board-regulations/create` - Create Regulation
  - `/admin/board-regulations/{id}/edit` - Edit Regulation
  - `/admin/board-regulations/{id}/history` - View Version History
  - `/admin/board-regulations/{id}` - Delete Regulation

#### System Management
- `/admin/government-agencies` - Government Agencies Management
  - `/admin/government-agencies/create` - Create Agency
  - `/admin/government-agencies/{id}/edit` - Edit Agency
  - `/admin/government-agencies/bulk-delete` - Bulk Delete
  - `/admin/government-agencies/bulk/activate` - Bulk Activate
  - `/admin/government-agencies/bulk/deactivate` - Bulk Deactivate

- `/admin/media-library` - Media Library
  - `/admin/media-library/upload` - Upload Media
  - `/admin/media-library/{id}` - View/Download Media
  - `/admin/media-library/{id}/update` - Update Media Details
  - `/admin/media-library/bulk-delete` - Bulk Delete

- `/admin/roles` - Role & Permission Management
  - `/admin/roles/create` - Create Role
  - `/admin/roles/{id}/edit` - Edit Role
  - `/admin/roles/{id}/update-permission` - Update Role Permissions

- `/admin/audit-logs` - Audit Logs
  - `/admin/audit-logs/export-pdf` - Export to PDF (with filters)

- `/admin/notifications` - Admin Notifications Page

- `/admin/profile/edit` - Admin Profile Edit

---

## 4. DESIGN GUIDELINES

### Color Palette (Brand Colors)

```css
Primary Blue: #055498
Secondary Blue: #123a60
Accent Red: #CE2028
Background Light: #F9FAFB
Background Dark: #0F172A
Text: #0A0A0A / #F1F5F9
```

### Layout Style

- Clean, modern design
- Rounded cards (border-radius: 8-16px)
- Consistent spacing (8px scale)
- Subtle shadows and hover effects
- Sticky header with dropdown menus
- Responsive grid layouts

### Components

- **DataTables** - Enhanced tables with search, filter, pagination
- **Multi-step Forms** - Progress indicators and step navigation
- **Modals** - Full-screen PDF viewer, image viewer, confirmation dialogs
- **Dropdown Menus** - Action buttons, notifications, messages
- **Tooltips** - Helpful hints on action buttons
- **Badges** - Status indicators, notification counts
- **Cards** - Content containers with consistent styling

### Responsive Design Principles

- Mobile-first approach
- Touch-friendly targets (minimum 44px)
- Flexible grid layouts
- Responsive typography
- Scrollable tables on mobile
- Adaptive navigation

---

## 🛠️ Installation

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL 8.0+
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

8. **Seed database (optional)**
   ```bash
   php artisan db:seed
   # Or specific seeders:
   php artisan db:seed --class=RolePermissionSeeder
   ```

9. **Build assets**
   ```bash
   npm run dev
   # or for production
   npm run build
   ```

10. **Start development server**
    ```bash
    php artisan serve
    ```

11. **Set up scheduler (for auto-logout)**
    Add to crontab:
    ```bash
    * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
    ```

12. **Visit**
    - Application: `http://localhost:8000`
    - Admin Dashboard: `http://localhost:8000/admin/dashboard`

### Default Admin Account

After seeding, you can create an admin account or use the default:
- **Email:** admin@admin.com
- **Password:** (set during registration or use password reset)

---

## 🔒 Security Features

- **Role-Based Access Control** - Granular permission system
- **Single-Device Login** - Prevents concurrent sessions
- **Activity Tracking** - Monitors user activity and auto-logout
- **Audit Logging** - Comprehensive action tracking
- **CSRF Protection** - Laravel built-in CSRF tokens
- **Password Hashing** - Bcrypt password encryption
- **Session Management** - Secure session handling
- **Input Validation** - Server-side validation for all forms

---

## 📚 Additional Documentation

- [MySQL Setup Guide](README_MYSQL.md)
- [Git Push Instructions](GIT_PUSH_INSTRUCTIONS.md)

---

## 🧪 Testing

### Key Functionality to Test

1. **Authentication**
   - Login/Logout
   - Registration and approval workflow
   - Password reset

2. **User Management**
   - Create/Edit CONSEC accounts
   - Create/Edit Board Members
   - Approve/Disapprove pending registrations
   - Permission management

3. **Document Management**
   - Create/Edit Board Resolutions
   - Create/Edit Board Regulations
   - Version history
   - PDF viewing and download

4. **RBAC**
   - Role creation and assignment
   - Permission matrix updates
   - Individual permission overrides

5. **Audit Trail**
   - Action logging
   - Search and filter
   - PDF export

---

## 📝 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 🤝 Contributing

Thank you for considering contributing to the boardsmemberportal project!

---

## 🔒 Security Vulnerabilities

If you discover a security vulnerability, please send an e-mail to the project maintainers. All security vulnerabilities will be promptly addressed.

---

## 📞 Support

For support, please contact the development team or create an issue in the repository.

---

## 👨‍💻 Developer

**Rolan Mondares Benavidez Jr**

- **Company:** Landogz Web Solutions
- **Email:** rolan.benavidez@gmail.com
- **Phone:** 09387077940
- **Facebook:** [https://www.facebook.com/landogz](https://www.facebook.com/landogz)
