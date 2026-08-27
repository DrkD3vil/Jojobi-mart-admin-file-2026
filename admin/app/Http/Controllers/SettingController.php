<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $settings = Setting::cached();

        return view('settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'store_name'          => 'nullable|string|max:150',
            'store_address'       => 'nullable|string|max:255',
            'store_phone'         => 'nullable|string|max:40',
            'store_email'         => 'nullable|email|max:150',
            'currency_symbol'     => 'nullable|string|max:5',
            'invoice_footer_note' => 'nullable|string|max:500',
        ]);

        Setting::setMany($data);

        return redirect()->route('settings.edit')->with('success', 'Settings updated.');
    }
}
