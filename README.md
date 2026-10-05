# ShopCart

##  Overview

ShopCart is an e-commerce web application built with Laravel that allows users to browse products, add products to their cart or wishlist, and place orders through Cash on Delivery (COD) or online payment.

The application is designed to provide a convenient shopping experience where users can discover and purchase products for their everyday needs as well as special occasions.

---

##  Key Features

* User registration and authentication
* Browse and view products
* Add products to cart
* Update and remove cart items
* Add and remove products from wishlist
* Purchase products using:

* Cash on Delivery (COD)
* Online payment
* View order history
* View order details
* Receive product offers
* Product management
* Order management

---

##  Product & Catalog

ShopCart provides a product catalog where users can browse available products and view individual product details before making a purchase.

### Product functionality

* Product listing
* Product details
* Product images
* Product pricing

The catalog is backed by a relational database, allowing product information to be managed and associated with the relevant entities.

---

##  Cart & Wishlist

### Cart

Users can add products to their cart before proceeding to checkout.

Cart functionality includes:

* Add product to cart
* Update product quantity
* Remove product from cart
* View cart items
* Calculate cart totals
* Proceed to checkout

### Wishlist

Users can save products they are interested in purchasing later by adding them to their wishlist.

Wishlist functionality includes:

* Add product to wishlist
* Remove product from wishlist
* View saved products
* Move/add wishlist products to cart

---

##  Checkout & Order Workflow

The shopping workflow follows the following process:

Browse Products
       ↓
View Product
       ↓
Add to Cart
       ↓
Review Cart
       ↓
Checkout
       ↓
Select Payment Method
       ↓
 ┌───────────────┐
 │               │
COD          Online Payment
 │               │
 └───────┬───────┘
         ↓
    Order Created
         ↓
    Order History

Users can choose between Cash on Delivery and online payment during checkout.

After placing an order, users can view their previous purchases through the order history section.

---

##  Database Design

ShopCart uses **MySQL** as its relational database.

The application uses relationships between entities such as:

* Users
* Products
* Categories
* Cart
* Wishlist
* Orders
* Order Items
* Payments

---

##  Laravel Concepts Used

ShopCart was developed using Laravel and demonstrates the following concepts:

* MVC architecture
* Routing
* Controllers
* Blade templates
* Eloquent ORM
* Eloquent relationships
* Migrations
* Database queries
* Authentication
* Form validation
* Middleware
* Sessions
* File/image handling
* CRUD operations
* Laravel configuration
* Environment variables
* Payment gateway integration
* Discount and coupon management
* Email notifications
* Order status notifications

---

##  Screenshots

---

## 🚀 Installation

### Prerequisites

Make sure the following are installed:

* PHP
* Composer
* MySQL
* Node.js & npm
* Laravel-compatible PHP extensions
* XAMPP / Laragon or another local PHP development environment

### 1. Clone the repository

git clone https://github.com/dishapatel1412/ShopCart.git
cd ShopCart

### 2. Install PHP dependencies

composer install

### 3. Install frontend dependencies

npm install

### 4. Create the environment file

cp .env.example .env

### 5. Generate the application key

php artisan key:generate

### 6. Configure the database

Update the database configuration in `.env`:
.env:

DB_DATABASE=shopcart
DB_USERNAME=root
DB_PASSWORD=

Create the corresponding database in MySQL before running the migrations.

### 7. Run migrations

php artisan migrate

For development:
npm run dev

### 9. Start the Laravel development server

php artisan serve

The application will then be available at: http://127.0.0.1:8000

---

## 🔮 Future Improvements

Potential improvements for future versions include:

* Product reviews and ratings
* Advanced product search and filtering
* Improved order tracking
* Inventory management
* Admin analytics dashboard
* Automated testing expansion
* Performance optimization
* Production deployment

---

## 🛠️ Tech Stack

**Backend**

* PHP
* Laravel

**Frontend**

* Blade
* HTML
* CSS
* JavaScript
* Bootstrap

**Database**

* MySQL

**Tools**

* Composer
* npm
* Git/GitHub
* Laragon

---

## 👩‍💻 Author

**Disha Patel**

* GitHub: https://github.com/dishapatel1412
