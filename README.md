# Expense Tracker API 💰📊

A RESTful API built with **Laravel** for managing personal expenses and categories.  
This project is designed to be used with a frontend (React or any client) to track daily expenses.

---

## 🚀 Features
- CRUD operations for **Expenses** and **Categories**  
- Dashboard with **summary statistics**  
- Filtering by date, category, and search keyword  
- Sorting and pagination support  
- JSON-based REST API responses  

---

## 🛠️ Tech Stack
- **Backend:** Laravel 12 (PHP 8.2+)  
- **Database:** MySQL 8  
- **Authentication:** Laravel Sanctum (optional)  
- **Frontend Compatible:** React, Vue, or any REST client  

---

## 📦 Installation

```bash
# Clone the repository
git clone https://github.com/your-username/expense-tracker-api.git

cd expense-tracker-api

# Install dependencies
composer install
npm install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations & seed database
php artisan migrate --seed
```

---

## ▶️ Running the App

```bash
# Start local server
php artisan serve
```

Default API URL:  
`http://127.0.0.1:8000/api`

---

## 📖 API Endpoints

### Categories
- `GET /api/categories` → List all categories  
- `POST /api/categories` → Create new category  
- `PUT /api/categories/{id}` → Update category  
- `DELETE /api/categories/{id}` → Delete category  

### Expenses
- `GET /api/expenses` → List all expenses  
- `POST /api/expenses` → Create new expense  
- `PUT /api/expenses/{id}` → Update expense  
- `DELETE /api/expenses/{id}` → Delete expense  

### Dashboard
- `GET /api/dashboard` → Get summary statistics  

---

## 🌐 Deployment

If using **Vercel/Netlify frontend** with **Laravel backend**:
- Update `config/cors.php` to allow your frontend domain.  
- Example:
  ```php
  'allowed_origins' => [
      'http://localhost:3000',
      'https://your-frontend.vercel.app'
  ],
  ```

---

## 📜 License
This project is licensed under the **MIT License**.
