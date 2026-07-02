<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Subject::with('department')->withCount(['questions', 'questions as active_questions_count' => function ($q) {
            $q->where('is_active', true);
        }]);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $subjects = $query->orderBy('name')->get();

        return response()->json(['data' => $subjects]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:subjects,name'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $subject = Subject::create($validated);

        return response()->json($subject->load('department'), 201);
    }

    public function update(Request $request, Subject $subject): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:subjects,name,' . $subject->id],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $subject->update($validated);

        return response()->json($subject->fresh()->load('department'));
    }

    public function destroy(Subject $subject): JsonResponse
    {
        if ($subject->questions()->exists()) {
            return response()->json([
                'message' => 'Cannot delete subject that has questions assigned. Reassign or remove the questions first.',
            ], 422);
        }

        $subject->delete();

        return response()->json(['message' => 'Subject deleted successfully.']);
    }

    public function toggleActive(Subject $subject): JsonResponse
    {
        $subject->update(['is_active' => !$subject->is_active]);

        return response()->json($subject->fresh()->load('department'));
    }
}
