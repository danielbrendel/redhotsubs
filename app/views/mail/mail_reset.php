<!doctype html>
<html lang="{{ getLocale() }}">
    <head>
        <meta charset="utf-8"/>

		<title>Your account on {{ env('APP_NAME') }}</title>
    </head>

    <body>
        <h1>Your account on {{ env('APP_NAME') }}</h1>

        <p>
            A password reset was requested for your account. If you requested this action, please follow the link below to reset your password.
            If you did not request this action, you can safely ignore this e-mail.
        </p>

        <p>
            <a href="{{ url('/user/reset?token=' . $token) }}">{{ url('/user/reset?token=' . $token) }}</a>
        </p>

        <p>
            <small>Powered by {{ env('APP_NAME') }}</small>
        </p>
    </body>
</html>