<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\BorrowRecord;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. สร้างบัญชีผู้ใช้เริ่มต้น
        $admin = User::firstOrCreate(
            ['email' => 'admin@asset.local'],
            [
                'name' => 'เจ้าหน้าที่พัสดุ (Admin)',
                'password' => Hash::make('password123'),
            ]
        );
        $admin->forceFill(['role' => 'admin'])->save();

        $staff = User::firstOrCreate(
            ['email' => 'staff@asset.local'],
            [
                'name' => 'สมชาย มีสุข (Staff)',
                'password' => Hash::make('password123'),
            ]
        );
        $staff->forceFill(['role' => 'user'])->save();

        // 2. สร้างหมวดหมู่ครุภัณฑ์
        $catIT = Category::firstOrCreate(
            ['code' => 'IT'],
            [
                'name' => 'อุปกรณ์เทคโนโลยีและคอมพิวเตอร์',
                'description' => 'คอมพิวเตอร์ โน้ตบุ๊ก จอภาพ แท็บเล็ต และอุปกรณ์ต่อพ่วง',
            ]
        );

        $catFurn = Category::firstOrCreate(
            ['code' => 'FURN'],
            [
                'name' => 'เฟอร์นิเจอร์สำนักงาน',
                'description' => 'โต๊ะทำงาน เก้าอี้สุขภาพ ตู้เอกสาร และชั้นวางของ',
            ]
        );

        $catElec = Category::firstOrCreate(
            ['code' => 'AV'],
            [
                'name' => 'โสตทัศนูปกรณ์และเครื่องใช้ไฟฟ้า',
                'description' => 'โปรเจกเตอร์ ลำโพง ไมโครโฟนไร้สาย และกล้องถ่ายทอดสด',
            ]
        );

        // 3. สร้างรายการครุภัณฑ์
        $asset1 = Asset::firstOrCreate(
            ['asset_code' => 'IT-2026-001'],
            [
                'category_id' => $catIT->id,
                'name' => 'โน้ตบุ๊ก Lenovo ThinkPad T14 Gen 3',
                'serial_number' => 'PF-3XYZ99',
                'price' => 38900.00,
                'purchase_date' => '2026-01-15',
                'status' => 'borrowed',
                'description' => 'Intel Core i7-1260P, RAM 16GB, SSD 512GB สำหรับยืมใช้นอกสถานที่',
            ]
        );

        $asset2 = Asset::firstOrCreate(
            ['asset_code' => 'IT-2026-002'],
            [
                'category_id' => $catIT->id,
                'name' => 'จอแสดงผล Dell UltraSharp 27" 4K',
                'serial_number' => 'CN-0K791M',
                'price' => 19500.00,
                'purchase_date' => '2026-02-10',
                'status' => 'available',
                'description' => 'จอ IPS 4K Type-C Hub 90W สำหรับต่อขยายการทำงาน',
            ]
        );

        $asset3 = Asset::firstOrCreate(
            ['asset_code' => 'AV-2026-001'],
            [
                'category_id' => $catElec->id,
                'name' => 'โปรเจกเตอร์พกพา Epson EB-FH52 Full HD',
                'serial_number' => 'EPS-440182',
                'price' => 32000.00,
                'purchase_date' => '2026-03-05',
                'status' => 'available',
                'description' => 'ความสว่าง 4,000 ลูเมน รองรับ Wireless สำหรับห้องประชุม',
            ]
        );

        $asset4 = Asset::firstOrCreate(
            ['asset_code' => 'FURN-2026-001'],
            [
                'category_id' => $catFurn->id,
                'name' => 'เก้าอี้เพื่อสุขภาพ Ergonomic Chair รุ่น Pro',
                'serial_number' => 'FUR-88210',
                'price' => 14500.00,
                'purchase_date' => '2026-04-12',
                'status' => 'available',
                'description' => 'พนักพิงปรับตามสรีระ รองรับน้ำหนัก 150 กก.',
            ]
        );

        // 4. เพิ่มรายการตัวอย่างพร้อมภาพประกอบในเครื่อง
        $this->call(DemoAssetSeeder::class);

        // 4.1 เติมตำแหน่งและผู้รับผิดชอบให้ข้อมูลตัวอย่าง
        $this->call(DemoAssetCustodySeeder::class);

        // 5. สร้างบันทึกการยืม-คืน
        BorrowRecord::firstOrCreate(
            [
                'user_id' => $staff->id,
                'asset_id' => $asset1->id,
                'borrow_date' => '2026-10-01',
            ],
            [
                'expected_return_date' => '2026-10-10',
                'status' => 'approved',
                'note' => 'ยืมไปใช้ในการประชุมสัมมนาและฝึกอบรมต่างจังหวัด',
            ]
        );
    }
}
