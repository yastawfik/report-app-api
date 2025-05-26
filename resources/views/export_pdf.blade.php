<!DOCTYPE html>
<html>
<head>
    <title>Rapport Exporté</title>
</head>
<body style="font-family: DejaVu Sans, Arial, sans-serif; margin: 30px auto; max-width: 900px; background: #f5f7fa; color: #2c3e50;">

    <h1 style="text-align: center; font-weight: 700; font-size: 32px; margin-bottom: 30px; letter-spacing: 1.2px; color: #34495e; text-transform: uppercase;">
        Rapports Filtrés
    </h1>

    <table style="width: 100%; border-collapse: collapse; border: 2px solid #2980b9; border-radius: 8px; background: white; box-shadow: 0 4px 8px rgba(41, 128, 185, 0.2);">
        <tbody>
            <tr style="background-color: #2980b9; color: white; text-transform: uppercase; font-weight: 700; letter-spacing: 0.1em;">
                <th style="border: 1px solid #2980b9; padding: 14px 18px; text-align: left;">ID</th>
                <th style="border: 1px solid #2980b9; padding: 14px 18px; text-align: left;">Date</th>
                <th style="border: 1px solid #2980b9; padding: 14px 18px; text-align: left;">Utilisateur</th>
                <th style="border: 1px solid #2980b9; padding: 14px 18px; text-align: left;">Validé</th>
            </tr>
            @foreach ($reports as $report)
                <tr style="{{ $loop->even ? 'background-color:#ecf0f1;' : '' }}">
                    <td style="border: 1px solid #2980b9; padding: 14px 18px;">{{ $report->id }}</td>
                    <td style="border: 1px solid #2980b9; padding: 14px 18px;">{{ $report->created_at->format('Y-m-d') }}</td>
                    <td style="border: 1px solid #2980b9; padding: 14px 18px;">{{ $report->username }}</td>
                    <td style="border: 1px solid #2980b9; padding: 14px 18px; text-align: center; font-weight: 700; color: {{ $report->locked ? '#e74c3c' : '#27ae60' }};">
                        {{ $report->locked ? 'Oui' : 'Non' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
