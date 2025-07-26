
<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="#"><img src="https://img.shields.io/badge/build-passing-brightgreen" alt="Build Status"></a>
  <a href="#"><img src="https://img.shields.io/badge/license-MIT-blue.svg" alt="License"></a>
  <a href="#"><img src="https://img.shields.io/badge/status-active-success" alt="Project Status"></a>
</p>

---

# Tenamart Waiting List API

The **TenaMart Waiting List** is a Laravel-powered REST API designed to manage early access signups for an e-commerce platform. It allows collecting, managing, and analyzing user data, with support for CSV export and scheduled email reports.

---

## ✨ Features

- 🔁 Full CRUD for waiting list signups
- 📈 Insights & statistics on signup sources
- 📨 Weekly report email (configurable)
- 📁 Export signups to CSV
- 🛡️ Clean, RESTful Laravel API architecture

---

## ⚙️ How to Run Locally

### Prerequisites

- PHP 8.1+
- Composer
- MySQL / SQLite
- Laravel CLI

### Steps

1. **Clone the Repository**

```bash
git clone https://github.com/Tovas7/Tenamart_waiting_list.git
cd Tenamart_waiting_list
```

2. **Install Dependencies**

```bash
composer install
```

3. **Configure Environment**

```bash
cp .env.example .env
php artisan key:generate
```

Then update `.env` with your database and mail credentials:

```env
DB_DATABASE=tenamart_waiting_list
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=admin@tenamart.com
MAIL_FROM_NAME="TenaMart Admin"
```

4. **Run Migrations**

```bash
php artisan migrate
```

5. **Serve the Application**

```bash
php artisan serve
```

The API will be accessible at: `http://127.0.0.1:8000/api`

---

## 🔌 API Endpoints

| Method | Endpoint               | Description               |
|--------|------------------------|---------------------------|
| GET    | `/api/waiting-list`    | List all signups          |
| POST   | `/api/waiting-list`    | Create a new signup       |
| GET    | `/api/waiting-list/{id}` | View a specific signup |
| PUT    | `/api/waiting-list/{id}` | Update a signup        |
| DELETE | `/api/waiting-list/{id}` | Delete a signup        |
| GET    | `/api/stats`           | Signup statistics         |
| GET    | `/api/export/csv`      | Export signups as CSV     |

---

## 🧪 How to Test API

Use tools like **Postman** or **Insomnia** to test the endpoints.

### Example `POST` Body:

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "signup_source": "referral"
}
```

### Run All Tests (if available):

```bash
php artisan test
```

---

## 💡 Logic & Technical Choices

### Laravel

Chosen for its expressive syntax, powerful tooling, and first-class support for RESTful API development, background tasks, and email handling.

### Signup Model

The `Signup` model includes:
- `name`: Full name of user
- `email`: Validated, unique
- `signup_source`: Origin (e.g., "ads", "referral")

### Statistics Endpoint

The `/api/stats` route uses Eloquent's aggregation methods to return:
- Total signups
- Source breakdown
- Signup trends over time

### CSV Export

Data is streamed directly using Laravel's `response()->streamDownload()` method for memory efficiency.

### Weekly Report

Implemented with Laravel’s Scheduler and custom Artisan command to send summary emails weekly.

To enable the scheduler:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📦 Project Structure

```
├── app/
├── database/
│   └── migrations/
├── routes/
│   └── api.php
├── app/Console/Commands/SendWeeklyReport.php
├── app/Http/Controllers/
└── tests/
```

---

## 🧑‍💻 Author

**Muluken Zewdu**  
📫 raphaeltovas6@gmail.com  
🔗 [GitHub](https://github.com/Tovas7) • [LinkedIn](https://linkedin.com/in/muluken-zewdu-a1b846357)

---

## 📝 License

This project is open-sourced under the [MIT License](LICENSE).

---

## ⭐️ Support

If you found this project useful, feel free to give it a ⭐ on GitHub!
