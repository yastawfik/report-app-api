<!-- resources/views/reports/pdf.blade.php -->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport PDF</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            font-size: 28px;
            color: #2C3E50;
            margin-bottom: 20px;
        }

        h3 {
            font-size: 22px;
            color: #34495E;
            margin-top: 30px;
        }

        p {
            font-size: 16px;
            margin: 10px 0;
            line-height: 1.6;
        }

        strong {
            color: #2980B9;
        }

        ul {
            list-style: none;
            padding: 0;
            text-align: left;
            margin: 20px 0;
        }

        ul li {
            font-size: 16px;
            background-color: #ECF0F1;
            padding: 8px 12px;
            margin-bottom: 8px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .footer {
            margin-top: 40px;
            font-size: 14px;
            color: #7F8C8D;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Rapport de Poids des Briques</h2>

        <p><strong>Zone:</strong> {{ $report->zone }}</p>
        <p><strong>Type de Briques:</strong> {{ $report->brick_type ?? 'N/A' }}</p>
        <p><strong>Date:</strong> {{ $report->datetime->format('d/m/Y H:i') }}</p>
        <p><strong>Poids Moyen:</strong> {{ $report->average_weight ?? 'N/A' }} kg</p>

        <h3>Poids des Briques:</h3>
        <ul>
            @php
                $weights = json_decode($report->weights, true); // Decode the JSON string into an array
            @endphp
            @foreach ($weights as $index => $weight)
                <li>
                    <span>Brique {{ $index + 1 }}:</span>
                    <span>{{ $weight }} kg</span>
                </li>
            @endforeach
        </ul>
    </div>
</body>
</html>
