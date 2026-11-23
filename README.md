# Courier Management API

A RESTful API backend for managing courier master data, built with Laravel 13. The project follows clean architecture principles with thin controllers, query scopes, form request validation, and API resources.

---

## Tech Stack

- **Runtime**: PHP 8.3+
- **Framework**: Laravel 13
- **Database**: SQLite (development & testing) / MySQL (production)
- **Testing**: Pest v5 with RefreshDatabase
- **Primary Key**: UUID v7 via `HasUuids`

---

## Requirements

- PHP >= 8.3
- Composer
- SQLite (for local development and testing)

---

## Installation & Setup

**1. Clone the repository**

```bash
git clone <repository-url>
cd backend-laravel
```

**2. Install dependencies**

```bash
composer install
```

**3. Configure environment**

```bash
cp .env.example .env
php artisan key:generate
```

**4. Create the SQLite database file**

```bash
touch database/database.sqlite
```

**5. Run migrations and seeders**

```bash
php artisan migrate --seed
```


**6. Start the development server**

```bash
php artisan serve
```

The API will be available at `http://localhost:8000/api`. This project is API-only: it has no UI, HTML, CSS, or JavaScript assets.

---

## Running Tests

Tests use an in-memory SQLite database configured in `phpunit.xml`. No additional setup is required.

```bash
php artisan test
```

Expected output: **14 tests, 48 assertions, 0 failures**.

To run only the courier feature tests:

```bash
php artisan test --filter=CourierTest
php artisan test --filter=CourierPerformanceTest
```

---

## Database Schema

Table: `couriers`

| Column            | Type                 | Notes                                              |
|-------------------|----------------------|----------------------------------------------------|
| `id`              | UUID (PK)            | UUIDv7, auto-generated                             |
| `nama`            | string(150)          | Indexed                         |
| `email`           | string(150)          | Unique                                             |
| `telepon`         | string(20)           | Unique                         |
| `level`           | unsignedTinyInteger  | 1–5, indexed, default 1                            |
| `jenis_kendaraan` | string(50), nullable | motor, mobil, van, truk |
| `plat_nomor`      | string(15), nullable |                          |
| `aktif`           | boolean              | Default true              |
| `created_at`      | timestamp            | Indexed                                            |
| `updated_at`      | timestamp            |                                                    |

---

## API Endpoints

Base URL: `/api`

All responses follow Laravel's standard JSON structure. Validation errors return HTTP `422`. Not found errors return HTTP `404`.

---

### List Couriers

```
GET /api/couriers
```

Returns a paginated list of couriers. Sorted by `nama` ascending by default.

**Query Parameters**

| Parameter  | Type    | Description                                                          | Example               |
|------------|---------|----------------------------------------------------------------------|-----------------------|
| `per_page` | integer | Number of results per page (default: 15)                             | `?per_page=10`        |
| `search`   | string  | Multi-word name search (AND condition per word)                      | `?search=budi+agung`  |
| `level`    | string  | Comma-separated level filter                                         | `?level=2,3`          |
| `sort_by`  | string  | `nama`, `email`, `telepon`, `level`, `created_at`, `updated_at`       | `?sort_by=created_at` |
| `sort_dir` | string  | Sort direction: `asc` or `desc`                                      | `?sort_dir=desc`      |

**Example Request**

```bash
GET /api/couriers?search=budi+agung&level=2,3&sort_by=created_at&sort_dir=desc&per_page=10
```

**Example Response**

