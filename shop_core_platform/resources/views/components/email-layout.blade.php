
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Order Summary</title>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <style>
            @media (prefers-color-scheme: dark) {
                body { background:#111 !important; }
                .card { background:#1a1a1a !important; color:#eaeaea !important; }
                .muted { color:#b5b5b5 !important; }
                a { color:#93c5fd !important; }
            }
            @media screen and (max-width:600px) {
                .container { width:100% !important; }
            }
        </style>
    </head>
    <body style="margin:0; padding:0; background:#f4f6f8;">
    {{ $slot }}
    </body>
    </html>

