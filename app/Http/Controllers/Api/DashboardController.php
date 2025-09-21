<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard data with summaries and charts.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Validate date inputs
            $request->validate([
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date'
            ]);

            $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

            // Ensure valid date range
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();

            // Total expenses for the period
            $totalExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
                ->sum('amount');

            // Total count of expenses
            $totalCount = Expense::whereBetween('expense_date', [$startDate, $endDate])
                ->count();

            // Category breakdown with totals
            $categoryBreakdown = Expense::select('category_id')
                ->selectRaw('SUM(amount) as total_amount')
                ->selectRaw('COUNT(*) as count')
                ->with('category:id,name,color')
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->groupBy('category_id')
                ->orderBy('total_amount', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'category' => $item->category,
                        'total_amount' => (float) $item->total_amount,
                        'count' => $item->count,
                        'percentage' => 0 // Will be calculated on frontend
                    ];
                });

            // Recent expenses (last 10)
            $recentExpenses = Expense::with('category:id,name,color')
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->orderBy('expense_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Daily expenses for chart (last 30 days from end date)
            $chartStartDate = Carbon::parse($endDate)->subDays(29)->startOfDay();
            $chartEndDate = Carbon::parse($endDate)->endOfDay();

            $dailyExpenses = Expense::select(DB::raw('DATE(expense_date) as date'))
                ->selectRaw('SUM(amount) as amount')
                ->whereBetween('expense_date', [$chartStartDate, $chartEndDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function ($item) {
                    return [
                        'date' => $item->date,
                        'amount' => (float) $item->amount
                    ];
                });

            // Monthly comparison (current vs previous month)
            $currentMonth = Carbon::now()->startOfMonth();
            $previousMonth = Carbon::now()->subMonth()->startOfMonth();
            $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

            $currentMonthTotal = Expense::whereBetween('expense_date', [$currentMonth, Carbon::now()])
                ->sum('amount');

            $previousMonthTotal = Expense::whereBetween('expense_date', [$previousMonth, $previousMonthEnd])
                ->sum('amount');

            $monthlyGrowth = $previousMonthTotal > 0
                ? (($currentMonthTotal - $previousMonthTotal) / $previousMonthTotal) * 100
                : 0;

            // Top spending categories (all time)
            $topCategories = Category::select('categories.*')
                ->selectRaw('SUM(expenses.amount) as total_spent')
                ->selectRaw('COUNT(expenses.id) as expense_count')
                ->join('expenses', 'categories.id', '=', 'expenses.category_id')
                ->groupBy('categories.id', 'categories.name', 'categories.color', 'categories.description', 'categories.created_at', 'categories.updated_at')
                ->orderBy('total_spent', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'color' => $category->color,
                        'total_spent' => (float) $category->total_spent,
                        'expense_count' => $category->expense_count
                    ];
                });

            return response()->json([
                'summary' => [
                    'total_expenses' => (float) $totalExpenses,
                    'total_count' => $totalCount,
                    'average_per_day' => $totalCount > 0 ? (float) ($totalExpenses / max(1, $startDate->diffInDays($endDate) + 1)) : 0,
                    'monthly_growth' => round($monthlyGrowth, 2)
                ],
                'category_breakdown' => $categoryBreakdown,
                'recent_expenses' => $recentExpenses,
                'daily_expenses' => $dailyExpenses,
                'top_categories' => $topCategories,
                'date_range' => [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString()
                ],
                'period_info' => [
                    'days_in_period' => $startDate->diffInDays($endDate) + 1,
                    'current_month_total' => (float) $currentMonthTotal,
                    'previous_month_total' => (float) $previousMonthTotal
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching dashboard data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get monthly statistics for a specific year.
     */
    public function getMonthlyStats(Request $request, int $year): JsonResponse
    {
        try {
            $monthlyStats = collect(range(1, 12))->map(function ($month) use ($year) {
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth();

                $total = Expense::whereBetween('expense_date', [$startDate, $endDate])
                    ->sum('amount');

                $count = Expense::whereBetween('expense_date', [$startDate, $endDate])
                    ->count();

                return [
                    'month' => $month,
                    'month_name' => $startDate->format('F'),
                    'total_amount' => (float) $total,
                    'expense_count' => $count,
                    'average_per_day' => $count > 0 ? (float) ($total / $startDate->daysInMonth) : 0
                ];
            });

            return response()->json([
                'year' => $year,
                'monthly_stats' => $monthlyStats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching monthly statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
