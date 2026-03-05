# ⚡ ChallengeHub — Installation Guide

## Requirements
- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.4+
- WAMPServer / XAMPP / EasyPHP
- Apache with mod_rewrite enabled

---

## ⚙️ Installation Steps

### 1. Place the project
Copy the `challengehub/` folder into your web server root:
- **WAMP**: `C:/wamp64/www/challengehub/`
- **XAMPP**: `C:/xampp/htdocs/challengehub/`

### 2. Create the database
1. Open phpMyAdmin → `http://localhost/phpmyadmin`
2. Create a database named `challengehub2`
3. Import the file: `challengehub_database.sql`

### 3. Configure the connection
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'challengehub2');
define('DB_USER', 'root');
define('DB_PASS', '');         // Your MySQL password
```

### 4. Configure the base URL
Edit `config/app.php`:
```php
define('BASE_URL', 'http://localhost/challengehub/public');
```

### 5. Set upload permissions
Make sure `public/images/uploads/` is writable (chmod 755 on Linux).

### 6. Launch
Open your browser: `http://localhost/challengehub/`

---

## 🔑 Demo Account
- **Email**: admin@challengehub.com
- **Password**: Admin1234

---

## 📁 Project Structure
```
challengehub/
├── app/
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── ChallengeController.php
│   │   ├── SubmissionController.php
│   │   └── ProfileController.php
│   ├── models/
│   │   ├── User.php
│   │   ├── Challenge.php
│   │   ├── Submission.php
│   │   ├── Comment.php
│   │   └── Vote.php
│   └── views/
│       ├── auth/        (login, register)
│       ├── challenges/  (index, show, create, edit)
│       ├── submissions/ (show, edit)
│       ├── profile/     (show, edit)
│       ├── partials/    (header, footer)
│       ├── home.php
│       └── leaderboard.php
├── config/
│   ├── database.php
│   └── app.php
├── public/
│   ├── css/main.css
│   ├── js/main.js
│   └── images/uploads/
├── index.php           ← Main router
├── .htaccess
└── challengehub_database.sql
```

---

## 🔒 Security Features
- ✅ Password hashing with `password_hash()` (bcrypt, cost 12)
- ✅ PDO prepared statements (SQL injection protection)
- ✅ XSS protection with `htmlspecialchars()` on all output
- ✅ CSRF token validation on all POST forms
- ✅ Secure session configuration (httponly, samesite)
- ✅ Session regeneration on login
- ✅ Input validation on all forms
- ✅ File upload validation (type + size)

---

## 🎯 Business Rules
- Maximum **5 participants** per challenge
- **3 pre-seeded e-commerce challenges** on installation
- Authors cannot submit to their own challenge
- One vote per user per submission
- Challenges are publicly visible

---

## 🔧 Tech Stack
- **Backend**: PHP 8.1+ OOP (no framework)
- **Architecture**: MVC (simplified)
- **Database**: MySQL via PDO
- **Frontend**: Custom CSS + Bootstrap 5 (single CDN link)
- **Icons**: Bootstrap Icons
- **Fonts**: Syne + DM Sans (Google Fonts)
