# E-Commerce Platform with Coupon System

A modern Laravel 12 e-commerce platform featuring a comprehensive discount coupon system with category-specific restrictions.

## Features

### 🛒 Core E-Commerce
- Product catalog with categories
- Shopping cart functionality
- Checkout process
- Order management

### 🎫 Coupon System
- **Category-Specific Discounts**: Coupons only apply when ALL cart products belong to eligible categories
- **Discount Types**: Percentage and fixed amount discounts
- **Date Range Control**: Set valid from/until dates for coupons
- **Admin Management**: Full CRUD interface for coupon management
- **Real-time Validation**: AJAX coupon validation during checkout
- **Business Logic**: Comprehensive validation rules and error handling

## Requirements

- PHP 8.3.24+
- Laravel 12
- MySQL/PostgreSQL
- Composer
- Node.js & NPM

## Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd e-commerce-platform
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
# Configure database in .env file
php artisan migrate
php artisan db:seed
```

5. **Build assets**
```bash
npm run build
# or for development
npm run dev
```

## Usage

### Admin Coupon Management

Navigate to `/admin/coupons` to:
- Create new coupons with category restrictions
- Set discount types (percentage/fixed)
- Configure validity periods
- Manage coupon status (active/inactive)

### Customer Checkout

During checkout:
- Enter coupon codes in the designated field
- Real-time validation ensures coupon applicability
- Discounts apply only if ALL cart products belong to eligible categories
- Clear error messages guide users through restrictions

## Database Schema

### Key Tables
- `coupons`: Stores coupon information
- `categories`: Product categories
- `category_coupon`: Many-to-many relationship between coupons and categories
- `products`: Product catalog
- `cart_items`: Shopping cart storage

## Testing

Run the comprehensive test suite:

```bash
# Run all tests
php artisan test

# Run coupon-specific tests
php artisan test --filter=Coupon
```

**Test Coverage:**
- 13 passing tests (22 assertions)
- Unit tests for models and services
- Feature tests for real-world scenarios
- 100% coverage of core coupon functionality

## Architecture

### SOLID Principles Implementation
- **Single Responsibility**: Separate services for cart and coupon logic
- **Open/Closed**: Extensible model and service architecture  
- **Dependency Inversion**: Constructor injection in controllers

### Laravel Best Practices
- Constructor property promotion (Laravel 12)
- Eloquent relationships with proper return types
- Form Request validation
- Service layer architecture
- Blade templating with Tailwind CSS

## API Endpoints

### Coupon Management
- `GET /admin/coupons` - List all coupons
- `GET /admin/coupons/create` - Create coupon form
- `POST /admin/coupons` - Store new coupon
- `GET /admin/coupons/{coupon}` - View coupon details
- `GET /admin/coupons/{coupon}/edit` - Edit coupon form
- `PUT /admin/coupons/{coupon}` - Update coupon
- `DELETE /admin/coupons/{coupon}` - Delete coupon
- `POST /validate-coupon` - AJAX coupon validation

### Checkout
- `GET /checkout` - Checkout page
- `POST /checkout/process` - Process order with optional coupon

## Code Quality

- **Laravel Pint**: Automated code formatting
- **PHPDoc**: Comprehensive documentation
- **Type Hints**: Full return type declarations
- **Error Handling**: Comprehensive validation and error messages

## Development Commands

```bash
# Format code
vendor/bin/pint

# Run tests
php artisan test

# Start development server
php artisan serve
```

## Contributing

1. Follow Laravel coding standards
2. Write tests for new features
3. Run `vendor/bin/pint` before committing
4. Ensure all tests pass

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).