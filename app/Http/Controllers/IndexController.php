<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Certificate;
use App\Models\PsqcCertification;
use App\Models\WebTest;

class IndexController extends Controller
{
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function pdf($code)
    {
        $certificate = Certificate::where('code', $code)->first();
        if (!$certificate) {
            return redirect()->route('home');
        }
        if($certificate->payment_status != 'paid'){
            return redirect()->route('home');
        }
        $test_type   = $certificate->test_type;
        $currentTest = WebTest::find($certificate->web_test_id);

        return view('pdf', compact('certificate', 'currentTest', 'test_type'));
    }

    public function psqc_pdf($code)
    {
        $certification = PsqcCertification::where('code', $code)->first();
        if (!$certification) {
            return redirect()->route('home');
        }
        if($certification->payment_status != 'paid'){
            return redirect()->route('home');
        }
        return view('psqc_pdf', compact('certification'));
    }
}