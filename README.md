# KAWAII Plushies - Online Shopping Platform

## 📑 Table of Contents

* [Project Overview](#-project-overview)
* [Demo Accounts](#-demo-accounts)
* [Technology Stack](#-technology-stack)
* [Environment Setup](#-environment-setup)
* [Features](#-features)
* [User Roles](#-user-roles)
* [Installation](#-installation)
* [System Architecture](#-system-architecture)
* [Security Features](#-security-features)
* [Future Improvements](#-future-improvements)
* [License](#-license)
* [Authors](#-authors)

---

## 🎀 Project Overview

**KAWAII Plushies** is a comprehensive PHP e-commerce platform designed for plushie retail, featuring multi-role access control, complete shopping functionality, and robust administrative tools. This system provides a complete online shopping experience from product browsing to order fulfillment.

---

## 🔑 Demo Accounts

### User/Member
**Email**: tongjlai1109@gmail.com  
**Password**: 123123123  

### Admin
**Email**: kawaii@gmail.com  
**Password**: 123123123  

### Staff
**Email**: Julie@kawaii.com  
**Password**: 123123123  

---

## 🛠️ Technology Stack

* **PHP** - Server-side scripting language
* **MySQL** - Database management system
* **HTML/CSS/JavaScript** - Frontend technologies
* **Bootstrap** - Responsive UI framework
* **PDO** - Database connectivity

---

## ⚙️ Environment Setup

### Prerequisites

Make sure the following are installed:

* PHP 7.4+ with PDO MySQL support
* MySQL 5.7+ or MariaDB 10.2+
* Apache/Nginx web server
* SMTP server for email functionality

### Installation Steps

1. Clone or download the project
2. Import database schema from `database.sql`
3. Configure database credentials in `_base.php`
4. Set up SMTP settings in `lib/SMTP.php`
5. Adjust file permissions for upload directories

---

## ✨ Features

### 🛍️ Customer Experience
* **Product Catalog** - Category-based browsing with search and filtering
* **Shopping Cart** - Persistent cart management across sessions
* **Wishlist** - Save favorite items for future purchases
* **Order Tracking** - Complete order history and status monitoring
* **Secure Checkout** - Multiple payment methods with validation

### 💼 Administrative Control
* **Product Management** - Full CRUD operations with image galleries
* **Category System** - Hierarchical organization (Type → Name)
* **Order Processing** - End-to-end order lifecycle management
* **User Management** - Member approval and status control
* **Inventory System** - Real-time stock tracking and management
* **Voucher Engine** - Discount codes with limits and expiry dates

---

## 👤 User Roles

### Customers/Members
* Browse and search products
* Add items to cart and wishlist
* Complete purchases and track orders
* Manage personal profiles

### Staff
* Process orders and update status
* Manage product inventory
* Handle customer inquiries
* Basic administrative tasks

### Administrators
* Full system control and configuration
* User management and approval
* Category and product management
* System analytics and reporting

---

## 🚀 Installation

### Setup Process

1. **Database Setup**:
   ```sql
   mysql -u username -p database_name < database.sql
   ```

2. **Configuration**:
   * Update database credentials in `_base.php`
   * Configure SMTP settings in `lib/SMTP.php`
   * Set proper file permissions for uploads

3. **Web Server**:
   * Point web server to project directory
   * Ensure mod_rewrite is enabled for clean URLs

4. **Testing**:
   * Use provided demo accounts to test functionality
   * Verify email system is working

---

## 🏗️ System Architecture

### Core Components
* **`_base.php`** - Centralized database connection and essential functions
* **Role-Based Templates** - Separate interfaces for users and administrators
* **Pagination System** - Efficient data loading with `SimplePager` class
* **File Management** - Secure image uploads for products and profiles

### Database Design
* **User Management** - Role-based permissions across Members, Staff, Admins
* **Product Catalog** - Categories, images, inventory, and tagging system
* **Order Pipeline** - Complete order→payment→delivery tracking
* **Security Framework** - Token management for authentication

---

## 🔒 Security Features

### Authentication & Authorization
* **Multi-Role Access** - Members, Staff, and Administrators with tailored permissions
* **Secure Authentication** - SHA1 password hashing with session management
* **Input Validation** - Comprehensive sanitization and security measures
* **Email Verification** - OTP-based account confirmation system

### Data Protection
* **SQL Injection Prevention** - PDO prepared statements
* **XSS Protection** - Input sanitization and output encoding
* **Session Security** - Secure session management
* **File Upload Security** - Type validation and size restrictions

---

## 📊 System Modules

### Product Management
* **Product Operations** - Add, edit, and manage product listings
* **Image Gallery** - Multi-image uploads with cover image selection
* **Inventory Control** - Stock quantity validation and tracking
* **Status Management** - Activate/delist products as needed

### Order & Member Management
* **Order Processing** - Update status through Pending→Shipped→Delivered
* **Member Approval** - Review and approve pending registrations
* **Order Tracking** - Comprehensive order information with products
* **Status Control** - Block/unblock member accounts as needed

### Category Management
* **Browse Categories** - Filter by type with search functionality
* **Category Operations** - Add and update categories with validation
* **Product Assignment** - Manage category-product relationships

---

## 🚀 Future Improvements

* **Mobile Application** - Native iOS and Android apps
* **Payment Gateway Integration** - Stripe, PayPal, etc.
* **Advanced Analytics** - Sales reporting and customer insights
* **Multi-language Support** - Internationalization
* **API Development** - RESTful API for third-party integrations

---

## 📄 License

This project is created for **academic purposes**.  
Not intended for commercial distribution.

---

## 👥 Authors

* Developed as a group project for university coursework
* Using modern web development practices and e-commerce best practices

---

*Group project developed for web development coursework*
