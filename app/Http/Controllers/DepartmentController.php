<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    public function index(): JsonResponse
    {
        $departments = Department::active()
            ->orderBy('name')
            ->get(['id', 'name', 'description']);

        return response()->json(['data' => $departments]);
    }
}
