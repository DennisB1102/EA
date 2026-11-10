<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $qrPassword = Setting::get('nfr_qr_password', '');
        return view('admin.settings', compact('qrPassword'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nfr_qr_password' => 'required|string|min:4|max:100',
        ], [
            'nfr_qr_password.required' => 'Bitte ein Passwort eingeben.',
            'nfr_qr_password.min'      => 'Das Passwort muss mindestens 4 Zeichen lang sein.',
        ]);

        Setting::set('nfr_qr_password', $request->input('nfr_qr_password'));

        return back()->with('success', 'Einstellungen gespeichert.');
    }
}
