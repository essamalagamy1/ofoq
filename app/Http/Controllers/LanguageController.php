<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function __invoke($lang): \Illuminate\Http\RedirectResponse
    {
        $lang = in_array($lang, ['en', 'ar']) ? $lang : config('app.locale', 'en');
        Session::put('lang', $lang);

        return back();
    }
}
