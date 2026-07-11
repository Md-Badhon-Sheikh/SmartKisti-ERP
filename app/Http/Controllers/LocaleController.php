<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        if (in_array($request->input('locale'), config('app.supported_locales'), true)) {
            session(['locale' => $request->input('locale')]);
        }

        return redirect()->back();
    }
}
