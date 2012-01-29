<?php

namespace RobMasters\Bundle\TaskBundle;

class TaskEvents
{
    /**
     * The START event occurs every time a task begins processing
     *
     * This event contains an accessor method to get the task that has just started,
     * which allows you to call skip() on it if it shouldn't be processed.
     *
     * @var string
     */
    const START = 'task.start';

    /**
     * The COMPLETE event occurs after the body of a task has successfully finished processing.
     *
     * The main use of this event is to
     *
     * @var string
     */
    const COMPLETE = 'task.complete';
    const FAIL = 'task.fail';
    const ERROR = 'task.error';
    const HALT = 'task.composite.halt';
    const START_SUBTASK = 'task.sub.start';
}