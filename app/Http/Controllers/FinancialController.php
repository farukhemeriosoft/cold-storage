<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialController extends Controller
{
    public function getReports(Request $request)
    {
        $fromDate = $request->input('from', Carbon::now()->subMonths(5)->startOfMonth());
        $toDate = $request->input('to', Carbon::now()->endOfMonth());
        $customerId = $request->input('customer_id');
        $paymentStatus = $request->input('payment_status');
        $minAmount = $request->input('min_amount');
        $maxAmount = $request->input('max_amount');
        $reportTypes = $request->input('report_types');

        // Convert to Carbon instances if they're strings
        if (is_string($fromDate)) {
            $fromDate = Carbon::parse($fromDate)->startOfDay();
        }
        if (is_string($toDate)) {
            $toDate = Carbon::parse($toDate)->endOfDay();
        }

        // Parse comma-separated values
        $paymentStatusArray = $paymentStatus ? explode(',', $paymentStatus) : [];
        $reportTypesArray = $reportTypes ? explode(',', $reportTypes) : [];

        // Get basic stats
        $stats = $this->getBasicStats($fromDate, $toDate, $customerId, $minAmount, $maxAmount);

        // Get revenue data
        $revenueData = $this->getRevenueData($fromDate, $toDate, $customerId);

        // Get outstanding payments
        $outstandingPayments = $this->getOutstandingPayments($customerId, $paymentStatusArray, $minAmount, $maxAmount);

        // Get profit data
        $profitData = $this->getProfitData($fromDate, $toDate, $customerId);

        // Get top customers
        $topCustomers = $this->getTopCustomers($fromDate, $toDate, $customerId);

        // Get customer analytics
        $customerAnalytics = $this->getCustomerAnalytics($customerId);

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'revenueData' => $revenueData,
                'outstandingPayments' => $outstandingPayments,
                'profitData' => $profitData,
                'topCustomers' => $topCustomers,
                'customerAnalytics' => $customerAnalytics
            ]
        ]);
    }

    private function getBasicStats($fromDate, $toDate, $customerId = null, $minAmount = null, $maxAmount = null)
    {
        // Total revenue in date range
        $revenueQuery = DB::table('invoices')
            ->whereBetween('created_at', [$fromDate, $toDate]);

        if ($customerId) {
            $revenueQuery->where('customer_id', $customerId);
        }
        if ($minAmount) {
            $revenueQuery->where('total_amount', '>=', $minAmount);
        }
        if ($maxAmount) {
            $revenueQuery->where('total_amount', '<=', $maxAmount);
        }

        $totalRevenue = $revenueQuery->sum('total_amount');

        // Outstanding payments
        $outstandingQuery = DB::table('invoices')
            ->where('balance_due', '>', 0);

        if ($customerId) {
            $outstandingQuery->where('customer_id', $customerId);
        }
        if ($minAmount) {
            $outstandingQuery->where('balance_due', '>=', $minAmount);
        }
        if ($maxAmount) {
            $outstandingQuery->where('balance_due', '<=', $maxAmount);
        }

        $outstandingPayments = $outstandingQuery->sum('balance_due');

        // Calculate actual profit margin: (Revenue - Outstanding) / Revenue * 100
        $netProfit = $totalRevenue - $outstandingPayments;
        $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        // Calculate growth rate (compare with previous period)
        $previousPeriodStart = $fromDate->copy()->subMonth();
        $previousPeriodEnd = $toDate->copy()->subMonth();

        $previousRevenue = DB::table('invoices')
            ->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
            ->sum('total_amount');

        $growthRate = $previousRevenue > 0
            ? (($totalRevenue - $previousRevenue) / $previousRevenue) * 100
            : 0;

        return [
            'totalRevenue' => $totalRevenue,
            'outstandingPayments' => $outstandingPayments,
            'profitMargin' => round($profitMargin, 1),
            'growthRate' => round($growthRate, 1)
        ];
    }

    private function getRevenueData($fromDate, $toDate, $customerId = null)
    {
        // Get monthly revenue data for the last 6 months
        $revenueData = [];
        $currentDate = $fromDate->copy()->startOfMonth();
        $endDate = $toDate->copy()->endOfMonth();

        while ($currentDate->lte($endDate)) {
            $monthStart = $currentDate->copy()->startOfMonth();
            $monthEnd = $currentDate->copy()->endOfMonth();

            $revenueQuery = DB::table('invoices')
                ->whereBetween('created_at', [$monthStart, $monthEnd]);

            if ($customerId) {
                $revenueQuery->where('customer_id', $customerId);
            }

            $revenue = $revenueQuery->sum('total_amount');

            $invoiceCount = DB::table('invoices')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            // Calculate growth (compare with previous month)
            $previousMonthStart = $monthStart->copy()->subMonth();
            $previousMonthEnd = $monthEnd->copy()->subMonth();

            $previousRevenue = DB::table('invoices')
                ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
                ->sum('total_amount');

            // Calculate growth percentage
            if ($previousRevenue > 0) {
                // Normal case: compare with previous month
                $growth = (($revenue - $previousRevenue) / $previousRevenue) * 100;
            } elseif ($revenue > 0) {
                // New revenue where there was none before = 100% growth
                $growth = 100;
            } else {
                // No revenue in either month = 0% growth
                $growth = 0;
            }

            $revenueData[] = [
                'period' => $currentDate->format('F Y'),
                'revenue' => $revenue,
                'growth' => round($growth, 1),
                'invoices' => $invoiceCount
            ];

            $currentDate->addMonth();
        }

        return $revenueData;
    }

    private function getOutstandingPayments($customerId = null, $paymentStatus = [], $minAmount = null, $maxAmount = null)
    {
        $query = DB::table('invoices')
            ->join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->where('invoices.balance_due', '>', 0);

        if ($customerId) {
            $query->where('invoices.customer_id', $customerId);
        }

        if ($minAmount) {
            $query->where('invoices.balance_due', '>=', $minAmount);
        }

        if ($maxAmount) {
            $query->where('invoices.balance_due', '<=', $maxAmount);
        }

        return $query->select(
                'invoices.id',
                'customers.full_name as customer_name',
                'invoices.invoice_number',
                'invoices.balance_due as amount',
                'invoices.due_date',
                DB::raw('DATEDIFF(NOW(), invoices.due_date) as days_overdue')
            )
            ->orderBy('invoices.due_date', 'asc')
            ->get()
            ->map(function ($payment) {
                // Determine status based on balance_due and days_overdue
                if ($payment->amount <= 0) {
                    $status = 'Paid';
                } else {
                    // Invoice has outstanding balance
                    if ($payment->days_overdue > 30) {
                        $status = 'Overdue';
                    } elseif ($payment->days_overdue > 7) {
                        $status = 'Late';
                    } elseif ($payment->days_overdue > 0) {
                        $status = 'Due Soon';
                    } else {
                        $status = 'Outstanding';
                    }
                }

                return [
                    'id' => $payment->id,
                    'customer_name' => $payment->customer_name,
                    'invoice_number' => $payment->invoice_number,
                    'amount' => $payment->amount,
                    'due_date' => $payment->due_date,
                    'days_overdue' => max(0, $payment->days_overdue),
                    'status' => $status
                ];
            });
    }

    private function getProfitData($fromDate, $toDate, $customerId = null)
    {
        $revenueQuery = DB::table('invoices')
            ->whereBetween('created_at', [$fromDate, $toDate]);

        if ($customerId) {
            $revenueQuery->where('customer_id', $customerId);
        }

        $grossRevenue = $revenueQuery->sum('total_amount');

        // Calculate actual profit: Revenue minus outstanding payments
        // Outstanding payments represent unpaid invoices, so they shouldn't count as profit
        $outstandingQuery = DB::table('invoices')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->where('balance_due', '>', 0);

        if ($customerId) {
            $outstandingQuery->where('customer_id', $customerId);
        }

        $outstandingAmount = $outstandingQuery->sum('balance_due');

        // Net profit = Total revenue - Outstanding payments (unpaid invoices)
        $netProfit = $grossRevenue - $outstandingAmount;
        $profitMargin = $grossRevenue > 0 ? ($netProfit / $grossRevenue) * 100 : 0;

        return [
            'grossRevenue' => $grossRevenue,
            'netProfit' => $netProfit,
            'profitMargin' => round($profitMargin, 1)
        ];
    }

    private function getTopCustomers($fromDate, $toDate, $customerId = null)
    {
        $query = DB::table('invoices')
            ->join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->whereBetween('invoices.created_at', [$fromDate, $toDate]);

        if ($customerId) {
            $query->where('invoices.customer_id', $customerId);
        }

        return $query->select(
                'customers.id',
                'customers.full_name as name',
                DB::raw('SUM(invoices.total_amount) as revenue'),
                DB::raw('COUNT(invoices.id) as invoices')
            )
            ->groupBy('customers.id', 'customers.full_name')
            ->orderBy('revenue', 'desc')
            ->limit(5)
            ->get();
    }

    private function getCustomerAnalytics($customerId = null)
    {
        // Get total invoices
        $totalQuery = DB::table('invoices');
        if ($customerId) {
            $totalQuery->where('customer_id', $customerId);
        }
        $totalInvoices = $totalQuery->count();

        if ($totalInvoices === 0) {
            return [
                'onTimePayments' => 0,
                'latePayments' => 0,
                'overduePayments' => 0
            ];
        }

        // Calculate payment behavior based on current status
        $onTimeCount = DB::table('invoices')
            ->where('status', 'paid')
            ->where('paid_date', '<=', DB::raw('due_date'))
            ->when($customerId, function($query) use ($customerId) {
                return $query->where('customer_id', $customerId);
            })
            ->count();

        $lateCount = DB::table('invoices')
            ->where('status', 'paid')
            ->where('paid_date', '>', DB::raw('due_date'))
            ->whereRaw('DATEDIFF(paid_date, due_date) <= 7')
            ->when($customerId, function($query) use ($customerId) {
                return $query->where('customer_id', $customerId);
            })
            ->count();

        $overdueCount = DB::table('invoices')
            ->where('status', 'overdue')
            ->when($customerId, function($query) use ($customerId) {
                return $query->where('customer_id', $customerId);
            })
            ->count();

        // If no specific status-based data, use balance-based calculation
        if ($onTimeCount + $lateCount + $overdueCount === 0) {
            $paidInvoices = DB::table('invoices')
                ->where('balance_due', '<=', 0)
                ->when($customerId, function($query) use ($customerId) {
                    return $query->where('customer_id', $customerId);
                })
                ->count();

            $unpaidInvoices = $totalInvoices - $paidInvoices;

            return [
                'onTimePayments' => round(($paidInvoices / $totalInvoices) * 100, 1),
                'latePayments' => 0,
                'overduePayments' => round(($unpaidInvoices / $totalInvoices) * 100, 1)
            ];
        }

        return [
            'onTimePayments' => round(($onTimeCount / $totalInvoices) * 100, 1),
            'latePayments' => round(($lateCount / $totalInvoices) * 100, 1),
            'overduePayments' => round(($overdueCount / $totalInvoices) * 100, 1)
        ];
    }

    public function exportReport(Request $request)
    {
        $reportType = $request->input('type', 'revenue');
        $fromDate = $request->input('from', Carbon::now()->startOfMonth());
        $toDate = $request->input('to', Carbon::now()->endOfMonth());

        // Convert to Carbon instances if they're strings
        if (is_string($fromDate)) {
            $fromDate = Carbon::parse($fromDate)->startOfDay();
        }
        if (is_string($toDate)) {
            $toDate = Carbon::parse($toDate)->endOfDay();
        }

        switch ($reportType) {
            case 'revenue':
                $data = $this->getRevenueData($fromDate, $toDate);
                break;
            case 'outstanding':
                $data = $this->getOutstandingPayments();
                break;
            case 'customers':
                $data = $this->getTopCustomers($fromDate, $toDate);
                break;
            default:
                $data = [];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'exported_at' => Carbon::now()->toISOString()
        ]);
    }
}
