<!doctype html>
<html lang="{{ getLocale() }}">
    <head>
        <meta charset="utf-8"/>

		<title>Your account on {{ env('APP_NAME') }}</title>
    </head>

    <body>
        <h1>Your account on {{ env('APP_NAME') }}</h1>

        <p>
            Your account was successfully created. Please confirm your account by clicking the link below.
        </p>

        <p>
            <a href="{{ url('/user/confirm?token=' . $token) }}">{{ url('/user/confirm?token=' . $token) }}</a>
        </p>

        <p>
            <small>Powered by {{ env('APP_NAME') }}</small>
        </p>
    </body>
</html>