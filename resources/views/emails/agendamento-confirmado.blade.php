<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Confirmacao de Agendamento</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f7f7f7; margin: 0; padding: 24px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 10px; border: 1px solid #e5e7eb;">
        <tr>
            <td style="padding: 24px;">
                <h2 style="margin: 0 0 12px; color: #111827;">Agendamento confirmado ✅</h2>
                <p style="margin: 0 0 16px; color: #374151;">
                    Ola, {{ $agendamento->cliente->nome }}! Seu horario foi agendado com sucesso na JC Barber.
                </p>

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280; width: 180px;">Servico</td>
                        <td style="padding: 8px 0; color: #111827;"><strong>{{ $agendamento->servico->nome }}</strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280;">Data e horario</td>
                        <td style="padding: 8px 0; color: #111827;"><strong>{{ \Carbon\Carbon::parse($agendamento->data_hora)->format('d/m/Y \a\s H:i') }}</strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280;">Telefone</td>
                        <td style="padding: 8px 0; color: #111827;">{{ $agendamento->cliente->telefone }}</td>
                    </tr>
                </table>

                <p style="margin: 20px 0 0; color: #374151;">
                    Em caso de imprevistos, entre em contato para remarcar.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>

