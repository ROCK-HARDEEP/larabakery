# Bakery Shop - Filament Admin Panel

A modern and powerful admin panel built with **Laravel Filament** for managing the bakery shop operations.

## 🚀 Features

- **Modern UI**: Built with Filament 3.x and Tailwind CSS
- **Responsive Design**: Works perfectly on desktop, tablet, and mobile
- **Role-based Access**: Secure admin authentication
- **Real-time Statistics**: Live dashboard with sales analytics
- **Product Management**: Complete CRUD operations for products
- **Order Management**: Track and manage customer orders
- **Coupon System**: Create and manage discount coupons
- **Media Management**: Integrated with Spatie Media Library
- **Search & Filters**: Advanced filtering and search capabilities

## 📋 Requirements

- PHP 8.2+
- Laravel 12.x
- MySQL/PostgreSQL
- Composer

## 🛠️ Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd bakery-shop
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

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Create admin user**
   ```bash
   php artisan make:filament-user
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

## 🔐 Admin Access

- **URL**: `http://your-domain.com/admin`
- **Login**: Use the credentials created with `make:filament-user`
- **Default Email**: `hardeepb2003@gmail.com`

## 📊 Dashboard Features

### Statistics Overview
- Total Orders
- Total Revenue
- Total Customers
- Total Products
- Today's Orders
- Today's Revenue

### Sales Analytics
- 7-day sales chart
- Revenue trends
- Order statistics

## 🛍️ Management Modules

### 1. Shop Management
- **Products**: Add, edit, delete products with images
- **Categories**: Organize products by categories
- **Bundles**: Create product bundles and packages

### 2. Order Management
- **Orders**: View and manage customer orders
- **Order Status**: Update order status (pending, processing, completed, cancelled)
- **Payment Status**: Track payment status
- **Picklist**: Generate picklists for order fulfillment

### 3. Customer Management
- **Customers**: View customer information
- **Customer Orders**: Track customer order history
- **Customer Analytics**: Customer behavior insights

### 4. Marketing & Promotions
- **Coupons**: Create discount coupons
- **Coupon Types**: Flat amount or percentage discounts
- **Usage Limits**: Set usage limits for coupons
- **Expiration**: Set coupon expiration dates

### 5. System Settings
- **Users**: Manage admin users
- **Roles & Permissions**: Role-based access control
- **System Configuration**: General settings

## 🎨 Customization

### Branding
- **Logo**: Update `public/img/bakery-logo.svg`
- **Colors**: Modify in `app/Providers/Filament/AdminPanelProvider.php`
- **Theme**: Customize in `resources/css/filament/admin/theme.css`

### Navigation
- **Groups**: Configure in `AdminPanelProvider.php`
- **Icons**: Use Heroicons or custom icons
- **Sorting**: Set navigation sort order

## 📱 Responsive Design

The admin panel is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones
- All modern browsers

## 🔒 Security Features

- **Authentication**: Secure login system
- **Authorization**: Role-based permissions
- **CSRF Protection**: Built-in CSRF protection
- **Input Validation**: Comprehensive form validation
- **SQL Injection Protection**: Eloquent ORM protection

## 🚀 Performance

- **Caching**: Built-in caching mechanisms
- **Optimized Queries**: Efficient database queries
- **Asset Optimization**: Minified CSS/JS
- **Image Optimization**: Automatic image resizing

## 📈 Analytics & Reporting

### Dashboard Widgets
- **Welcome Widget**: Overview statistics
- **Sales Stats**: Sales analytics chart
- **Recent Orders**: Latest order activity
- **Popular Products**: Best-selling items

### Export Features
- **Data Export**: Export data to CSV/Excel
- **Reports**: Generate custom reports
- **Charts**: Visual data representation

## 🛠️ Development

### Adding New Resources
```bash
php artisan make:filament-resource ModelName
```

### Adding New Widgets
```bash
php artisan make:filament-widget WidgetName
```

### Adding New Pages
```bash
php artisan make:filament-page PageName
```

## 📝 File Structure

```
app/
├── Filament/
│   ├── Resources/
│   │   ├── ProductResource.php
│   │   ├── OrderResource.php
│   │   └── CouponResource.php
│   ├── Pages/
│   │   └── Dashboard.php
│   └── Widgets/
│       ├── WelcomeWidget.php
│       └── SalesStats.php
└── Providers/
    └── Filament/
        └── AdminPanelProvider.php
```

## 🔧 Configuration

### Admin Panel Provider
Located at: `app/Providers/Filament/AdminPanelProvider.php`

Key configurations:
- Panel ID and path
- Brand name and logo
- Navigation groups
- Widgets
- Middleware
- Colors and theme

### Resource Configuration
Each resource can be customized with:
- Navigation grouping
- Form fields
- Table columns
- Actions and filters
- Validation rules

## 🐛 Troubleshooting

### Common Issues

1. **Admin panel not loading**
   - Clear cache: `php artisan config:clear`
   - Check routes: `php artisan route:list --name=admin`

2. **Images not uploading**
   - Check storage permissions
   - Verify media library configuration

3. **Database errors**
   - Run migrations: `php artisan migrate`
   - Check database connection

4. **Permission issues**
   - Verify user roles
   - Check middleware configuration

## 📞 Support

For support and questions:
- Check the [Filament Documentation](https://filamentphp.com/docs)
- Review Laravel documentation
- Check GitHub issues

## 🎯 Future Enhancements

- [ ] Advanced reporting
- [ ] Email notifications
- [ ] SMS integration
- [ ] Inventory management
- [ ] Supplier management
- [ ] Advanced analytics
- [ ] Multi-language support
- [ ] API endpoints

---

**Built with ❤️ using Laravel and Filament**
