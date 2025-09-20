<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{

    public function update(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . auth()->id(),
            'phone'      => 'required|string|max:20|unique:users,phone,' . auth()->id(),
        ]);

        auth()->user()->update($request->only('first_name', 'last_name', 'email', 'phone'));

        return back()->with('success', 'Profile updated successfully.');
    }

    public function orders()
    {
        $orders = auth()->user()
            ->orders()
            ->select('id', 'created_at', 'status', 'total_price')
            ->latest()
            ->get();

        return view('user.orders', compact('orders'));
    }


    public function showOrder(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);


        $order->load(['items.product', 'items.variant.attributeValues.attribute', 'address']);

        return view('user.orders.show', compact('order'));
    }

}
