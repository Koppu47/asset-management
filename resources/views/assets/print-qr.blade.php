<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สติกเกอร์ครุภัณฑ์ - {{ $asset->asset_code }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Prompt', sans-serif;
        }
        body {
            background-color: #f1f5f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }
        .no-print {
            margin-bottom: 24px;
        }
        .btn {
            background: #4f46e5;
            color: #fff;
            border: none;
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }
        .sticker-card {
            width: 380px;
            background: #fff;
            border: 2px dashed #94a3b8;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .org-title {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .qr-box {
            display: flex;
            justify-content: center;
            margin: 12px 0;
        }
        .qr-box svg {
            width: 170px;
            height: 170px;
        }
        .code-title {
            font-family: monospace;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .asset-name {
            font-size: 14px;
            color: #334155;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .sub-info {
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 8px;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .sticker-card {
                border: 1px solid #000;
                box-shadow: none;
                margin: 0 auto;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn">สั่งพิมพ์สติกเกอร์ (Print Label)</button>
    </div>

    <div class="sticker-card">
        <div class="org-title">ป้ายระบุครุภัณฑ์องค์กร (ORGANIZATION ASSET)</div>
        
        <div class="code-title">{{ $asset->asset_code }}</div>
        <div class="asset-name">{{ $asset->name }}</div>

        <div class="qr-box">
            {!! $qrCode !!}
        </div>

        <div class="sub-info">
            หมวดหมู่: {{ $asset->category->name }} | ตรวจรับ: {{ $asset->purchase_date->format('d/m/Y') }}
        </div>
    </div>

</body>
</html>
