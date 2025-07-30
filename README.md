<p align="center">
    <a href="#" target="_blank">
        <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
    </a>
</p>

<h2 align="center">AskSEO Chat App</h2>

<p align="center">
    A modern Laravel-based real-time chat application with AJAX messaging, OTP email verification, superadmin controls, and attendance management for staff.
</p>

---

## 🌐 Features

- 🔐 **User Authentication**
  - Register and login with email.
  - Email verification via OTP.
  - Secure Laravel auth with sessions.

- 💬 **Live Chat System**
  - One-on-one and group chat support.
  - AJAX-based real-time messaging.
  - Upload and preview text, images, videos, files, and links.
  - User notifications and read/unread tracking.

- 🧑‍💼 **Superadmin Panel**
  - View and manage all users.
  - Add or remove users/groups.
  - View login history and message statistics.

- 🕒 **Daily Attendance Tracking**
  - Staff attendance is marked automatically upon login.
  - Admin can view attendance records.
  - Tracks login timestamps per user.

---

## 🧰 Tech Stack

- **Laravel** (Backend)
- **Blade Components** (Frontend)
- **AJAX** (Real-time messaging fallback)
- **MySQL** (Database)
- **Laravel Reverb / Broadcasting** *(Optional for future realtime upgrade)*
- **CSS / Tailwind / Custom UI**

---

## 🛠 Setup Instructions

### Prerequisites

- PHP >= 8.1
- Composer
- MySQL / MariaDB
- Node.js & npm (for frontend assets)

### Installation

```bash
git clone git@github.com:MalickMati/askseo_chat.git
cd askseo_chat

composer install
cp .env.example .env
php artisan key:generate

# Set up database credentials in .env

php artisan migrate
php artisan db:seed # Optional
npm install && npm run dev
php artisan serve