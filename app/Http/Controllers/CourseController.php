<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\CourseRepository;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected CourseRepository $courseRepo;

    public function __construct(CourseRepository $courseRepo)
    {
        $this->courseRepo = $courseRepo;
    }

    public function index(Request $request)
    {
        $courses = $this->courseRepo->paginate(10);
        return response()->json($courses);
    }

    public function show($id)
    {
        $course = $this->courseRepo->find($id);

        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        return response()->json($course);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'category_id' => 'required|exists:categories,id',
            'instructor_id' => 'required|exists:users,id',
            'is_published' => 'boolean',
        ]);

        $course = $this->courseRepo->create($validated);
        return response()->json($course, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'level' => 'sometimes|in:beginner,intermediate,advanced',
            'category_id' => 'sometimes|exists:categories,id',
            'instructor_id' => 'sometimes|exists:users,id',
            'is_published' => 'boolean',
        ]);

        $course = $this->courseRepo->update($id, $validated);

        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        return response()->json($course);
    }

    public function destroy($id)
    {
        $deleted = $this->courseRepo->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Course not found or already deleted'], 404);
        }

        return response()->json(['message' => 'Course deleted successfully']);
    }
}
