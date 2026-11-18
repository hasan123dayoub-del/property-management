<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $months = collect(range(5, 0))
            ->map(fn ($i) => Carbon::now()->subMonths($i)->format('Y-m'))
            ->values();
        $rawBooking = Booking::select(
            DB::raw($this->yearMonth('scheduled_at').' as month'),
            // CASE instead of SUM(status = ...): PostgreSQL cannot add up booleans
            DB::raw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending"),
            DB::raw("SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved"),
            DB::raw("SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected")
        )
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->get()
            ->keyBy('month');
        $statusStats = $months->map(function ($m) use ($rawBooking) {
            $row = $rawBooking->get($m);

            return [
                'month_key' => $m,
                'month' => Carbon::createFromFormat('Y-m', $m)->format('M'),
                'pending' => (int) ($row->pending ?? 0),
                'approved' => (int) ($row->approved ?? 0),
                'rejected' => (int) ($row->rejected ?? 0),
            ];
        })->values();

        $rawProps = Property::select(
            DB::raw($this->yearMonth('created_at').' as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $propertiesPerMonth = $months->map(function ($m) use ($rawProps) {
            $r = $rawProps->get($m);

            return [
                'month_key' => $m,
                'month' => Carbon::createFromFormat('Y-m', $m)->format('M'),
                'total' => (int) ($r->total ?? 0),
            ];
        })->values();
        $totalProperties = Property::count();
        $totalBookings = Booking::count();

        return view('dashboard.index', compact(
            'months',
            'statusStats',
            'propertiesPerMonth',
            'totalProperties',
            'totalBookings'
        ));
    }

    // "YYYY-MM" of a date column, on MySQL, SQLite and PostgreSQL (used by the demo deployment).
    private function yearMonth(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', {$column})",
            'pgsql' => "to_char({$column}, 'YYYY-MM')",
            default => "DATE_FORMAT({$column}, '%Y-%m')",
        };
    }
}
