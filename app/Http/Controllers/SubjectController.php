<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Subject::active()
            ->withCount(['questions as active_questions_count' => function ($q) {
                $q->where('is_active', true);
            }]);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $subjects = $query
            ->orderBy('name')
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'description' => $s->description,
                'active_questions_count' => $s->active_questions_count,
            ]);

        return response()->json(['data' => $subjects]);
    }
}
