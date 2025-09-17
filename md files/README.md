# 🍰 Bakery Shop - Modern E-commerce Platform

A beautiful, modern bakery shop e-commerce platform built with Laravel 12, featuring a responsive design, advanced search, cart management, and admin panel.

## ✨ Features

### 🛍️ Customer Features
- **Modern Responsive Design** - Beautiful UI with Tailwind CSS
- **Product Catalog** - Browse categories with filters and search
- **Advanced Search** - Fast database search with filters and sorting
- **Shopping Cart** - Add/remove items with real-time updates
- **Product Variants** - Size, flavor, and color options
- **Add-ons System** - Customize orders with additional items
- **Delivery Slots** - Choose preferred delivery time
- **Pincode Validation** - Check delivery availability
- **Payment Integration** - Razorpay + Cash on Delivery
- **Order Tracking** - Real-time order status updates
- **GST Invoicing** - Professional invoices with tax breakdown

### 🎛️ Admin Features
- **Dashboard** - Sales analytics and insights
- **Product Management** - CRUD with media uploads
- **Order Management** - Process orders and generate invoices
- **Customer Management** - View customer profiles and orders
- **Coupon System** - Create and manage discount codes
- **Reports** - Sales, returns, and analytics
- **Role-based Access** - Secure admin permissions

### 🚀 Technical Features
- **Laravel 12** - Latest PHP framework
- **Tailwind CSS** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript framework
- **Database Search** - Fast, optimized search queries
- **Spatie Media Library** - Professional media management
- **Redis Caching** - High-performance caching
- **Queue System** - Background job processing

## 🛠️ Installation

### Prerequisites
- PHP 8.2+
- Composer
- MySQL 8.0+ or PostgreSQL 13+
- Node.js 18+ and npm
- Redis (optional, for caching)

### 1. Clone the Repository
```bash
git clone <repository-url>
cd bakery-shop
```

### 2. Install Dependencies
```bash
# PHP dependencies
composer install

# Node.js dependencies
npm install
```

### 3. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Environment Variables
Edit `.env` file with your database and service credentials:

```env
APP_NAME="Bakery Shop"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bakery_shop
DB_USERNAME=root
DB_PASSWORD=

# Redis Configuration (optional)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@bakery.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Database Setup
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE bakery_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

### 6. Storage Setup
```bash
# Create storage link
php artisan storage:link

# Set proper permissions
chmod -R 775 storage bootstrap/cache
```

### 7. Build Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 8. Start Development Server
```bash
php artisan serve
```

Visit `http://localhost:8000` to see your bakery shop!

## 🔧 Configuration

### Search Configuration
The platform uses optimized database queries for fast search functionality:

1. **Full-text Search** - Searches across product names, descriptions, and categories
2. **Fuzzy Matching** - Handles typos and partial matches
3. **Advanced Filtering** - Price range, category, stock availability
4. **Smart Sorting** - Relevance, price, name, newest first

### Redis Setup (Optional)
1. Install Redis:
```bash
# macOS
brew install redis

# Ubuntu
sudo apt-get install redis-server

# Windows
# Download from https://redis.io/download
```

2. Start Redis:
```bash
redis-server
```

3. Configure in `.env`:
```env
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Configure production database credentials
- [ ] Set up SSL certificate
- [ ] Configure web server (Nginx/Apache)
- [ ] Set up queue workers for background jobs
- [ ] Configure caching (Redis recommended)
- [ ] Set up monitoring and logging

### Performance Optimization
- Enable OPcache for PHP
- Use Redis for session and cache storage
- Implement CDN for static assets
- Enable database query caching
- Use queue workers for heavy operations

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Laravel team for the amazing framework
- Tailwind CSS for the utility-first CSS framework
- Filament team for the excellent admin panel
- Spatie for the media library package

## 📞 Support

If you have any questions or need help, please open an issue on GitHub or contact us at support@bakery.com.

---

**Made with ❤️ by the Bakery Shop Team**