```json
{
  "data": [
    {
      "id": "0196b2a3-1234-7000-8000-abcdef012345",
      "nama": "Budiono Hadi Agung",
      "email": "budiono@example.com",
      "telepon": "081234567890",
      "level": 2,
      "jenis_kendaraan": "motor",
      "plat_nomor": "B-1234",
      "aktif": true,
      "created_at": "2025-01-15T08:00:00.000000Z",
      "updated_at": "2025-01-15T08:00:00.000000Z"
    }
  ],
  "links": {
    "first": "http://localhost:8000/api/couriers?page=1",
    "last": "http://localhost:8000/api/couriers?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

---

### Get Courier

```
GET /api/couriers/{id}
```

Returns a single courier by UUID (primary key terindeks).

**Example Response — 200 OK**

```json
{
  "data": {
    "id": "0196b2a3-1234-7000-8000-abcdef012345",
    "nama": "Budiono Hadi Agung",
    "email": "budiono@example.com",
    "telepon": "081234567890",
    "level": 2,
    "jenis_kendaraan": "motor",
    "plat_nomor": "B-1234",
    "aktif": true,
    "created_at": "2025-01-15T08:00:00.000000Z",
    "updated_at": "2025-01-15T08:00:00.000000Z"
  }
}
```

**Example Response — 404 Not Found**

```json
{
  "message": "Record not found."
}
```

---

### Create Courier

```
POST /api/couriers
Content-Type: application/json
```

**Request Body**

| Field            | Type    | Required | Rules                           |
|------------------|---------|----------|---------------------------------|
| `nama`           | string  | Yes      | min:3, max:150                  |
| `email`          | string  | Yes      | valid email, unique             |
| `telepon`        | string  | Yes      | min:10, max:20, unique          |
| `level`          | integer | Yes      | between 1 and 5                 |
| `jenis_kendaraan`| string  | No       | motor, mobil, van, truk         |
| `plat_nomor`     | string  | No       | max:15                          |
| `aktif`          | boolean | No       | defaults to true                |

**Example Request**

```json
{
  "nama": "Budiono Hadi Agung",
  "email": "budiono@example.com",
  "telepon": "081234567890",
  "level": 2,
  "jenis_kendaraan": "motor",
  "plat_nomor": "B-1234",
  "aktif": true
}
```

**Example Response — 201 Created**

```json
{
  "data": {
    "id": "0196b2a3-1234-7000-8000-abcdef012345",
    "nama": "Budiono Hadi Agung",
    "email": "budiono@example.com",
    "telepon": "081234567890",
    "level": 2,
    "jenis_kendaraan": "motor",
    "plat_nomor": "B-1234",
    "aktif": true,
    "created_at": "2025-01-15T08:00:00.000000Z",
    "updated_at": "2025-01-15T08:00:00.000000Z"
  }
}
```

**Example Response — 422 Unprocessable Entity**

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."],
    "level": ["The level field must be between 1 and 5."]
  }
}
```

---

### Update Courier

```
PUT /api/couriers/{id}
Content-Type: application/json
```

Accepts the same fields as the create endpoint. Unique rules for `email` and `telepon` ignore the current record.

**Example Response — 200 OK**

```json
{
  "data": {
    "id": "0196b2a3-1234-7000-8000-abcdef012345",
    "nama": "Budiono Hadi Agung Updated",
    "email": "budiono.new@example.com",
    "telepon": "081234567891",
    "level": 3,
    "jenis_kendaraan": "mobil",
    "plat_nomor": "B-5678",
    "aktif": false,
    "created_at": "2025-01-15T08:00:00.000000Z",
    "updated_at": "2025-01-15T09:00:00.000000Z"
  }
}
```

---

### Delete Courier

```
DELETE /api/couriers/{id}
```

Permanently removes the courier record from the database.

**Example Response — 200 OK**

```json
{
  "message": "Courier deleted successfully."
}
```

---


## Project Structure

```
app/
  Http/
    Controllers/Api/CourierController.php   # Thin controller, query scopes & API resources
    Requests/StoreCourierRequest.php        # Validation for create
    Requests/UpdateCourierRequest.php       # Validation for update (unique ignore)
    Resources/CourierResource.php           # API response transformer
  Models/Courier.php                        # Model with typed query scopes
database/
  factories/CourierFactory.php
  migrations/..._create_couriers_table.php
  seeders/CourierSeeder.php
routes/
  api.php                                   # Route::apiResource('couriers', ...)
  web.php                                   # Empty (API only)
tests/
  Feature/CourierTest.php                   # Feature tests for all API endpoints
  Feature/CourierPerformanceTest.php        # row response check
bootstrap/
  app.php                                   # Exception handling (404, 422 JSON responses)
```

---

## Feature Test Coverage

| Test Case                                      | Assertion                                      |
|------------------------------------------------|------------------------------------------------|
| Returns standard pagination structure          | `data`, `links`, `meta` keys present           |
| Default sort by `nama` ASC                     | Response names are alphabetically ordered      |
| Override sort by `created_at` DESC             | Response dates are in descending order         |
| Multi-word search (`budi agung`)               | Matches "Budiono Hadi Agung", excludes others  |
| Level filter (`?level=2,3`)                    | Only levels 2 and 3 returned                   |
| Show existing courier                          | HTTP 200 with correct data                     |
| Show non-existing courier                      | HTTP 404 with JSON message                     |
| Store valid courier                            | HTTP 201, record exists in database            |
| Store with invalid data                        | HTTP 422 validation error                      |
| Store with duplicate email/phone               | HTTP 422 unique constraint error               |
| Update existing courier                        | HTTP 200, database reflects changes            |
| Delete existing courier                        | HTTP 200, record absent from database          |
| 10.000-row response                            | 15 rows only, no `select *`, `limit 15`, < 5s  |
