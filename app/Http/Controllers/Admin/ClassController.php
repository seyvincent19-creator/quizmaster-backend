<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = SchoolClass::with('department')->withCount('users');

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $classes = $query->orderBy('name')->get();

        return response()->json(['data' => $classes]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('classes')->where('department_id', $request->department_id),
            ],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'year_of_study' => ['nullable', 'string', 'max:50'],
            'generation' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ]);

        $class = SchoolClass::create($validated);

        return response()->json($class->load('department'), 201);
    }

    public function update(Request $request, SchoolClass $schoolClass): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('classes')->where('department_id', $request->department_id)->ignore($schoolClass->id),
            ],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'year_of_study' => ['nullable', 'string', 'max:50'],
            'generation' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ]);

        $schoolClass->update($validated);

        return response()->json($schoolClass->fresh()->load('department'));
    }

    public function destroy(SchoolClass $schoolClass): JsonResponse
    {
        if ($schoolClass->users()->exists()) {
            return response()->json([
                'message' => 'Cannot delete class that has students assigned. Reassign or remove the students first.',
            ], 422);
        }

        $schoolClass->delete();

        return response()->json(['message' => 'Class deleted successfully.']);
    }

    public function toggleActive(SchoolClass $schoolClass): JsonResponse
    {
        $schoolClass->update(['is_active' => !$schoolClass->is_active]);

        return response()->json($schoolClass->fresh()->load('department'));
    }
}
