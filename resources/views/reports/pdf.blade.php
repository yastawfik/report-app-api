<!-- resources/views/reports/pdf.blade.php -->

<h2>Rapport</h2>
<p><strong>Zone:</strong> {{ $report->zone }}</p>
<p><strong>Type de Briques:</strong> {{ $report->brick_type }}</p>
<p><strong>Date:</strong> {{ $report->datetime->format('d/m/Y H:i') }}</p>
<p><strong>Poids Moyen:</strong> {{ $report->average }} kg</p>

<h3>Poids des Briques:</h3>
<ul>
    @foreach ($report->weights as $index => $weight)
        <li>Brique {{ $index + 1 }}: {{ $weight }} kg</li>
    @endforeach
</ul>
