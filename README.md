<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>


# Laravel Task Manager

## Overview

This Laravel Task Manager application allows users to manage tasks with role-based access control. It supports task creation, assignment, status updates, and deletion. The application has three main user roles: admin, manager, and employee, each with specific permissions.

## Features

- **Role-based Access Control**: Admins, managers, and employees have different permissions.
- **Task Management**: Create, update, assign, and delete tasks.
- **Task Filtering**: Filter tasks by priority, status, or whether they are deleted.
- **Task Assignment**: Assign tasks to employees and track their progress.
- **Status Updates**: Update task status and calculate completion ratings.
- **Soft Deletes**: Support for soft deleting tasks.

## Requirements

- PHP 8.0 or higher
- Laravel 10.x
- Composer
- MySQL or another supported database

## Installation

1. **Clone the repository:**

```
git clone https://github.com/your-username/laravel-task-manager.git
cd laravel-task-manager
```

2. **Install dependencies:**
 ```
 composer install
 ```

3. **Create a .env file:**
```
     cp .env.example .env
```

4. **Generate the application key:**
```
php artisan key:generate
```

5. **Run database migrations and seed the database:**
```
php artisan migrate
php artisan db:seed
```

6. **Start the development server:**
```
php artisan serve
```

The application will be available at http://localhost:8000. or 8001

<h2>API Endpoints</h2>

**List Tasks**
```
GET /api/tasks
```
  
  Query Parameters:
    withDeleted (boolean): Include soft-deleted tasks.
**Create Task**    
```
POST /api/tasks
```
   Request Body:

    1.title (string)
    2.description (string)
    3.priority_id (integer)
    4.status_id (integer)
    5.execute_time (integer, optional)
    6.user_id (integer, optional)

**Get Task by ID**
```
GET /api/tasks/{id}
```

**Update Task**
```
PUT /api/tasks/{id}
```
  Request Body:

    title (string, optional)
    description (string, optional)
    priority_id (integer, optional)
    execute_time (integer, optional)
    user_id (integer, optional)

**Assign Task**
```
POST /api/tasks/{id}/assign
```
  Request Body:

    user_id (integer)
    assign_date (date)
    due_date (date)

**Update Task Status**
```
   PUT /api/tasks/{id}/status
```
  Request Body:

    status_id (integer)

**Delete Task**
```
DELETE /api/tasks/{id}
```
