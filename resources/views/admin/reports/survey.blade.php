<!doctype html>
<html lang="th"><head><meta charset="utf-8"><title>รายงานสำรวจครุภัณฑ์ {{ $inventorySurvey->year }}</title><style>body{font:14px Arial,sans-serif;color:#172033;margin:2rem}h1{font-size:22px}.toolbar{margin-bottom:1rem}button{padding:.6rem 1rem}table{width:100%;border-collapse:collapse;font-size:12px}th,td{border:1px solid #aab2c0;padding:7px;text-align:left}th{background:#edf0f5}@media print{body{margin:1cm;font-size:11px}.toolbar{display:none}th{background:#edf0f5!important;print-color-adjust:exact}}</style></head>
<body>
    <div class="toolbar"><button onclick="window.print()">พิมพ์ / บันทึก PDF</button></div>
    <h1>รายงานสำรวจครุภัณฑ์ประจำปี {{ $inventorySurvey->year }}</h1><p>สถานะ: {{ $inventorySurvey->completed_at ? 'ปิดรอบ '.$inventorySurvey->completed_at->format('d/m/Y') : 'กำลังดำเนินการ' }} · เริ่มโดย {{ $inventorySurvey->startedBy?->name }}</p>
    <table><thead><tr><th>รหัส</th><th>ชื่อครุภัณฑ์</th><th>ผลตรวจ</th><th>หมายเหตุ</th><th>ติดตามผล</th><th>ผู้รับผิดชอบติดตาม</th></tr></thead><tbody>
    @foreach ($inventorySurvey->items as $item)<tr><td>{{ $item->asset->asset_code }}</td><td>{{ $item->asset->name }}</td><td>{{ $item->status }}</td><td>{{ $item->note }}</td><td>{{ $item->status === 'missing' ? $item->resolution_status : '—' }} {{ $item->resolution_note }}</td><td>{{ $item->assignee?->name }}</td></tr>@endforeach
    </tbody></table>
</body></html>
