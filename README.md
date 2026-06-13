# IT Helpdesk Ticketing System

A full-featured web-based helpdesk ticketing system built with PHP, MySQL, and Bootstrap. Supports role-based access for Admin, Technician, and Employee users with real-time notifications, performance tracking, and customizable themes.

## Features

### Role-Based Access Control
- **Admin** - Full system access: manage users, departments, categories, view reports, and all tickets
- **Technician** - View and update assigned tickets, add comments, change ticket status
- **Employee** - Create tickets, view own tickets, reopen resolved tickets

### Ticket Management
- Create, view, update, and close support tickets
- Ticket number auto-generation (TKT-YYYYMMDD-XXXX)
- Priority levels: Low, Medium, High, Critical
- Status workflow: Created > Assigned > In Progress > Resolved > Closed
- File attachments with extension validation (JPG, PNG, GIF, PDF, DOC, DOCX, TXT, ZIP)
- Comment system with role badges and timestamps
- Ticket reassignment between technicians

### Dashboard Analytics
- **Admin Dashboard** - Total tickets, open/pending/resolved/closed counts, monthly trend chart, status distribution with percentages, technician performance metrics, recent tickets
- **Technician Dashboard** - Assigned tickets overview, priority/status pie charts, recent tickets
- **User Dashboard** - Personal ticket stats, status doughnut chart, quick actions

### Notifications
- Real-time notification bell with unread count badge
- Auto-polling every 30 seconds for new notifications
- Mark all as read / individual mark as read
- Notifications for: assignment, status changes, comments, resolves

### User Profile
- Upload and manage profile photo (JPG, PNG, GIF, max 2MB)
- Photo displayed in navbar avatar and users table
- Fallback to initials when no photo is set
- Edit personal information (name, email, phone)
- Change password with current password verification

### Theming
- 10 preset theme colors + custom color picker
- Light and Dark mode
- Compact mode option
- Per-user theme persistence via database
- Real-time preview in settings page

### Admin Management
- **User Management** - Create, edit, activate/deactivate, delete users with profile photos
- **Department Management** - CRUD operations for departments
- **Category Management** - CRUD operations for ticket categories
- **Reports & Analytics** - Date range filters, status/priority/category/department breakdowns, Chart.js visualizations, printable reports

### Security
- CSRF protection on all forms
- Prepared statements for all SQL queries (PDO)
- Password hashing with `password_hash()` (bcrypt)
- Session-based authentication with 30-minute timeout
- Role-based access control middleware
- File upload extension whitelist validation

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.1 (Native, no framework) |
| Database | MySQL 8.0 |
| Frontend | HTML5, CSS3, JavaScript |
| CSS Framework | Bootstrap 5.3.0 (CDN) |
| Charts | Chart.js (CDN) |
| Font | Inter (Google Fonts) |
| Icons | Inline SVG |
| Server | Laragon (Apache + MySQL) |

## Project Structure

```
helpdesk_system/
├── admin/                      # Admin pages
│   ├── dashboard.php           # Admin dashboard with analytics
│   ├── tickets.php             # View and manage all tickets
│   ├── view_ticket.php         # Ticket detail view
│   ├── users.php               # User management (CRUD)
│   ├── departments.php         # Department management (CRUD)
│   ├── categories.php          # Category management (CRUD)
│   └── reports.php             # Reports and analytics
├── technician/                 # Technician pages
│   ├── dashboard.php           # Technician dashboard
│   ├── tickets.php             # Assigned tickets list
│   └── view_ticket.php         # Ticket detail view
├── user/                       # Employee pages
│   ├── dashboard.php           # Employee dashboard
│   ├── tickets.php             # My tickets list
│   ├── view_ticket.php         # Ticket detail view
│   └── create_ticket.php       # Create new ticket
├── assets/
│   ├── css/
│   │   └── style.css           # Custom styles (1200+ lines)
│   ├── js/
│   │   └── main.js             # JavaScript (tooltips, notifications, shortcuts)
│   └── images/                 # Static images
├── config/
│   └── database.php            # DB config, helper functions, CSRF, session
├── includes/
│   ├── header.php              # HTML head, theme CSS variables, meta tags
│   ├── navbar.php              # Role-based navigation, notifications, avatar
│   ├── footer.php              # Footer, modals, flash messages
│   └── auth.php                # Authentication, role checks, session timeout
├── uploads/
│   └── profile/                # User profile photos
├── login.php                   # Login page
├── logout.php                  # Logout handler
├── index.php                   # Root redirect to login
├── profile.php                 # User profile with photo upload
├── settings.php                # Theme and notification preferences
├── notifications.php           # Full notifications list
├── mark_notifications_read.php # Mark notifications as read
├── get_notification_count.php  # AJAX notification count endpoint
├── forgot_password.php         # Password reset request
├── reset_password.php          # Password reset form
└── _seed_dummy_data.php        # Database seeder (20 users, 16 tickets)
```

## Database Schema

| Table | Description |
|-------|-------------|
| `roles` | Admin, Technician, Employee |
| `departments` | IT, HR, Finance, Administration, Operations |
| `ticket_categories` | Network, Hardware, Printer, Software, Account, Email, Internet, Others |
| `users` | User accounts with profile_photo column |
| `tickets` | Support tickets with status, priority, assignment |
| `ticket_comments` | Comment threads on tickets |
| `ticket_attachments` | File attachments for tickets |
| `notifications` | User notifications for assignments and updates |
| `activity_logs` | Audit trail for user actions |
| `user_settings` | Per-user theme preferences |

## Login Credentials

| Role | Username | Password |
|------|----------|----------|
| Admin | admin | admin123 |
| Technician | tech_carlos | password123 |
| Technician | tech_maria | password123 |
| Technician | tech_james | password123 |
| Technician | tech_anita | password123 |
| Employee | juan_d | password123 |
| Employee | anna_m | password123 |
| Employee | robert_c | password123 |
| Employee | rachel_b | password123 |
| Employee | kevin_t | password123 |

## Setup

1. Place project in `C:\laragon\www\helpdesk\helpdesk_system\`
2. Ensure Laragon Apache and MySQL are running
3. Import database or run `_seed_dummy_data.php` to populate tables
4. Access at `http://localhost/helpdesk/helpdesk_system/login.php`

## License

Aldrian C. Loberiano - Devloped as a comprehensive IT helpdesk ticketing system with advanced features and security best practices.
