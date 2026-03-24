<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <div style="background-color: #0f172a; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="margin: 0; font-size: 24px;">Reporte de Citas del Día</h1>
        <p style="margin: 5px 0 0 0; color: #94a3b8;">{{ now()->format('d/m/Y') }}</p>
    </div>
    
    <div style="background-color: #f8fafc; padding: 20px; border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 8px 8px;">
        
        @if($recipientType === 'admin')
            <p>Hola Administrador, a continuación se detallan <strong>todas</strong> las citas programadas para el día de hoy.</p>
        @else
            <p>Hola Doctor(a), este es su itinerario de citas para el día de hoy.</p>
        @endif

        @if($appointments->isEmpty())
            <div style="background-color: #f1f5f9; padding: 20px; text-align: center; border-radius: 6px; margin: 20px 0;">
                <p style="margin: 0; color: #64748b;">No hay citas registradas para hoy.</p>
            </div>
        @else
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr style="background-color: #e2e8f0;">
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #cbd5e1;">Hora</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #cbd5e1;">Paciente</th>
                        @if($recipientType === 'admin')
                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #cbd5e1;">Doctor</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $appointment)
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">{{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}</td>
                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">{{ $appointment->patient->user->name ?? 'N/A' }}</td>
                        @if($recipientType === 'admin')
                        <td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">Dr. {{ $appointment->doctor->user->name ?? 'N/A' }}</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        
        <p style="color: #64748b; font-size: 12px; margin-top: 30px; text-align: center;">
            Sistema de Gestión Médica MediMatch.
        </p>
    </div>
</body>
</html>
