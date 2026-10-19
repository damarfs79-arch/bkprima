<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class RuleController extends Controller
{
    public function index()
    {
        return Inertia::render('RulePoints');
    }
}
