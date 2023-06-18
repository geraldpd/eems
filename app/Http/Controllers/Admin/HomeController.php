<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Type;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $topOrganizers = User::withCount('organizedEvents')
            ->with('organization')
            ->orderBy('organized_events_count', 'desc')
            ->take(3)
            ->get();

        $topAttendees = User::withCount('attendedEvents')
            ->orderBy('attended_events_count', 'desc')
            ->take(3)
            ->get();

        $eventChart = $this->getEventsData();

        $categoryPie = $this->getCategoriesData();

        $typePie = $this->getTypeData();

        return view('admin.index', compact('topOrganizers', 'topAttendees', 'eventChart', 'categoryPie', 'typePie'));
    }

    private function getEventsData()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();

        for ($i = 0; $i < 12; $i++) {
            $startOfMonth = $currentMonth->copy()->subMonths($i);
            $endOfMonth = $endMonth->copy()->subMonths($i);
            $monthsRange[] = $startOfMonth->format('M');
            $eventsData[] = Event::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        }

        return [
            'label' => array_reverse($monthsRange),
            'data' => array_reverse($eventsData)
        ];
    }

    private function getCategoriesData()
    {
        $categories = Category::whereIsActive(true)->get();

        $data = [];
        foreach ($categories as $category) {
            $data[] = [
                'label' => $category->name,
                'color' => '#' . dechex(mt_rand(0x000000, 0xFFFFFF)),
                'data' => Event::whereTypeId($category->id)->count()
            ];
        }

        return $data;
    }
    private function getTypeData()
    {
        $types = Type::whereIsActive(true)->get();

        $data = [];
        foreach ($types as $type) {
            $data[] = [
                'label' => $type->name,
                'color' => '#' . dechex(mt_rand(0x000000, 0xFFFFFF)),
                'data' => Event::whereTypeId($type->id)->count()
            ];
        }

        return $data;
    }
}
