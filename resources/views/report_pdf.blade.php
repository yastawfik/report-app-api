<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            margin: 40px;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #A45B17;
            margin: 10px 0 5px;
        }

        .meta-info {
            margin-top: 20px;
            line-height: 1.6;
        }

        .meta {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        th {
            background-color: #F5E8D8;
            color: #5C3D1E;
            font-weight: bold;
            padding: 8px;
            border: 1px solid #ccc;
        }

        td {
            padding: 7px;
            border: 1px solid #ccc;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #fdf6ef;
        }

        .footer {
            margin-top: 30px;
            font-style: italic;
            font-size: 12px;
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
