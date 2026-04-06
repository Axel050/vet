<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Restablecer Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            color: #374151;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #111827;
        }

        .content {
            line-height: 1.6;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .button {
            background-color: #4f46e5;
            color: #ffffff;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            display: inline-block;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }

        .trouble-link {
            word-break: break-all;
            color: #4f46e5;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <!-- Puedes agregar tu logo aquí -->
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="content">
            <p>Hola, <strong>{{ $user->name ?? 'Usuario' }}</strong>:</p>
            <p>Recibiste este correo porque solicitaste restablecer la contraseña de tu cuenta.</p>

            <div class="button-container">
                <a href="{{ $url }}" class="button" style="color: #ffffff;">Restablecer Contraseña</a>
            </div>

            <p>Este enlace para restablecer tu contraseña expirará en
                {{ config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') }} minutos.</p>
            <p>Si no realizaste esta solicitud, no requieres realizar ninguna otra acción.</p>
        </div>
        <div class="footer">
            <p>Saludos,<br>{{ config('app.name') }}</p>
            <div style="margin-top: 30px; font-size: 12px; color: #9ca3af; text-align: left;">
                <p>Si tienes problemas haciendo clic en el botón "Restablecer Contraseña", copia y pega el siguiente
                    enlace en tu navegador web:</p>
                <p><a href="{{ $url }}" class="trouble-link">{{ $url }}</a></p>
            </div>
        </div>
    </div>
</body>

</html>
