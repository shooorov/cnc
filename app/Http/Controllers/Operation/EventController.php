<?php

namespace App\Http\Controllers\Operation;

use App\Http\Cache\CacheEvent;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\RolePermission;
use App\UseBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class EventController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Show the events list page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $isDateSearch = RolePermission::isEnabled('record_search.event_date_search');

        if ($isDateSearch) {
            $end_date = $request->end_date ? now()->parse($request->end_date) : now()->endOfMonth();
            $start_date = $request->start_date ? now()->parse($request->start_date) : now()->startOfMonth();
        } else {
            $end_date = now()->endOfMonth();
            $start_date = now()->startOfMonth();
        }

        $events = CacheEvent::get();

        $params = [
            'events' => $events,
            'filter' => [
                'end_date' => $end_date->format('Y-m-d'),
                'start_date' => $start_date->format('Y-m-d'),
            ],
        ];

        return Inertia::render('Operation/Event/Index', $params);
    }

    /**
     * Show the events Calendar page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function calendar(Request $request)
    {
        if ($request->year && $request->month && checkdate($request->month, '01', $request->year)) {
            $date_generate = now()->parse($request->year.'-'.$request->month);
        } else {
            $date_generate = now()->format('Y-m');
        }

        $month_first_day = now()->parse($date_generate)->startOfMonth()->format('w');
        $date = now()->parse(date('Y-m-d', strtotime($date_generate)))->subDay($month_first_day)->format('Y-m-d');

        $days = [];
        $selectedDay = '';
        for ($i = 0; $i < 42; $i++) {
            $events = CacheEvent::get()->where('date', $date);
            $days[] = [
                'date' => now()->parse($date)->format('d'),
                'events' => $events,
                'isToday' => now()->format('Ymd') == now()->parse($date)->format('Ymd'),
                'isCurrentMonth' => now()->parse($date_generate)->format('Ym') == now()->parse($date)->format('Ym'),
            ];
            $date = now()->parse($date)->addDay()->format('Y-m-d');
        }
        $today_events = CacheEvent::get()->where('date', now());
        $selectedDay = [
            'date' => now()->format('d'),
            'events' => $today_events,
        ];

        $params = [
            'days' => $days,
            'selectedDay' => $selectedDay,
            'today' => [
                'year' => now()->format('Y'),
                'month' => now()->format('m'),
            ],
            'calendar' => [
                'year' => now()->parse($date_generate)->format('Y'),
                'month' => now()->parse($date_generate)->format('m'),
                'month_year' => now()->parse($date_generate)->format('F Y'),
            ],
        ];

        return Inertia::render('Operation/Event/Calendar', $params);
    }

    /**
     * Show the event create.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create()
    {
        $params = [];

        return Inertia::render('Operation/Event/Create', $params);
    }

    /**
     * Create new resource in storage.
     *
     * @param  Event  $event
     * @return Response
     */
    public function store(Request $request)
    {
        if (! UseBranch::id()) {
            return back()->with('fail', 'Please specify branch first.');
        }

        $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date'],
            'name' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        DB::beginTransaction();

        $event = new Event;
        $event->date = now()->parse($request->date);
        $event->start_time = now()->parse($request->start_time);
        $event->end_time = now()->parse($request->end_time);
        $event->name = $request->name;
        $event->description = $request->description;
        $event->branch_id = UseBranch::id();
        $event->save();

        DB::commit();
        CacheEvent::forget();

        return redirect()->route('event.index')->with('success', 'Event created successfully.');
    }

    /**
     * Show the event detail page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show(Event $event)
    {
        $params = [
            'event' => $event,
        ];

        return Inertia::render('Operation/Event/Show', $params);
    }

    /**
     * Show the event edit page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit(Event $event)
    {
        $params = [
            'event' => $event,
        ];

        return Inertia::render('Operation/Event/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date'],
            'name' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        DB::beginTransaction();
        $event->date = now()->parse($request->date);
        $event->start_time = now()->parse($request->start_time);
        $event->end_time = now()->parse($request->end_time);
        $event->name = $request->name;
        $event->description = $request->description;
        $event->update();

        DB::commit();
        CacheEvent::forget();

        return back()->with('success', 'Event updated successfully.');
    }

    /**
     * Delete the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Event $event)
    {
        DB::beginTransaction();
        $event->delete();

        DB::commit();
        CacheEvent::forget();

        return redirect()->route('event.index')->with('success', __('Event removed successfully!'));
    }
}
