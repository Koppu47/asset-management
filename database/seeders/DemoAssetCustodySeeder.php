<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoAssetCustodySeeder extends Seeder
{
    public function run(): void
    {
        $sampleCustodianId = User::query()->where('email', 'staff@asset.local')->value('id');

        $locations = [
            'IT' => ['department' => 'ฝ่ายเทคโนโลยีสารสนเทศ', 'building' => 'อาคารสำนักงาน', 'room' => 'ห้อง IT'],
            'AV' => ['department' => 'ฝ่ายโสตทัศนศึกษา', 'building' => 'อาคารเรียน', 'room' => 'ห้องโสตฯ'],
            'FURN' => ['department' => 'ฝ่ายบริหารทั่วไป', 'building' => 'อาคารสำนักงาน', 'room' => 'สำนักงานกลาง'],
        ];

        $sampleCodes = [
            'IT-2026-001', 'IT-2026-002', 'AV-2026-001', 'FURN-2026-001',
        ];
        foreach ([101, 102, 103, 104, 105, 106, 107, 108, 109, 110] as $number) {
            $sampleCodes[] = 'IT-2026-'.$number;
            if ($number <= 105) {
                $sampleCodes[] = 'AV-2026-'.$number;
                $sampleCodes[] = 'FURN-2026-'.$number;
            }
        }

        foreach (Asset::query()->whereIn('asset_code', $sampleCodes)->get() as $asset) {
            $prefix = str_starts_with($asset->asset_code, 'FURN-') ? 'FURN' : substr($asset->asset_code, 0, 2);
            $asset->forceFill($locations[$prefix] + ['custodian_user_id' => $sampleCustodianId])->save();
        }
    }
}
