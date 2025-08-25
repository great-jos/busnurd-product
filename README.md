# Busnurd Product App

A simple CRUD app built with **Laravel 12**, designed for temporary use and easy deployment.
Features include user authentication, product creation, listing, viewing, and basic CSRF-protected forms.

---

## Features

* User authentication (email & password)
* Create product (name, price, image, description)
* List all products & view individual product details
* File uploads (product images)
* Basic validation + CSRF protection
* Eloquent models, migrations
* Built with Laravel 10+, Blade, and Vite

---

## Tech Stack

* PHP 8.4 + Apache
* Laravel 12
* SQLite (for simplicity, no external DB required)
* Node.js 20 & Vite for frontend asset building
* Docker & Docker Compose (optional for local or cloud deployment)

---

## Setup (Local)

1. **Clone the repo**

```bash
git clone https://github.com/great-jos/busnurd-product.git
cd busnurd-product
```

2. **Install dependencies**

```bash
composer install
npm install
npm run build
```

3. **Set up environment**

* Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

* Set the database connection:

```
DB_CONNECTION=sqlite
DB_DATABASE=/full/path/to/database/database.sqlite
```

* Create SQLite file:

```bash
touch database/database.sqlite
chmod -R 775 database
```

4. **Run migrations & seeders**

```bash
php artisan migrate
```

5. **Run the app**

```bash
php artisan serve
```

Visit: `http://127.0.0.1:8000`

---

## Setup (Docker)

1. **Build Docker image**

```bash
docker build -t busnurd-product .
```

2. **Run container**

```bash
docker run -p 8080:80 busnurd-product
```

3. Visit: `http://localhost:8080`

> If using Docker Compose, just run:
> `docker-compose up -d`

---

## Deployment

This app can be deployed to:

* [Render](https://render.com)
* [Railway](https://railway.app)
* Vercel (for frontend static files only)
* Any Docker-compatible hosting

---

## Notes / Future Improvements

* Store images on S3 or other cloud storage
* Add roles and permissions (admin vs user)
* Add tests (PHPUnit + Dusk)
* Add pagination for products
* Improve frontend UX

---

## License

MIT License — free to use, modify, and distribute.