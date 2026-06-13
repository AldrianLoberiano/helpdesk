# IT Helpdesk Ticketing Management System (Pure PHP + MySQL)

## Tech Stack
- PHP (Native / Core PHP)
- MySQL
- HTML5
- CSS3 / Bootstrap
- JavaScript
- AJAX (Optional)
- Apache (XAMPP/WAMP)

## System Objective
Develop a modern, scalable, secure, and responsive IT Helpdesk Ticketing System using **Pure PHP and MySQL only** (without frameworks).

This system should help schools, companies, or government offices manage IT-related concerns digitally.

---

## User Roles

### 1. Admin
Responsibilities:
- Full system management
- Manage users
- Manage departments
- Assign technicians
- Configure ticket categories
- Generate reports
- View analytics

### 2. Technician
Responsibilities:
- View assigned tickets
- Update ticket status
- Add comments
- Resolve tickets
- Upload files

### 3. Employee/User
Responsibilities:
- Submit ticket
- Track progress
- Upload attachments
- Reply to technician
- Rate service

---

## Authentication Module

### Features
- Login
- Logout
- Forgot Password
- Session Management
- Remember Me
- Password Hashing
- Role-Based Access Control

### Security
- Prepared Statements (PDO/MySQLi)
- SQL Injection Prevention
- Password Hashing
- Input Validation
- CSRF Protection
- Session Protection

---

## Ticket Lifecycle

Created → Pending → Assigned → In Progress → Resolved → Closed

---

## Ticket Management Features
- Create Ticket
- Edit Ticket
- Delete Ticket
- Assign Technician
- Reassign Technician
- Update Status
- Upload Attachments
- Ticket History
- Advanced Search
- Filtering

### Priorities
- Low
- Medium
- High
- Critical

### Categories
- Network Issues
- Hardware Issues
- Printer Problems
- Software Installation
- Account Access
- Email Problems
- Internet Connectivity
- Others

---

## Dashboard Features
- Total Tickets
- Pending Tickets
- Resolved Tickets
- Open Tickets
- Technician Performance
- Monthly Reports

---

## Database Tables
- users
- roles
- departments
- tickets
- ticket_categories
- ticket_comments
- ticket_attachments
- feedbacks
- notifications
- activity_logs

---

## Suggested Project Structure

```text
project/
│── config/
│   └── database.php
│
│── admin/
│── technician/
│── user/
│
│── includes/
│   ├── header.php
│   ├── footer.php
│   ├── navbar.php
│   └── auth.php
│
│── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
│── uploads/
│
│── login.php
│── register.php
│── dashboard.php
│── index.php
```

## Deliverables
1. Full Pure PHP project structure
2. MySQL database schema
3. Authentication system
4. CRUD operations
5. Ticket management
6. Reports
7. Dashboard
8. Documentation
