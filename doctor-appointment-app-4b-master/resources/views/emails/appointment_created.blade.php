<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <div style="background-color: #4F46E5; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="margin: 0; font-size: 24px;">Confirmación de Cita Médica</h1>
    </div>
    
    <div style="background-color: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px;">
        <p>Hola <strong>{{ $appointment->patient->user->name ?? 'Paciente' }}</strong>,</p>
        
        <p>¡Tu cita ha sido agendada con éxito en <strong>MediMatch</strong>!</p>
        
        <div style="background-color: white; padding: 15px; border-radius: 6px; border-left: 4px solid #4F46E5; margin: 20px 0;">
            <p style="margin: 0 0 10px 0;">📅 <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}</p>
            <p style="margin: 0 0 10px 0;">⏰ <strong>Hora:</strong> {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}</p>
            <p style="margin: 0 0 10px 0;">👨‍⚕️ <strong>Médico:</strong> Dr(a). {{ $appointment->doctor->user->name ?? 'N/A' }}</p>
            <p style="margin: 0;">📝 <strong>Motivo:</strong> {{ $appointment->reason ?? 'No especificado' }}</p>
        </div>
        
        <p>Adjunto a este correo encontrarás el <strong>comprobante oficial en PDF</strong> con los detalles de tu consulta.</p>
        
        <p style="color: #6b7280; font-size: 14px; margin-top: 30px;">
            Gracias por confiar en nosotros.<br>
            El equipo de MediMatch.
        </p>
    </div>
</body>
</html>
