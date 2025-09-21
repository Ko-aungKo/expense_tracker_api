<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses with filtering and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Expense::with('category:id,name,color');

            // Apply filters
            if ($request->filled('start_date')) {
                $query->whereDate('expense_date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('expense_date', '<=', $request->end_date);
            }

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'expense_date');
            $sortOrder = $request->get('sort_order', 'desc');

            // Validate sort fields
            $allowedSortFields = ['expense_date', 'amount', 'title', 'created_at'];
            if (!in_array($sortBy, $allowedSortFields)) {
                $sortBy = 'expense_date';
            }

            $query->orderBy($sortBy, $sortOrder);

            // Paginate results
            $perPage = min($request->get('per_page', 15), 100); // Max 100 items per page
            $expenses = $query->paginate($perPage);

            return response()->json($expenses);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching expenses',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a newly created expense.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'amount' => 'required|numeric|min:0.01|max:99999999.99',
                'expense_date' => 'required|date|before_or_equal:today',
                'category_id' => 'required|exists:categories,id'
            ], [
                'title.required' => 'Expense title is required',
                'amount.min' => 'Amount must be greater than 0',
                'amount.max' => 'Amount cannot exceed 99,999,999.99',
                'expense_date.before_or_equal' => 'Expense date cannot be in the future',
                'category_id.exists' => 'Selected category does not exist'
            ]);

            $expense = Expense::create($validated);
            $expense->load('category:id,name,color');

            return response()->json([
                'message' => 'Expense created successfully',
                'data' => $expense
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating expense',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense): JsonResponse
    {
        try {
            $expense->load('category:id,name,color');

            return response()->json([
                'data' => $expense
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching expense',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update the specified expense.
     */
    public function update(Request $request, Expense $expense): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'amount' => 'required|numeric|min:0.01|max:99999999.99',
                'expense_date' => 'required|date|before_or_equal:today',
                'category_id' => 'required|exists:categories,id'
            ], [
                'title.required' => 'Expense title is required',
                'amount.min' => 'Amount must be greater than 0',
                'amount.max' => 'Amount cannot exceed 99,999,999.99',
                'expense_date.before_or_equal' => 'Expense date cannot be in the future',
                'category_id.exists' => 'Selected category does not exist'
            ]);

            $expense->update($validated);
            $expense->load('category:id,name,color');

            return response()->json([
                'message' => 'Expense updated successfully',
                'data' => $expense
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating expense',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified expense.
     */
    public function destroy(Expense $expense): JsonResponse
    {
        try {
            $expense->delete();

            return response()->json([
                'message' => 'Expense deleted successfully'
            ], 204);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting expense',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
