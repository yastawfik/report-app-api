<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
   <style>
    @page {
        margin: 20px;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
        margin: 0;
        padding: 0;
        color: #333;
    }

    h1 {
        text-align: center;
        color: #A45B17;
        margin: 10px 0 5px;
    }

    .meta-info {
        margin: 10px 40px;
        line-height: 1.5;
    }

    .meta {
        font-weight: bold;
    }

    table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
        margin: 25px 20px 0;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        word-wrap: break-word;
    }

    th, td {
        border: 1px solid #ccc;
        padding: 6px;
        text-align: center;
        font-size: 11px;
        word-break: break-word;
    }

    th {
        background-color: #F5E8D8;
        color: #5C3D1E;
        font-weight: bold;
    }

    tr:nth-child(even) {
        background-color: #fdf6ef;
    }

    .footer {
        margin: 30px 40px 0;
        font-style: italic;
        font-size: 11px;
        text-align: right;
    }
</style>
</head>
<body>

    <h1>Rapport Poids des Briques</h1>

    <div class="meta-info">
        <p class="meta">Rapport ID : {{ $report->id }}</p>
        <p class="meta">Date : {{ $report->created_at->format('Y-m-d') }}</p>
        <p class="meta">Heure : {{ $report->created_at->format('H:i') }}</p>
        <p class="meta">Fait par : {{ $report->user->name ?? 'Inconnu' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Zone</th>
                <th>Type de Brique</th>
                @for ($i = 1; $i <= $maxWeightCount; $i++)
                    <th>Poids {{ $i }} (kg)</th>
                @endfor
                <th>Moyenne (kg)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report->subreports as $sub)
                @php
                    $weights = is_array($sub->weights) ? $sub->weights : json_decode($sub->weights, true);
                    $weights = is_array($weights) ? $weights : [];
                    $average = count($weights) ? array_sum($weights) / count($weights) : 0;
                @endphp
                <tr>
                    <td>{{ $sub->zone ?? 'N/A' }}</td>
                    <td>{{ $sub->brick_type ?? 'N/A' }}</td>
                    @foreach ($weights as $w)
                        <td>{{ number_format($w, 3) }}</td>
                    @endforeach
                    @for ($i = count($weights); $i < $maxWeightCount; $i++)
                        <td>-</td>
                    @endfor
                    <td><strong>{{ number_format($average, 3) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">Document généré le {{ now()->format('d/m/Y') }}</p>
</body>
</html>
