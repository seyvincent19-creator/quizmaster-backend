<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(): JsonResponse
    {
        $departments = Department::withCount(['classes', 'subjects'])->orderBy('name')->get();

        return response()->json(['data' => $departments]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:departments,name'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $department = Department::create($validated);

        return response()->json($department, 201);
    }

    public function update(Request $request, Department $department): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:departments,name,' . $department->id],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $department->update($validated);

        return response()->json($department->fresh());
    }

    public function destroy(Department $department): JsonResponse
    {
        if ($department->classes()->exists()) {
            return response()->json([
                'message' => 'Cannot delete department that has classes assigned. Reassign or remove the classes first.',
            ], 422);
        }

        if ($department->subjects()->exists()) {
            return response()->json([
                'message' => 'Cannot delete department that has subjects assigned. Reassign or remove the subjects first.',
            ], 422);
        }

        $department->delete();

        return response()->json(['message' => 'Department deleted successfully.']);
    }

    public function toggleActive(Department $department): JsonResponse
    {
        $department->update(['is_active' => !$department->is_active]);

        return response()->json($department->fresh());
    }
}
