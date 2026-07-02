<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = SchoolClass::active();

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $classes = $query->orderBy('name')->get(['id', 'name', 'department_id', 'year_of_study', 'generation']);

        return response()->json(['data' => $classes]);
    }
}
