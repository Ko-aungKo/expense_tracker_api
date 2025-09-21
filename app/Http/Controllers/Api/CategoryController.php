<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(): JsonResponse
    {
        try {
            $categories = Category::withCount('expenses')
                ->orderBy('name')
                ->get();

            return response()->json($categories);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching categories',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
                'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'description' => 'nullable|string|max:500'
            ], [
                'name.required' => 'Category name is required',
                'name.unique' => 'Category name already exists',
                'color.required' => 'Color is required',
                'color.regex' => 'Color must be a valid hex color (e.g., #FF5733)'
            ]);

            $category = Category::create($validated);

            return response()->json([
                'message' => 'Category created successfully',
                'data' => $category->loadCount('expenses')
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating category',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category): JsonResponse
    {
        try {
            $category->loadCount('expenses');

            return response()->json([
                'data' => $category
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching category',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
                'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'description' => 'nullable|string|max:500'
            ], [
                'name.required' => 'Category name is required',
                'name.unique' => 'Category name already exists',
                'color.required' => 'Color is required',
                'color.regex' => 'Color must be a valid hex color (e.g., #FF5733)'
            ]);

            $category->update($validated);

            return response()->json([
                'message' => 'Category updated successfully',
                'data' => $category->loadCount('expenses')
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating category',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category): JsonResponse
    {
        try {
            // Check if category has expenses
            if ($category->expenses()->count() > 0) {
                return response()->json([
                    'message' => 'Cannot delete category that has expenses. Please delete or move the expenses first.'
                ], 400);
            }

            $category->delete();

            return response()->json([
                'message' => 'Category deleted successfully'
            ], 204);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting category',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
