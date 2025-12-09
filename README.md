# 🗂️ Laravel Kanban Board – Backend API

A clean and simple **Laravel-powered backend** for a Kanban Board application.  
This API supports task creation, updates, movement between columns, and deletion — built to work seamlessly with the Vue.js frontend.

<br>

## 🚀 Features
- RESTful API for Kanban tasks  
- Create / Read / Update / Delete tasks  
- Move tasks between columns  
- Clean and modular Laravel structure  
- Ready to integrate with the Vue.js frontend

<br>

## 📦 Tech Stack
- **Laravel 11**
- **MySQL** (or any supported database)
- **PHP 8+**

<br>

## 🔗 Frontend Repository
Vue.js frontend for this project:  
👉 https://github.com/iamsukanta/kanbanboard-frontend

<br>

## 🛠️ Installation

```bash
git clone https://github.com/iamsukanta/laravel-vue-kanban-board.git
cd laravel-vue-kanban-board
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
