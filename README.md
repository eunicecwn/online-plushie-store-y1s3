# KAWAII Plushies - Online Shopping Platform

## 🎀 Demo Accounts

### User/Member
**Email**: tongjlai1109@gmail.com  
**Password**: 123123123  

### Admin
**Email**: kawaii@gmail.com  
**Password**: 123123123  

### Staff
**Email**: Julie@kawaii.com  
**Password**: 123123123  

## 🧸 Overview

A comprehensive PHP e-commerce platform designed for plushie retail, featuring multi-role access control, complete shopping functionality, and robust administrative tools.

## ✨ Key Features

### 🛍️ Customer Experience
- **Product Catalog**: Category-based browsing with search and filtering
- **Shopping Cart**: Persistent cart management across sessions
- **Wishlist**: Save favorite items for future purchases
- **Order Tracking**: Complete order history and status monitoring
- **Secure Checkout**: Multiple payment methods with validation

### 💼 Administrative Control
- **Product Management**: Full CRUD operations with image galleries
- **Category System**: Hierarchical organization (Type → Name)
- **Order Processing**: End-to-end order lifecycle management
- **User Management**: Member approval and status control
- **Inventory System**: Real-time stock tracking and management
- **Voucher Engine**: Discount codes with limits and expiry dates

## 🛠️ Technical Architecture

### Database Design
- **User Management**: Role-based permissions across Members, Staff, Admins
- **Product Catalog**: Categories, images, inventory, and tagging system
- **Order Pipeline**: Complete order→payment→delivery tracking
- **Security Framework**: Token management for authentication

### Core Components
- **`_base.php`**: Centralized database connection and essential functions
- **Role-Based Templates**: Separate interfaces for users and administrators
- **Pagination System**: Efficient data loading with `SimplePager` class
- **File Management**: Secure image uploads for products and profiles

### Security Implementation
- **Multi-Role Access**: Members, Staff, and Administrators with tailored permissions
- **Secure Authentication**: SHA1 password hashing with session management
- **Input Validation**: Comprehensive sanitization and security measures
- **Email Verification**: OTP-based account confirmation system

## 📋 System Modules

### Product Management
- **Product Operations**: Add, edit, and manage product listings
- **Image Gallery**: Multi-image uploads with cover image selection
- **Inventory Control**: Stock quantity validation and tracking
- **Status Management**: Activate/delist products as needed

### Category Management
- **Browse Categories**: Filter by type with search functionality
- **Category Operations**: Add and update categories with validation
- **Product Assignment**: Manage category-product relationships
- **Status Control**: Activate/deactivate categories

### Order & Member Management
- **Order Processing**: Update status through Pending→Shipped→Delivered
- **Member Approval**: Review and approve pending registrations
- **Order Tracking**: Comprehensive order information with products
- **Status Control**: Block/unblock member accounts as needed

## 🚀 Installation

### Prerequisites
- PHP 7.4+ with PDO MySQL support
- MySQL 5.7+ or MariaDB 10.2+
- Apache/Nginx web server
- SMTP server for email functionality

### Setup Process
1. Import database schema from `database.sql`
2. Configure database credentials in `_base.php`
3. Set up SMTP settings in `lib/SMTP.php`
4. Adjust file permissions for upload directories

## 📊 Technical Features

### Performance Optimization
- Efficient pagination for large datasets
- Optimized database query structures
- Image optimization and caching strategies
- Responsive design for cross-device compatibility

### Business Logic
- Real-time inventory validation system
- Voucher application with usage tracking
- Complete order lifecycle management
- Automated email notification system

---

*Group project developed for university coursework* 
