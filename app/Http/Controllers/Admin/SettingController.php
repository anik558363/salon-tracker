<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'salon_name'       => 'required|string|max:255',
            'salon_city'       => 'nullable|string|max:100',
            'salon_phone'      => 'nullable|string|max:50',
            'salon_email'      => 'nullable|email|max:100',
            'salon_address'    => 'nullable|string|max:500',
            'currency'         => 'nullable|string|max:10',
            'currency_symbol'  => 'nullable|string|max:10',
            'hours_sat_thu'    => 'nullable|string|max:100',
            'hours_friday'     => 'nullable|string|max:100',
            'walkin_policy'    => 'nullable|string|max:100',
            'google_maps_url'  => 'nullable|string|max:1000',
            'hero_badge'       => 'nullable|string|max:255',
            'hero_title'       => 'nullable|string|max:255',
            'hero_subtitle'    => 'nullable|string|max:1000',
            'footer_text'      => 'nullable|string|max:255',
            'logo'             => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'favicon'          => 'nullable|image|mimes:jpeg,png,ico,webp|max:1024',
        ]);

        // ১. সব টেক্সট ফিল্ড রিকোয়েস্ট থেকে নিয়ে সরাসরি সেভ করা
        $excludedKeys = ['_token', 'logo', 'favicon'];
        foreach ($request->except($excludedKeys) as $key => $value) {
            Setting::set($key, $value);
        }

        // ২. মেইন লোগো ফাইল আপলোড
        if ($request->hasFile('logo')) {
            $oldLogo = Setting::get('logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $logoPath = $request->file('logo')->store('settings', 'public');
            Setting::set('logo', $logoPath);
        }

        // ৩. ফ্যাভিকন ফাইল আপলোড
        if ($request->hasFile('favicon')) {
            $oldFavicon = Setting::get('favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }
            $faviconPath = $request->file('favicon')->store('settings', 'public');
            Setting::set('favicon', $faviconPath);
        }

        // ৪. ক্যাশ পরিষ্কার করা
        Cache::forget('app_settings');

        return redirect()->back()->with('success', 'Salon settings saved successfully!');
    }
}
