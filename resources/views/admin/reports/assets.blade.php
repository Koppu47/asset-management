<!doctype html>
<html lang="th"><head><meta charset="utf-8"><title>รายงานทะเบียนครุภัณฑ์</title><style>body{font:14px Arial,sans-serif;color:#172033;margin:2rem}h1{font-size:22px}.toolbar{margin-bottom:1rem}button,a{padding:.6rem 1rem}table{width:100%;border-collapse:collapse;font-size:12px}th,td{border:1px solid #aab2c0;padding:7px;text-align:left}th{background:#edf0f5}@media print{body{margin:1cm;font-size:11px}.toolbar{display:none}th{background:#edf0f5!important;print-color-adjust:exact}}</style></head>
<body>
    <div class="toolbar"><button onclick="window.print()">พิมพ์ / บันทึก PDF</button> <a href="{{ route('admin.dashboard') }}">กลับหน้าหลัก</a></div>
    <h1>รายงานทะเบียนครุภัณฑ์</h1><p>จัดทำเมื่อ {{ now()->format('d/m/Y H:i') }} · จำนวน {{ $assets->count() }} รายการ</p>
    <table><thead><tr>@foreach (['รหัส','ชื่อครุภัณฑ์','หมวดหมู่','สถานะ','ราคา','แผนก','อาคาร/ห้อง','ผู้รับผิดชอบ'] as $heading)<th>{{ $heading }}</th>@endforeach</tr></thead><tbody>
    @foreach ($assets as $asset)<tr><td>{{ $asset->asset_code }}</td><td>{{ $asset->name }}</td><td>{{ $asset->category?->name }}</td><td>{{ $asset->status }}</td><td>{{ number_format((float) $asset->price, 2) }}</td><td>{{ $asset->department }}</td><td>{{ trim(($asset->building ?? '').' '.($asset->room ?? '')) }}</td><td>{{ $asset->custodian?->name }}</td></tr>@endforeach
    </tbody></table>
</body></html>
