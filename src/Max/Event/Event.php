<?php
declare(strict_types=1);

namespace Max\Event;

use Max\Facade\Config;

class Event
{

    protected $events = [
        'app_start'     => [],
        'response_sent' => [],
        'view_rendered' => []
    ];

    public function listen($trigger, $event)
    {
        if (!$this->has($trigger, $event)) {
            $this->events[$trigger][] = $event;
        } else {
            throw new \Exception('Event has already listened ' . $trigger . ' : ' . $event);
        }
    }

    public function has($trigger, $event)
    {
        return isset($this->events[$trigger][$event]);
    }

    public function get($trigger)
    {
        return $this->events[$trigger];
    }

    public function trigger($trigger)
    {
        $triggers = array_merge($this->get($trigger), Config::get('app.events.' . $trigger, []));
        foreach ($triggers as $event) {
            (new $event)->trigger();
        }
    }
}
