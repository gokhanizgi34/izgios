<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    /**
     * Dashboard Ana Sayfası
     */
    public function index()
    {
        return view('dashboard.index');
    }
}