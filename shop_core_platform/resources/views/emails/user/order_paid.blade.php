<x-email-layout>
    {{-- resources/views/emails/order_summary.blade.php --}}
    @php
        $currency = 'GH₵';
        $subtotal = 0;
    @endphp
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Order Summary</title>
        <meta name="viewport" content="width=device-width,initial-scale=1">

    </head>
    <body style="margin:0; padding:0; background:#f4f6f8;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:24px;">
                <table role="presentation" width="600" class="container" cellpadding="0" cellspacing="0" style="background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.08);">
                    <!-- Header -->
                    <tr>
                        <td style="padding:24px; text-align:center; font:700 22px -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#111827;">
                            {{ config('app.name') }}
                        </td>
                    </tr>

                    <!-- Greeting -->
                    <tr>
                        <td style="padding:0 24px 16px; font:16px/1.5 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#374151;">
                            Hello {{ $notifiable->first_name }},
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 24px 24px; font:14px/1.6 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#374151;">
                            Thank you for placing an order. Here are the details:
                        </td>
                    </tr>

                    <!-- Items -->
                    <tr>
                        <td style="padding:0 24px 24px;">
                            @foreach ($orderItems as $item)
                                @php
                                    $name      = $item->product->name ?? 'Item';
                                    $qty       = (int) $item->quantity;
                                    $price     = (float) $item->price;
                                    $lineTotal = $price * $qty;
                                    $subtotal += $lineTotal;
                                    $imagePath = $item->product->photos[0] ?? null;
                                    $imageUrl  = $imagePath ? Storage::disk('public')->url($imagePath) : null;
                                @endphp

                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px; border:1px solid #e5e7eb; border-radius:6px;">
                                    <tr>
                                        @if($imageUrl)
                                            <td style="padding:12px; width:100px;">
                                                <img src="{{ $imageUrl }}" alt="{{ $name }}" width="80" style="border-radius:4px; display:block;">
                                            </td>
                                        @endif
                                        <td style="padding:12px; vertical-align:top;">
                                            <div style="font:600 14px -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#111827;">
                                                {{ $name }}
                                            </div>
                                            <div style="font:13px/1.4 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#4b5563;">
                                                Qty: {{ $qty }}
                                            </div>
                                            <div style="font:13px/1.4 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#4b5563;">
                                                Price: {{ $currency }} {{ number_format($price, 2) }}
                                            </div>
                                            <div style="font:600 13px/1.4 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#111827; margin-top:4px;">
                                                Line Total: {{ $currency }} {{ number_format($lineTotal, 2) }}
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            @endforeach
                        </td>
                    </tr>

                    <!-- Totals -->
                    <tr>
                        <td style="padding:0 24px 24px; text-align:right; font:600 14px -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#111827; border-top:1px solid #e5e7eb;">
                            Total: {{ $currency }} {{ number_format($subtotal, 2) }}
                        </td>
                    </tr>

                    <!-- Closing -->
                    <tr>
                        <td style="padding:0 24px 32px; font:14px/1.6 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#374151;">
                            We appreciate your business!
                            <br><br>Thanks,
                            <br>{{ config('app.name') }}
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:16px 24px; text-align:center; font:12px/1.6 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#9ca3af; background:#f9fafb;">
                            © {{ now()->year }} {{ config('app.name') }}. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    </body>
    </html>

</x-email-layout>
