# Liquid Vending Machine App - Laravel + Vue.js

A complete liquid vending machine application built with Laravel backend and Vue.js frontend.

## 🚀 Features

- **Interactive Vending Interface** - Modern, responsive UI for customers
- **Admin Panel** - Complete inventory and order management
- **Multiple Payment Methods** - Cash, Card, and UPI support
- **Analytics Dashboard** - Sales reports and insights
- **MySQL Database** - Robust data storage and management
- **Laravel Sanctum** - API authentication
- **Vue.js 3** - Modern frontend framework

## 🛠️ Tech Stack

### Backend
- **Laravel 10** - PHP framework
- **MySQL** - Database
- **Laravel Sanctum** - API authentication
- **Eloquent ORM** - Database operations

### Frontend
- **Vue.js 3** - Frontend framework
- **Vue Router** - Client-side routing
- **Axios** - HTTP client
- **Vite** - Build tool
- **Font Awesome** - Icons

## 📋 Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js (v16 or higher)
- MySQL Server
- npm or yarn

## 🚀 Installation & Setup

### 1. Clone and Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 2. Environment Setup

```bash
# Copy environment file
cp env.example .env

# Generate application key
php artisan key:generate
```

### 3. Database Configuration

Update `.env` file with your MySQL credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=liquid_vending
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Database Setup

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE liquid_vending;"

# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

### 5. Start the Application

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - Frontend Development:**
```bash
npm run dev
```

### 6. Access the Application

- **Customer Interface:** http://localhost:8000/vending
- **Admin Panel:** http://localhost:8000/admin
- **API Endpoints:** http://localhost:8000/api

## 🔐 Default Admin Credentials

- **Email:** admin@vending.com
- **Password:** admin123

⚠️ **Important:** Change the default admin credentials in production!

## 📊 API Endpoints

### Products
- `GET /api/products` - Get all products
- `POST /api/admin/products` - Add new product (Admin only)
- `PUT /api/admin/products/{id}` - Update product (Admin only)
- `DELETE /api/admin/products/{id}` - Delete product (Admin only)

### Orders
- `POST /api/orders` - Create new order
- `GET /api/admin/orders` - Get all orders (Admin only)

### Admin
- `POST /api/admin/login` - Admin login

### Analytics
- `GET /api/admin/analytics` - Get sales analytics (Admin only)

## 🗂️ Project Structure

```
laravel-vending-app/
├── app/
│   ├── Http/Controllers/
│   │   ├── ProductController.php
│   │   ├── OrderController.php
│   │   ├── AdminController.php
│   │   └── AnalyticsController.php
│   ├── Models/
│   │   ├── Product.php
│   │   ├── Order.php
│   │   └── Admin.php
│   └── Providers/
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── js/
│   │   ├── components/
│   │   │   ├── VendingMachine.vue
│   │   │   └── AdminPanel.vue
│   │   ├── App.vue
│   │   └── app.js
│   ├── views/
│   └── css/
├── routes/
│   └── web.php
├── public/
└── composer.json
```

## 🎯 Features Overview

### Customer Interface
- Browse available drinks by category
- Select quantity and view total price
- Multiple payment options (Cash, Card, UPI)
- Real-time stock updates
- Responsive design for mobile devices

### Admin Panel
- **Products Management:** Add, edit, delete products
- **Orders Management:** View all orders and sales history
- **Analytics:** Sales reports, popular products, revenue tracking
- **Stock Management:** Monitor and update product inventory

### Security Features
- Laravel Sanctum authentication for admin access
- Password hashing with bcrypt
- Protected admin routes
- Input validation and sanitization

## 🔧 Customization

### Adding New Product Categories
1. Update the category options in `AdminPanel.vue`
2. Add corresponding icons in `VendingMachine.vue`
3. Update the database schema if needed

### Styling Customization
- Modify `resources/css/app.css` for global styles
- Update component-specific styles in each Vue file
- Change color scheme by updating CSS variables

### Payment Integration
- Integrate real payment gateways in the backend
- Update payment processing logic in `VendingMachine.vue`
- Add payment confirmation handling

## 🚀 Production Deployment

### Frontend Build
```bash
npm run build
```

### Environment Variables
Update `.env` file for production:
```env
APP_ENV=production
APP_DEBUG=false
DB_HOST=your_production_db_host
DB_USERNAME=your_production_db_user
DB_PASSWORD=your_production_db_password
```

### Database Security
- Use strong passwords
- Enable SSL connections
- Restrict database user permissions
- Regular backups

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Verify MySQL is running
   - Check database credentials in `.env`
   - Ensure database exists

2. **Composer Install Issues**
   - Update Composer to latest version
   - Check PHP version compatibility

3. **Node.js Dependencies**
   - Clear npm cache: `npm cache clean --force`
   - Delete `node_modules` and run `npm install`

4. **Laravel Key Generation**
   - Run `php artisan key:generate`
   - Ensure `.env` file exists

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support

For support and questions, please create an issue in the repository.

## 🎉 Quick Start Commands

```bash
# Complete setup
composer install
npm install
cp env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
npm run dev
```

Enjoy your Liquid Vending Machine App! 🥤
