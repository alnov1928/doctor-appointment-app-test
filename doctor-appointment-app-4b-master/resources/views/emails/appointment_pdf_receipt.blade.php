<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; color: #333; }
        .header { background: #E0E7FF; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .details { background: #f9f9f9; padding: 15px; border-radius: 8px; border: 1px solid #ddd; }
        h1 { color: #4F46E5; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #e2e8f0; }
        th { color: #64748b; font-weight: normal; width: 40%; }
        td { font-weight: bold; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <h1> MediMatch</h1>
        <p><strong>Comprobante de Cita Médica</strong></p>
    </div>

    <div class="details">
        <p>A continuación se detallan los datos de la cita registrada en nuestro sistema:</p>

        <table>
            <tr>
                <th>Paciente</th>
                <td>{{ $appointment->patient->user->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Médico Asignado</th>
                <td>Dr(a). {{ $appointment->doctor->user->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Especialidad</th>
                <td>{{ $appointment->doctor->specialty->name ?? 'Medicina General' }}</td>
            </tr>
            <tr>
                <th>Motivo / Síntomas</th>
                <td>{{ $appointment->reason ?? 'No especificado' }}</td>
            </tr>
            <tr>
                <th>Fecha programada</th>
                <td>{{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <th>Horario</th>
                <td>{{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Este es un comprobante generado automáticamente. Por favor llegue 10 minutos antes de su horario asignado.</p>
        <p>Emitido el {{ now()->format('d/m/Y H:i A') }}</p>
    </div>
</body>
</html>
