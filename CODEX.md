# CODEX & AI Collaborator Context Guide
# Organization Asset Management System (ระบบจัดการครุภัณฑ์องค์กร)
**Course:** 954348 Web Application Development - Term Project (20 Points)  
**Project Path:** `D:\954348_Project\asset-management`  
**Repository State:** Operational & Verified (34/34 PHPUnit Tests Passing)

---

## 1. Project Mission & Academic Constraints

This application is built to satisfy strict academic criteria for course **954348**. Any AI coding assistant (Codex, Copilot, etc.) working on this repository **MUST** adhere to the following hard constraints:

1. **Rendering Engine:** Must use **Blade Templates ONLY**.
   * **STRICT BAN:** Do NOT convert the project or pages into an SPA (Single Page Application, e.g., React, Vue, Inertia, or Angular).
2. **Custom Eloquent Models (At least 3 models, not counting `User`):**
   * `Category` $\rightarrow$ `hasMany(Asset)`
   * `Asset` $\rightarrow$ `belongsTo(Category)`, `hasMany(BorrowRecord)`
   * `BorrowRecord` $\rightarrow$ `belongsTo(User)`, `belongsTo(Asset)`
3. **Full CRUD for all 3 models:**
   * Category: Create, Read, Update, Delete (Admin)
   * Asset: Create, Read, Update, Delete (Admin + QR sticker generator)
   * BorrowRecord: Create (Staff borrow request), Read (Staff own history & Admin all requests), Update (Admin approve / return), Delete (Staff cancel pending request).
4. **Session Flash Notifications & Error Handling:**
   * Every database manipulation must return flash messages via `session()->flash('success', ...)` or `with('error', ...)`.
   * Form validation errors must be handled and displayed clearly.
5. **Authentication & Authorization (2 Distinct Roles):**
   * `admin` (เจ้าหน้าที่พัสดุ): Full CRUD, borrow approvals, return processing.
   * `user` (พนักงานทั่วไป / Staff): Browsing, borrowing, tracking personal records.
   * **Public Routes:** Anyone can browse the asset catalog (`/assets`, `/assets/{id}`) without authentication.
   * **Protected Routes:** Guaranteed via `auth` and `admin` middlewares (`EnsureUserIsAdmin`).
6. **Styling:** Tailwind CSS with responsive layout and Thai typography (Prompt font).
7. **Third-Party PHP Package (Not taught in lectures):**
   * Package: `simplesoftwareio/simple-qrcode` (^4.2).
   * Purpose: Generates printable QR Code stickers for physical asset tracking.
8. **Student Oral Defense Warning:**
   * All code must be clear, clean, idiomatic Laravel, and easy for undergraduate students to explain to the professor. Avoid excessive metaprogramming or obscure abstractions.

---

## 2. Directory Structure & Key Files

```text
D:\954348_Project\
├── Term_Project.pdf                   # Official project assignment & grading rubric
├── lec/                               # Course lectures 01-12 (reference for what was taught)
└── asset-management/                  # Laravel application root
    ├── app/
    │   ├── Http/
    │   │   ├── Controllers/
    │   │   │   ├── AssetController.php         # Public catalog, Admin CRUD, QR Code generation
    │   │   │   ├── BorrowRecordController.php  # Borrow workflow, approval, return, status sync
    │   │   │   └── CategoryController.php      # Category CRUD + asset constraint guard
    │   │   └── Middleware/
    │   │       └── EnsureUserIsAdmin.php       # Role guard for 'admin' (alias: 'admin')
    │   └── Models/
    │       ├── User.php                        # Includes 'role' ('admin'|'user'), isAdmin() helper
    │       ├── Category.php                    # id, name, code, description
    │       ├── Asset.php                       # id, category_id, asset_code, name, price, status, image
    │       └── BorrowRecord.php                # id, user_id, asset_id, borrow_date, return_date, status
    ├── database/
    │   ├── database.sqlite                     # Active database storage
    │   ├── migrations/                         # Schema definitions
    │   └── seeders/DatabaseSeeder.php          # Demo accounts and sample inventory
    ├── resources/
    │   └── views/
    │       ├── layouts/
    │       │   ├── app.blade.php               # Main shell with Prompt font & flash messages
    │       │   └── navigation.blade.php        # Responsive navbar with role-aware tabs & guest links
    │       ├── components/
    │       │   └── flash-messages.blade.php    # Success/Error alert banners & validation errors
    │       ├── assets/                         # index.blade.php, show, create, edit, print-qr
    │       ├── borrows/                        # create.blade.php, my-borrows, index (Admin view)
    │       └── categories/                     # index.blade.php, create, edit
    ├── routes/
    │   ├── web.php                             # Public, Protected, and Admin route groups
    │   └── auth.php                            # Laravel Breeze authentication routes
    └── tests/
        └── Feature/
            └── AssetManagementTest.php         # 34 tests covering permissions, CRUD & DB transactions
```

