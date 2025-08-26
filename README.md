



# 📌 Project Backend – PHP MVC

## 📖 Overview

This is a **backend system built with PHP (MVC pattern)**.
It provides a clean structure with **Controllers, Models, Core, and Routes**.
The system includes **Authentication, Employee Management, Salaries, and Leaves**.

---

## ⚙️ Features

* 🔑 Authentication (Login / Logout)
* 👨‍💼 Employee management
* 💰 Salary management
* 📅 Leave requests
* 📡 REST API ready for frontend integration

---

## 📂 Structure

```
app/Controllers   # Controllers (Auth, Employee, Salary, Leave)
app/Models        # Models (User, Employee, Salary)
app/Core          # Router, Request, Response, DB connection
routes/api.php    # API routes
public/index.php  # Entry point
config/           # Database config
```




---

## 🔗 API Routes

### Auth

* `POST /api/login`
* `POST /api/logout`

### Employees

* `GET /api/employees`
* `POST /api/employees`
* `PUT /api/employees/{id}`
* `DELETE /api/employees/{id}`

### Salaries

* `GET /api/salaries`
* `POST /api/salaries`
* `PUT /api/salaries/{id}`
* `DELETE /api/salaries/{id}`

### Leaves

* `GET /api/leaves`
* `POST /api/leaves`
* `PUT /api/leaves/{id}`

---

## 🧑‍💻 Git Workflow

* `main` → stable
* `feature/auth` → authentication
* `feature/employees` → employees CRUD
* `feature/salaries` → salaries management
* `feature/leaves` → leave management
