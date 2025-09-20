@php
    $currency = 'GH₵';
    $subtotal = 0;
@endphp

<x-email-layout>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f4f6f8;">
        <tr>
            <td align="center" style="padding:24px;">
                <table role="presentation" width="600" class="container" cellspacing="0" cellpadding="0" border="0"
                       style="width:600px; max-width:100%;">
                    <!-- Header -->


                    <!-- Card -->
                    <tr>
                        <td class="px" style="padding:0 24px 24px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                   class="card"
                                   style="background:#ffffff; border-radius:12px; box-shadow:0 1px 3px rgba(16,24,40,.06);">

                                <tr>
                                    <td style="padding:24px; text-align:center;">
                                        <a href="{{ config('app.url') }}" style="text-decoration:none; color:inherit;">
                <span
                    style="font:700 22px/1.2 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif;">
                  {{ config('app.name') }}
                </span>
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:28px 24px 8px; font:700 20px/1.2 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#111827;">
                                        Hello {{ $notifiable->first_name }},
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 24px 16px; font:400 14px/1.6 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#374151;">
                                        You have received a new order. Review the details below and take action from
                                        your dashboard:
                                    </td>
                                </tr>

                                <!-- Items (card per item, with photo) -->
                                <tr>
                                    <td style="padding:0 24px 8px;">
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

                                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                                   border="0"
                                                   style="margin:0 0 16px 0; border:1px solid #e5e7eb; border-radius:8px; background:#fafafa;">
                                                <tr>
                                                    <td style="padding:12px; vertical-align:top; width:120px;">
                                                        @if($imageUrl)
                                                            <img src="{{ $imageUrl }}" alt="{{ $name }} photo"
                                                                 width="100" style="border-radius:6px; display:block;">
                                                        @endif
                                                    </td>
                                                    <td style="padding:12px; vertical-align:top;">
                                                        <div
                                                            style="font:600 14px/1.4 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#111827; margin-bottom:4px;">
                                                            {{ $name }}
                                                        </div>
                                                        <div
                                                            style="font:400 13px/1.6 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#4b5563;">
                                                            Qty: {{ $qty }}
                                                        </div>
                                                        <div
                                                            style="font:400 13px/1.6 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#4b5563;">
                                                            Price: {{ $currency }} {{ number_format($price, 2) }}
                                                        </div>
                                                        <div
                                                            style="font:600 13px/1.6 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#111827; margin-top:6px;">
                                                            Total: {{ $currency }} {{ number_format($lineTotal, 2) }}
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        @endforeach
                                    </td>
                                </tr>

                                <!-- Subtotal -->
                                <tr>
                                    <td style="padding:0 24px 16px; text-align:right; font:600 14px/1.4 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#111827; border-top:1px solid #e5e7eb;"
                                        class="divider">
                                        Total: {{ $currency }} {{ number_format($subtotal, 2) }}
                                    </td>
                                </tr>

                                <!-- CTA Button -->
                                <tr>
                                    <td align="center" style="padding:16px 24px 24px;">
                                        <a href="{{ $ordersUrl }}"
                                           style="display:inline-block; background:#2563eb; color:#ffffff; text-decoration:none; font:600 14px/1 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; padding:14px 20px; border-radius:8px;"
                                           class="btn">
                                            View Recent Orders
                                        </a>
                                    </td>
                                </tr>

                                <!-- URL Fallback -->
                                <tr>
                                    <td style="padding:0 24px 28px; font:400 12px/1.6 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#6b7280;"
                                        class="muted">
                                        If the button doesn’t work, copy and paste this URL into your browser:<br>
                                        <a href="{{ $ordersUrl }}"
                                           style="color:#2563eb; text-decoration:underline;">{{ $ordersUrl }}</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Signature -->
                    <tr>
                        <td style="padding:8px 24px 40px; text-align:left; font:400 13px/1.6 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#374151;">
                            Thanks,<br>{{ config('app.name') }}
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:0 24px 40px; text-align:center; font:400 11px/1.6 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#9ca3af;">
                            © {{ now()->year }} {{ config('app.name') }}. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>


</x-email-layout>
