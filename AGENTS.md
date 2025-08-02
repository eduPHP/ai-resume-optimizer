# Contributor Guide

## Prerequisites

Ensure the tools below are installed on your system:

1. **Node.js**: Install via [Node.js website](https://nodejs.org/) to ensure `npm` is available.
2. **PHP**: Install the required PHP version compatible with the project dependencies.
3. **Composer**: Install via [Composer website](https://getcomposer.org).

---

## Environment Setup

### Backend (Laravel)

1. Install Laravel dependencies using Composer:
   ```sh
   composer config --global prefer-dist true
   composer config --global github-protocols https
   composer install
   ```
2. Copy and configure the `.env` file:
   ```sh
   cp .env.example .env
   ```
3. Generate the application key:
   ```sh
   php artisan key:generate
   ```
4. Run migrations and seeds (if applicable):
   ```sh
   php artisan migrate --seed
   ```
5. Run tests
   ```sh
   php artisan test
   ```

### Frontend (JavaScript/Vue)

1. Install all project dependencies:
   ```sh
   npm install
   ```
2. Build the javascript bundle
   ```sh
   npm run build
   ```
---

## Running the Project

### Start the Backend (Laravel)

To start the Laravel server locally:
