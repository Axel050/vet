<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de Atención - {{ $pet->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .workshop-name {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
            text-transform: uppercase;
            margin: 0;
        }

        .subtitle {
            font-size: 15px;
            color: #666;
            font-style: italic;
        }

        .info-grid {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }

        .info-cell {
            width: 50%;
            vertical-align: top;
            padding: 10px;
        }

        .label {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #999;
            display: block;
            margin-bottom: 5px;
        }

        .value {
            font-size: 16px;
            font-weight: bold;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #333;
        }

        .record {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 8px;
        }

        .record-header {
            display: block;
            margin-bottom: 10px;
        }

        .record-date {
            color: #4f46e5;
            font-size: 12px;
            font-weight: bold;
        }

        .record-service {
            font-size: 16px;
            font-weight: bold;
            margin-top: 2px;
        }

        .record-mileage {
            font-size: 12px;
            color: #666;
            float: right;
        }

        .record-notes {
            font-size: 12px;
            color: #555;
            background: #f9f9f9;
            padding: 10px;
            border-left: 3px solid #ddd;
            margin-top: 10px;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #999;
            margin-top: 50px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div style="margin-bottom: 20px;">
            <img src="{{ public_path('assets/logo-v.png') }}" style="height: 80px; width: auto;" alt="Logo">
        </div>
        <h1 class="workshop-name">{{ $veterinary->name }}</h1>
        <p class="subtitle">Historial de Atención</p>
    </div>

    <table class="info-grid">
        <tr>
            <td class="info-cell">
                <span class="label">Mascota</span>
                <span class="value">{{ $pet->name }}</span>
                <div style="font-size: 13px; color: #666;">
                    {{ $pet->species?->name }} {{ $pet->breed?->name }} ({{ $pet->birth_year }})
                </div>
            </td>
            <td class="info-cell">
                <span class="label">Dueño</span>
                <span class="value">{{ $pet->customer->name }}</span>
            </td>
        </tr>
    </table>

    <h2 class="section-title">Registros de Atención</h2>

    @foreach ($pet->medicalRecords as $record)
        <div class="record">
            <div class="record-header">
                <span class="record-date">{{ $record->performed_at->format('d/m/Y') }}</span>
                <div class="record-service">
                    {{ $record->service ? $record->service->name : $record->custom_service_name }}
                </div>
            </div>
            @if ($record->notes)
                <div class="record-notes">{{ $record->notes }}</div>
            @endif
        </div>
    @endforeach

    <div class="footer">
        Documento generado por {{ config('app.name') }} el {{ now()->format('d/m/Y H:i') }}<br>
        &copy; {{ date('Y') }} {{ $veterinary->name }}
    </div>
</body>

</html>
