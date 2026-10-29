# เอกสารสรุปโครงการระบบจัดการครุภัณฑ์องค์กร (สำหรับนำเสนอและตอบคำถามอาจารย์)

> **วิชา:** 954348 Term Project (คะแนนเต็ม 20 คะแนน)  
> **ที่ตั้งโปรเจกต์:** `D:\954348_Project\asset-management`

---

## 1. ข้อมูลบัญชีผู้ใช้สำหรับทดสอบ (Demo Accounts)

ระบบได้เตรียมข้อมูลจำลองเริ่มต้น (Seed Data) ให้พร้อมใช้งานทันที:

| บทบาท (Role) | อีเมล (Email) | รหัสผ่าน (Password) | สิทธิ์การเข้าถึง |
| :--- | :--- | :--- | :--- |
| **เจ้าหน้าที่พัสดุ (Admin)** | `admin@asset.local` | `password123` | จัดการหมวดหมู่ (CRUD), ครุภัณฑ์ (CRUD), อนุมัติการยืม และบันทึกรับคืน |
| **พนักงานทั่วไป (Staff)** | `staff@asset.local` | `password123` | ดูแคตตาล็อก, ยื่นคำร้องขอยืม, ดูประวัติและยกเลิกคำขอของตนเอง |
| **บุคคลภายนอก (Guest)** | *(ไม่ต้องล็อกอิน)* | - | ค้นหาและดูรายละเอียดแคตตาล็อกครุภัณฑ์ และสแกน QR Code |

---

## 2. วิธีการเปิดใช้งานระบบ (How to Run)

เปิด Terminal ในโฟลเดอร์ `D:\954348_Project\asset-management` แล้วรันคำสั่ง:

```bash
php artisan serve
```

เปิด Browser ไปที่: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 3. สรุปจุดเชื่อมโยงกับเกณฑ์การให้คะแนน (Mapping to Requirements)

### ข้อ 1: Eloquent Models อย่างน้อย 3 โมเดล + Relationships (2 คะแนน)
* **โมเดลที่ 1:** [`Category`](file:///D:/954348_Project/asset-management/app/Models/Category.php) (หมวดหมู่ครุภัณฑ์)  
  * ความสัมพันธ์: `hasMany(Asset::class)`
* **โมเดลที่ 2:** [`Asset`](file:///D:/954348_Project/asset-management/app/Models/Asset.php) (รายการครุภัณฑ์)  
  * ความสัมพันธ์: `belongsTo(Category::class)` และ `hasMany(BorrowRecord::class)`
* **โมเดลที่ 3:** [`BorrowRecord`](file:///D:/954348_Project/asset-management/app/Models/BorrowRecord.php) (ประวัติการยืม-คืน)  
  * ความสัมพันธ์: `belongsTo(User::class)` และ `belongsTo(Asset::class)`

---

### ข้อ 2: ระบบ CRUD ครบทั้ง 3 โมเดล (6 คะแนน)
1. **Category CRUD:** ใน [`CategoryController.php`](file:///D:/954348_Project/asset-management/app/Http/Controllers/CategoryController.php)  
   * Create (`create`, `store`), Read (`index`), Update (`edit`, `update`), Delete (`destroy`)
2. **Asset CRUD:** ใน [`AssetController.php`](file:///D:/954348_Project/asset-management/app/Http/Controllers/AssetController.php)  
   * Create (`create`, `store`), Read (`index`, `show`, `printQr`), Update (`edit`, `update`), Delete (`destroy`)
3. **BorrowRecord CRUD:** ใน [`BorrowRecordController.php`](file:///D:/954348_Project/asset-management/app/Http/Controllers/BorrowRecordController.php)  
   * Create (พนักงานยื่นขอยืม `store`), Read (พนักงานดูประวัติตนเอง `myBorrows` / แอดมินดูทั้งหมด `index`), Update (แอดมินอนุมัติ `approve` และรับคืน `returnAsset`), Delete (พนักงานยกเลิกคำขอ `cancel`)

---

### ข้อ 3: ระบบแจ้งเตือน (Notifications via Session) & Error Handling (2 คะแนน)
* มีการใช้ `session()->flash('success', ...)` และ `session()->flash('error', ...)` ในทุก Controller
* ดักจับข้อผิดพลาด เช่น ไม่อนุญาตให้ลบ Category หากยังมี Asset ผูกอยู่, ไม่อนุญาตให้ลบ Asset ขณะกำลังถูกยืม
* Component แสดงผลการแจ้งเตือนและ Form Errors อยู่ที่ [`flash-messages.blade.php`](file:///D:/954348_Project/asset-management/resources/views/components/flash-messages.blade.php)

---

### ข้อ 4: Authentication & 2 Roles + Public Routes (6 คะแนน)
* **ระบบ Login/Register:** ติดตั้งด้วย Laravel Breeze (Blade Stack)
* **2 Roles:** แยกฟิลด์ `role` ในตาราง `users` (`admin` และ `user`)
* **Role Protection Middleware:** [`EnsureUserIsAdmin.php`](file:///D:/954348_Project/asset-management/app/Http/Middleware/EnsureUserIsAdmin.php) เพื่อป้องกันไม่ให้ผู้ใช้ทั่วไปเข้าถึงฟังก์ชันจัดการระบบ
* **Public Routes:** [`web.php`](file:///D:/954348_Project/asset-management/routes/web.php) หน้า `/assets` และ `/assets/{asset}` เข้าดูได้โดยไม่ต้องล็อกอิน

---

### ข้อ 5: การตกแต่งด้วย CSS (2 คะแนน)
* ตกแต่งด้วย **Tailwind CSS** เต็มรูปแบบ รองรับทั้ง Desktop และ Mobile Responsive พร้อมฟอนต์ภาษาไทย Prompt

---

### ข้อ 6: PHP Third-Party Library (2 คะแนน)
* **Library ที่ใช้:** `simplesoftwareio/simple-qrcode` (ติดตั้งผ่าน Composer)
* **การใช้งานในระบบ:** สร้าง SVG QR Code สำหรับสแกนเปิดดูรายละเอียดอุปกรณ์ และพิมพ์สติกเกอร์ติดครุภัณฑ์จริง ใน [`AssetController.php`](file:///D:/954348_Project/asset-management/app/Http/Controllers/AssetController.php) และ [`assets/show.blade.php`](file:///D:/954348_Project/asset-management/resources/views/assets/show.blade.php)
