<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerAddressRequest;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addresses = auth()->user()->customerAddresses;

        return view('user.addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.addresses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerAddressRequest $request)
    {
        $request->merge([
            'is_default' => $request->boolean('is_default', false),
        ]);
        $data = $request->validated();

        if (!empty($data['is_default'])) {
            auth()->user()->customerAddresses()->update(['is_default' => false]);
        }

        auth()->user()->customerAddresses()->create($data);



        return redirect()->route('addresses.index')->with('success', 'Address saved.');

    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerAddress $customerAddress)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerAddress $address)
    {
        return view('user.addresses.create',compact('address') );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerAddressRequest $request, CustomerAddress $address)
    {

        $request->merge([
            'is_default' => $request->boolean('is_default', false),
        ]);
        $data = $request->validated();

        // If the updated address is being set as default
        if (!empty($data['is_default'])) {
            // Unset all other defaults for the same user
            $address->user
                ->customerAddresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($data);

        return redirect()->route('addresses.index')->with('success', 'Address updated.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerAddress $customerAddress)
    {
        //
    }
}
