# Vportal - EV Showroom Platform

## Overview
Vportal is a comprehensive EV (Electric Vehicle) Showroom Platform with e-commerce, billing, offers, and a multi-level admin system. Built with PHP and MySQL, designed to run on XAMPP locally.

## Project Structure
```
/
├── index.php              # Homepage
├── bikes.php              # EV bikes listing with filters
├── bike-detail.php        # Individual bike details
├── login.php              # User login
├── register.php           # User registration
├── dashboard.php          # User dashboard
├── preorder.php           # Pre-order system
├── about.php              # About page
├── contact.php            # Contact page
├── logout.php             # Logout handler
│
├── include/
│   ├── config.php         # Database config & helper functions
│   ├── header.php         # Common header with navigation
│   └── footer.php         # Common footer
│
├── admin/                 # Showroom Admin Panel
│   ├── index.php          # Admin dashboard
│   ├── login.php          # Admin login
│   └── logout.php         # Admin logout
│
├── superadmin/            # Super Admin Panel
│   ├── index.php          # Super admin dashboard
│   ├── login.php          # Super admin login
│   └── logout.php         # Super admin logout
│
├── ajax/
│   └── wishlist.php       # Wishlist AJAX handlers
│
├── uploads/               # File uploads directory
│
└── DATABASE.sql           # Complete database schema
```

## System Hierarchy (3 Levels)
1. **Super Admin** - System owner, manages all admins, global settings, billing templates
2. **Admin** - Showroom owner, manages products, orders, customers, invoices
3. **User** - Customer, browses bikes, pre-orders, tracks orders

## Tech Stack
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Backend**: PHP 8.x
- **Database**: MySQL/MariaDB
- **Icons**: Font Awesome 6
- **Alerts**: SweetAlert2

## Local Setup (XAMPP)

### 1. Install XAMPP
Download and install XAMPP from https://www.apachefriends.org/

### 2. Copy Project Files
Copy this entire project folder to `C:\xampp\htdocs\ev-showroom\`

### 3. Start Services
Open XAMPP Control Panel and start:
- Apache
- MySQL

### 4. Create Database
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Create a new database named `ev_showroom`
3. Import `DATABASE.sql` file

### 5. Configure Database Connection
Edit `include/config.php`:
```php
$db_host = "localhost";
$db_name = "ev_showroom";
$db_user = "root";
$db_pass = "";  // Your MySQL password if set
```

### 6. Access the Application
- Website: http://localhost/ev-showroom/
- Admin Panel: http://localhost/ev-showroom/admin/
- Super Admin: http://localhost/ev-showroom/superadmin/

## Default Login Credentials
- **Super Admin**: superadmin@evshowroom.com / admin123

## Features
- Responsive EV-themed UI with dark mode
- Product catalog with filters (price, range, category)
- Pre-order system
- User dashboard with order tracking
- Wishlist functionality
- Admin panel for showroom management
- Super Admin panel for system-wide control
- Invoice generation
- Offer/notification system
- Multi-showroom support

## Database Tables
- super_admin, admin, users
- products, categories, inventory
- preorders, orders, order_items
- invoices, payments
- offers, notifications
- wishlist, banners, themes
- global_settings, system_logs, admin_activity
- permissions, support_tickets

## Notes
- This project is designed for XAMPP/MySQL environment
- Default password hash uses PHP's password_hash() with PASSWORD_DEFAULT
- All user sessions are managed via PHP sessions
- GST/tax calculations follow Indian billing standards
