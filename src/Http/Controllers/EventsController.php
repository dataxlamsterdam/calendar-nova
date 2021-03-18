<?php

namespace Dataxl\NovaCalendarTool\Http\Controllers;

use Dataxl\NovaCalendarTool\Models\Event;
use Illuminate\Http\Request;

class EventsController
{
    public function index(Request $request)
    {
        $events = Event::filter($request->query())
	        ->leftJoin('event_user', 'event_user.event_id', '=', 'events.id')
	        ->where('event_user.user_id', auth()->user()->id)
            ->get(['id', 'title', 'start', 'end'])
            ->toJson();

        return response($events);
    }

    public function store(Request $request)
    {
        $validation = Event::getModel()->validate($request->input(), 'create');

        if ($validation->passes())
        {
            $event = Event::create($request->input());

            if ($event)
            {
                return response()->json([
                    'success' => true,
                    'event' => $event
                ]);
            }
        }

        return response()->json([
            'error' => true,
            'message' => $validation->errors()->first()
        ]);
    }

    public function update(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        $validation = Event::getModel()->validate($request->input(), 'update');

        if ($validation->passes())
        {
            $event->update($request->input());

            return response()->json([
                'success' => true,
                'event' => $event
            ]);
        }

        return response()->json([
            'error' => true,
            'message' => $validation->errors()->first()
        ]);
    }

    public function destroy(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        if ( ! is_null($event))
        {
            $event->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['error' => true]);
    }
}