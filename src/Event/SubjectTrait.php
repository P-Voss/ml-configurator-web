<?php

namespace App\Event;

use App\Entity\Event;

trait SubjectTrait
{

    /**
     * @var Event[]
     */
    protected array $events = [];

    /**
     * @var \SplObserver[]
     */
    protected array $observers = [];


    public function attach(\SplObserver $observer): void
    {
        $this->observers[spl_object_hash($observer)] = $observer;
    }

    public function detach(\SplObserver $observer): void
    {
        if ($this->observers[spl_object_hash($observer)]) {
            unset($this->observers[spl_object_hash($observer)]);
        }
    }

    public function addEvent(Event $event): static
    {
        $this->events[] = $event;
        return $this;
    }

    public function getEvents(): array
    {
        return $this->events;
    }

}