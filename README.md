# Barangay Submission and Report System (DILG)

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange.svg)](https://mysql.com)
[![License](https://img.shields.io/badge/License-Government%20Use-green.svg)](LICENSE)

A comprehensive web-based platform for the Department of the Interior and Local Government (DILG) to streamline report submission and management processes for barangays across the Philippines.

## 🏛️ Overview

The **Barangay Submission and Report System** digitizes and modernizes the traditional paper-based reporting system, enabling real-time monitoring, efficient data management, and improved transparency in local government operations.

### Key Features

- 📊 **Multi-Role Dashboard System** - Admin, Facilitator, and Barangay interfaces
- 📋 **Comprehensive Report Management** - Weekly, Monthly, Quarterly, Semestral, and Annual reports
- 🔔 **Advanced Notification System** - Real-time alerts and email notifications
- 📈 **Analytics & Reporting** - Real-time statistics and performance metrics
- 🏢 **Organizational Structure** - Cluster management and facilitator assignment
- 🔐 **Security Features** - Role-based access control and file validation

## 🚀 Quick Start

### Prerequisites

- PHP 8.2 or higher
- MySQL 5.7 or higher
- Composer
- Node.js 16+
- Web server (Apache/Nginx)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/alexandernicolebravo/barangay-submission-report-system.git
   cd barangay-submission-report-system
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database configuration**
   ```bash
   # Update .env with your database credentials
   php artisan migrate
   php artisan db:seed
   ```

5. **Build assets**
   ```bash
   npm run build
   ```

6. **Start the application**
   ```bash
   php artisan serve
   ```

## 📚 Documentation

- **[Project Overview](PROJECT_OVERVIEW.md)** - Comprehensive project documentation
- **[Technical Documentation](TECHNICAL_DOCUMENTATION.md)** - Technical architecture and implementation details
- **[API Documentation](API_DOCUMENTATION.md)** - RESTful API reference
- **[Deployment Guide](DEPLOYMENT_GUIDE.md)** - Production deployment instructions
- **[User Manual](USER_MANUAL.md)** - User guide for all roles

## 🛠️ Technology Stack

- **Backend**: Laravel 11.x (PHP 8.2+)
- **Frontend**: Blade Templates, Bootstrap 5, jQuery
- **Database**: MySQL 5.7+
- **Real-time**: Pusher WebSockets
- **File Storage**: Laravel Storage

## 👥 User Roles

### 🔧 System Administrator
- Full system access and user management
- Report type configuration
- System analytics and announcements

### 👨‍💼 Cluster Facilitator
- Regional oversight and report review
- Barangay management and notifications
- Performance monitoring

### 🏘️ Barangay Official
- Report submission and file management
- Status tracking and deadline management
- Report resubmission capabilities

## 📊 System Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Barangay      │    │   Facilitator   │    │     Admin       │
│   Interface     │    │   Interface     │    │   Interface     │
└─────────┬───────┘    └─────────┬───────┘    └─────────┬───────┘
          │                      │                      │
          └──────────────────────┼──────────────────────┘
                                 │
                    ┌─────────────▼─────────────┐
                    │     Laravel Backend       │
                    │   (Controllers, Models)   │
                    └─────────────┬─────────────┘
                                 │
                    ┌─────────────▼─────────────┐
                    │      MySQL Database       │
                    │   (Reports, Users, etc.)  │
                    └───────────────────────────┘
```

## 🔐 Security Features

- Role-based access control
- File type and size validation
- CSRF protection
- SQL injection prevention
- XSS protection
- Secure file storage

## 📈 Performance Optimizations

- Database query optimization
- Eager loading relationships
- Route and view caching
- File compression
- CDN-ready architecture

## 🚀 Deployment

For production deployment, see the [Deployment Guide](DEPLOYMENT_GUIDE.md).

### Quick Production Setup

```bash
# Install dependencies
composer install --optimize-autoloader --no-dev

# Build assets
npm run production

# Run migrations
php artisan migrate --force

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is developed for the Department of the Interior and Local Government (DILG) and is intended for government use. All rights reserved.

## 👨‍💻 Developer

**Alexander Nicole E. Bravo**
- **GitHub**: [@alexandernicolebravo](https://github.com/alexandernicolebravo)
- **LinkedIn**: [Alexander Nicole Bravo](https://linkedin.com/in/alexander-nicole-bravo-3aa544377)
- **Institution**: University of Negros Occidental - Recoletos (UNO-R)
- **Specialization**: Full Stack Web Developer and Android Developer

## 📞 Support

For support and questions:
- Check the [documentation](PROJECT_OVERVIEW.md)
- Review [troubleshooting guide](DEPLOYMENT_GUIDE.md#troubleshooting)
- Open an [issue](https://github.com/alexandernicolebravo/barangay-submission-report-system/issues)

---

**Built with ❤️ for the Department of the Interior and Local Government (DILG)**
