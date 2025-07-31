# B Laban 🍮 – Full-Stack Restaurant Website

**B Laban** is a full-stack web application for a fictional restaurant, featuring user authentication, a dynamic food menu, a cart system, and a user profile page. Built with **HTML, CSS, JavaScript, PHP, and MySQL**, it's designed to simulate a basic online food ordering system.

---

## 📌 Features

### 👥 User Authentication
- Secure **Sign Up** and **Login** using PHP + MySQL
- Password hashing for security
- Session-based user login system

### 🏠 Home / Intro Page
- Welcome banner
- About section
- Navigation to other pages

### 📋 Menu Page
- Items loaded from the database
- Food item images, names, prices, and "Add to Cart" buttons
- Category filtering (optional)

### 🛒 Cart System
- Add/remove items
- Cart stored via session or database
- Checkout form (optional)

### 👤 Profile Page
- View and edit user info
- Past orders (optional)
- Logout button

---

## 🧑‍💻 Tech Stack

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Other:** Apache server  XAMPP

---

## 📁 Project Structure
project/
│
├── introduction.html # Home/landing page
├── login.html # Login form
├── signup.html # Sign-up form
├── menu.php # Dynamic menu from database
├── cart.html / cart.php # Cart interface
├── profile.html / profile.php# Profile view
│
├── login.php # Login processing (backend)
├── signup.php # Sign-up processing (backend)
├── db_connect.php # Database connection
├── cart_operations.php # Handles cart actions
├── profile_operations.php # (Optional) Profile actions
│
├── /css/
│ ├── start.css
│ ├── style.css
│ ├── introduction.css
│ ├── menu.css
│ ├── cart.css
│ └── profile.css
│
├── /js/
│ ├── script.js
│ ├── introduction.js
│ ├── menu.js
│ ├── cart.js
│ ├── login.js
│ ├── signup.js
│ └── profile.js
│
├── /Photos/ # (Optional) Food or UI images
└── /Videos/ # (Optional) Media files
