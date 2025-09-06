<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    public function index()
    {
        return response()->view('sitemap.index')
            ->header('Content-Type', 'application/xml');
    }
}