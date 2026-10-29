<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\InventorySurvey;
use Illuminate\Database\Seeder;

class DemoAssetSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['IT', 'IT-2026-101', 'คอมพิวเตอร์ตั้งโต๊ะ Dell OptiPlex 7010', 'DL-OP7010-2401', 32900, '2026-01-20', 'desktop', 'คอมพิวเตอร์ Intel Core i5, RAM 16GB, SSD 512GB พร้อมจอภาพ 24 นิ้ว สำหรับงานสำนักงาน'],
            ['IT', 'IT-2026-102', 'โน้ตบุ๊ก HP ProBook 440 G10', 'HP-PB440-2402', 28900, '2026-02-03', 'laptop', 'Intel Core i5 รุ่นที่ 13, RAM 16GB, SSD 512GB หน้าจอ 14 นิ้ว เหมาะสำหรับงานเอกสารและประชุมออนไลน์'],
            ['IT', 'IT-2026-103', 'จอภาพ LG UltraWide 29 นิ้ว', 'LG-UW29-2403', 11900, '2026-02-18', 'monitor', 'จอ IPS ความละเอียด Full HD อัตราส่วน 21:9 มีพอร์ต HDMI และ DisplayPort'],
            ['IT', 'IT-2026-104', 'เครื่องพิมพ์เลเซอร์ Brother HL-L2370DN', 'BR-HL2370-2404', 7490, '2026-03-01', 'printer', 'เครื่องพิมพ์ขาวดำระบบเลเซอร์ พิมพ์สองหน้าอัตโนมัติ รองรับเครือข่าย Ethernet'],
            ['IT', 'IT-2026-105', 'เครื่องสแกนเอกสาร Epson WorkForce DS-530 II', 'EP-DS530-2405', 15900, '2026-03-14', 'scanner', 'สแกนสองหน้าอัตโนมัติ ความเร็วสูงสุด 35 หน้าต่อนาที รองรับเอกสารหลายขนาด'],
            ['IT', 'IT-2026-106', 'แท็บเล็ต Samsung Galaxy Tab S9 FE', 'SM-TAB9FE-2406', 16900, '2026-04-02', 'tablet', 'หน้าจอ 10.9 นิ้ว ความจุ 128GB รองรับปากกา S Pen และ Wi-Fi สำหรับงานภาคสนาม'],
            ['IT', 'IT-2026-107', 'เครื่องสำรองไฟ APC Back-UPS 1100VA', 'APC-BX1100-2407', 5890, '2026-04-19', 'ups', 'กำลังไฟ 1100VA/660W พร้อมระบบป้องกันไฟกระชากและสำรองไฟสำหรับคอมพิวเตอร์'],
            ['IT', 'IT-2026-108', 'สวิตช์เครือข่าย TP-Link JetStream 24 Port', 'TP-SG2428-2408', 8900, '2026-05-05', 'network', 'สวิตช์ Gigabit 24 พอร์ต รองรับ VLAN และการจัดการผ่านหน้าเว็บสำหรับเครือข่ายสำนักงาน'],
            ['IT', 'IT-2026-109', 'เราเตอร์ไร้สาย ASUS RT-AX3000', 'AS-AX3000-2409', 4790, '2026-05-22', 'router', 'เราเตอร์ Wi-Fi 6 แบบ Dual Band รองรับการเชื่อมต่อหลายอุปกรณ์และระบบรักษาความปลอดภัย AiProtection'],
            ['IT', 'IT-2026-110', 'เว็บแคม Logitech Brio 4K', 'LG-BRIO4K-2410', 6900, '2026-06-07', 'webcam', 'กล้องประชุมความละเอียด 4K พร้อมไมโครโฟนคู่ ระบบปรับแสงอัตโนมัติ และฝาปิดเลนส์'],
            ['AV', 'AV-2026-101', 'โปรเจกเตอร์ BenQ TH575 Full HD', 'BQ-TH575-2411', 25900, '2026-01-28', 'projector', 'ความละเอียด Full HD ความสว่าง 3800 ANSI Lumens สำหรับห้องเรียนและห้องประชุม'],
            ['AV', 'AV-2026-102', 'ลำโพงประชุม JBL EON710', 'JBL-EON710-2412', 21900, '2026-02-22', 'speaker', 'ลำโพง Active ขนาด 10 นิ้ว กำลังขับ 1300W มี Bluetooth และ DSP ในตัว'],
            ['AV', 'AV-2026-103', 'ชุดไมโครโฟนไร้สาย Shure BLX24/SM58', 'SH-BLX24-2413', 16900, '2026-03-11', 'microphone', 'ชุดไมโครโฟนไร้สายพร้อมเครื่องรับสัญญาณ เหมาะสำหรับบรรยายและกิจกรรมในห้องประชุม'],
            ['AV', 'AV-2026-104', 'กล้องวิดีโอ Canon XA60', 'CN-XA60-2414', 68900, '2026-04-08', 'camera', 'กล้องบันทึกวิดีโอ UHD 4K เลนส์ซูม 20 เท่า พร้อมระบบกันสั่น สำหรับบันทึกกิจกรรม'],
            ['AV', 'AV-2026-105', 'จอแสดงผล Samsung 65 นิ้ว 4K', 'SM-QE65-2415', 32900, '2026-05-16', 'television', 'จอ UHD 4K ขนาด 65 นิ้ว พร้อมพอร์ต HDMI หลายช่อง สำหรับนำเสนอข้อมูลในห้องประชุม'],
            ['FURN', 'FURN-2026-101', 'โต๊ะทำงานปรับระดับไฟฟ้า 140 ซม.', 'FR-DESK140-2416', 18900, '2026-01-12', 'desk', 'หน้าโต๊ะลายไม้ ขาปรับระดับไฟฟ้า รองรับการทำงานทั้งท่านั่งและยืน'],
            ['FURN', 'FURN-2026-102', 'เก้าอี้สำนักงานตาข่ายปรับเอนได้', 'FR-CHAIR-2417', 7900, '2026-02-15', 'chair', 'พนักพิงตาข่าย ระบายอากาศได้ดี ปรับความสูงและเอนได้ มีที่รองรับหลังส่วนล่าง'],
            ['FURN', 'FURN-2026-103', 'ตู้เอกสารเหล็ก 4 ลิ้นชัก', 'FR-CAB4-2418', 6200, '2026-03-20', 'cabinet', 'ตู้เหล็กพ่นสีพร้อมระบบล็อกกลาง 4 ลิ้นชัก ใช้จัดเก็บเอกสารสำนักงาน'],
            ['FURN', 'FURN-2026-104', 'โต๊ะประชุม 8 ที่นั่ง', 'FR-MEET8-2419', 14500, '2026-04-25', 'table', 'โต๊ะประชุมหน้าเมลามีนพร้อมขาเหล็ก รองรับผู้ใช้งาน 8 คน เหมาะสำหรับห้องประชุมขนาดกลาง'],
            ['FURN', 'FURN-2026-105', 'ชั้นวางหนังสือและแฟ้ม 5 ชั้น', 'FR-SHELF5-2420', 4900, '2026-06-12', 'shelf', 'ชั้นเหล็ก 5 ชั้น ปรับระยะชั้นได้ รับน้ำหนักกระจายได้สูงสุด 50 กก. ต่อชั้น'],
        ];

        foreach ($items as [$categoryCode, $assetCode, $name, $serial, $price, $purchaseDate, $imageName, $description]) {
            $category = Category::where('code', $categoryCode)->firstOrFail();

            $asset = Asset::updateOrCreate(
                ['asset_code' => $assetCode],
                [
                    'category_id' => $category->id,
                    'name' => $name,
                    'serial_number' => $serial,
                    'price' => $price,
                    'purchase_date' => $purchaseDate,
                    'status' => 'available',
                    'image' => "assets/demo/{$imageName}.jpg",
                    'description' => $description,
                ]
            );

            InventorySurvey::query()
                ->where('year', now()->year)
                ->whereNull('completed_at')
                ->first()
                ?->items()
                ->firstOrCreate(['asset_id' => $asset->id]);
        }
    }
}