---

## 3. Database Schema Reference

### `users`
* `id` (integer, PK)
* `name` (string)
* `email` (string, unique)
* `password` (string, hashed)
* `role` (string: `'admin'` | `'user'`, default: `'user'`)
* `remember_token`, `timestamps`

### `categories`
* `id` (integer, PK)
* `name` (string)
* `code` (string, unique) - e.g., 'IT', 'FURN', 'AV'
* `description` (text, nullable)
* `timestamps`

### `assets`
* `id` (integer, PK)
* `category_id` (FK $\rightarrow$ `categories.id`, onDelete cascade)
* `asset_code` (string, unique) - e.g., 'IT-2026-001'
* `name` (string)
* `serial_number` (string, nullable)
* `price` (decimal 10,2)
* `purchase_date` (date)
* `status` (string: `'available'` | `'borrowed'` | `'maintenance'` | `'retired'`, default: `'available'`)
* `image` (string, nullable) - stored under `public/assets`
* `description` (text, nullable)
* `timestamps`

### `borrow_records`
* `id` (integer, PK)
* `user_id` (FK $\rightarrow$ `users.id`, onDelete cascade)
* `asset_id` (FK $\rightarrow$ `assets.id`, onDelete cascade)
* `borrow_date` (date)
* `expected_return_date` (date)
* `actual_return_date` (date, nullable)
* `status` (string: `'pending'` | `'approved'` | `'returned'` | `'rejected'`, default: `'pending'`)
* `note` (text, nullable)
* `timestamps`

---

## 4. Key Business Logic & State Transitions

1. **Borrow Request:**
   * Employee creates a request $\rightarrow$ `borrow_records.status = 'pending'`.
   * Asset remains `'available'` until officially approved.
2. **Admin Approval:**
   * Wrapped in `DB::transaction()`:
     * `borrow_record.status = 'approved'`
     * `asset.status = 'borrowed'`
3. **Return Processing:**
   * Wrapped in `DB::transaction()`:
     * `borrow_record.status = 'returned'`
     * `borrow_record.actual_return_date = now()`
     * `asset.status = 'available'`
4. **Referential Integrity Guards:**
   * Cannot delete a `Category` if it still has associated assets (`$category->assets()->count() > 0`).
   * Cannot delete an `Asset` if it is currently `'borrowed'`.

---

## 5. Development & Testing Commands for Codex

When working in Windows PowerShell on this machine:

* **Start Local Server:**
  ```powershell
  cd D:\954348_Project\asset-management
  php artisan serve
  ```
* **Run Test Suite:**
  ```powershell
  php artisan test
  ```
* **Compile Frontend Assets (Use `npm.cmd` due to Windows PowerShell ExecutionPolicy):**
  ```powershell
  npm.cmd run build
  ```
* **Reset Database & Load Demo Data:**
  ```powershell
  php artisan migrate:fresh --seed
  ```

---

## 6. Pre-Configured Test Credentials

| Role | Email | Password | Access Capabilities |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@asset.local` | `password123` | Full access to categories, assets, approvals, returns |
| **Staff** | `staff@asset.local` | `password123` | Asset catalog, request borrow, personal history |
