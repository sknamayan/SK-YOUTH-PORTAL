<?php

namespace App\Http\Controllers;

use App\Models\HealthRequest;
use App\Models\MedicineRequest;
use App\Models\SilidKarununganRequest;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    /**
     * Display the public landing page with categories.
     */
    public function index()
    {
        $categories = [
            'EDUCATION',
            'HEALTH',
            'GOVERNANCE',
            'ACTIVE CITIZENSHIP',
            'SOCIAL INCLUSION',
            'PEACE BUILDING',
            'ENVIRONMENT',
            'YOUTH EMPLOYMENT & EMPOWERMENT',
            'AGRICULTURE',
            'GLOBAL MOBILITY'
        ];

        return view('welcome', compact('categories'));
    }


}
