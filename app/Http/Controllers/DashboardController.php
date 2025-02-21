<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('2fa');
    }

    public function index(Request $request)
    {
        if (Auth::user()->can('manage dashboard')) {
            $subcategories   = SubCategory::count();
            $categories      = Category::count();
            $open_ticket     = Ticket::whereIn('status', ['On Hold', 'In Progress'])->count();
            $close_ticket    = Ticket::where('status', '=', 'Closed')->count();
            $agents          = User::where('parent', \Auth::user()->createId())->count();

            $categoriesChart = Ticket::select(
                [
                    'categories.name',
                    'categories.color',
                    \DB::raw('count(*) as total'),
                ]
            )->join('categories', 'categories.id', '=', 'tickets.category')->groupBy('categories.id')->get();

            $chartData = ['color' => [], 'name' => [], 'value' => []];
            foreach ($categoriesChart as $category) {
                $chartData['name'][]  = $category->name;
                $chartData['value'][] = $category->total;
                $chartData['color'][] = $category->color;
            }

            $monthData = [];
            $barChart  = Ticket::select(
                [
                    \DB::raw('MONTH(created_at) as month'),
                    \DB::raw('YEAR(created_at) as year'),
                    \DB::raw('count(*) as total'),
                ]
            )->where('created_at', '>', \DB::raw('DATE_SUB(NOW(),INTERVAL 1 YEAR)'))->groupBy(
                [
                    \DB::raw('MONTH(created_at)'),
                    \DB::raw('YEAR(created_at)'),
                ]
            )->get();

            $start = \Carbon\Carbon::now()->startOfYear();
            for ($i = 0; $i <= 11; $i++) {
                $monthData[$start->format('M')] = 0;
                foreach ($barChart as $chart) {
                    if (intval($chart->month) == intval($start->format('m'))) {
                        $monthData[$start->format('M')] = $chart->total;
                    }
                }
                $start->addMonth();
            }

            $statusChart = Ticket::select('status', \DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get();

            $statusData = [
                'name' => [],
                'value' => []
            ];
            foreach ($statusChart as $status) {
                $statusData['name'][] = $status->status;
                $statusData['value'][] = $status->total;
            }

            $priorityChart = Ticket::select([
                'priorities.name as priority_name',
                'priorities.color as priority_color',
                \DB::raw('count(*) as total')
            ])

                ->join('priorities', 'priorities.id', '=', 'tickets.priority')
                ->groupBy('priorities.id')
                ->get();
            $priorityData = [
                'name' => [],
                'color' => [],
                'value' => [],
            ];

            foreach ($priorityChart as $priority) {
                $priorityData['name'][] = $priority->priority_name;
                $priorityData['value'][] = $priority->total;
                $priorityData['color'][] = $priority->priority_color;
            }

            return view('admin.dashboard.index', compact('categories', 'subcategories', 'open_ticket', 'close_ticket', 'agents', 'chartData', 'monthData', 'statusData', 'priorityData'));
        }else{
            return redirect()->back()->with('error','Permission Denied.');
        }
    }
}
