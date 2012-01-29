<?php

namespace RobMasters\Bundle\TaskBundle\Task;

use RobMasters\Bundle\TaskBundle\TaskEvents;
use RobMasters\Bundle\TaskBundle\Event\TaskEvent;
use RobMasters\Bundle\TaskBundle\Event\ExceptionEvent;
use RobMasters\Bundle\TaskBundle\Exception\TaskException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Contains the logic of executing a single task
 *
 * @author Rob Masters
 */
abstract class Task implements TaskInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Task identifier
     *
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    private $skip = false;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }


    public function skip()
    {
        $this->skip = true;
    }

    /**
     * @return bool
     */
    final public function process()
    {
        $event = new TaskEvent($this);
        $this->dispatch(TaskEvents::START, $event);

        if($this->shouldBeSkipped()) {
            // Skipped processes should not be treated as a failure as this could prematurely halt composites
            return true;
        }

        try {
            $processed = $this->doProcess();
            $eventName = ($processed) ? TaskEvents::COMPLETE : TaskEvents::FAIL;

            $postProcessedEvent = new TaskEvent($this);
            $this->dispatch($eventName, $postProcessedEvent);
        }
        catch(TaskException $e) {
            $event = new ExceptionEvent($this, $e);
            $this->dispatch(TaskEvents::ERROR, $event);

            // A problem with the task logic should halt all execution
            throw new $e;
        }
        catch(\Exception $e) {
            $event = new ExceptionEvent($this, $e);
            $this->dispatch(TaskEvents::ERROR, $event);

            $processed = false;
        }

        return $processed;
    }

    /**
     * Set an event dispatcher instance
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Dispatch an event, if the current task has an event dispatcher
     *
     * @param   string $eventType
     * @param   \Symfony\Component\EventDispatcher\Event $event
     * @return  \Symfony\Component\EventDispatcher\Event
     * @throws  \RobMasters\Bundle\TaskBundle\Exception\TaskException
     */
    protected function dispatch($eventType, \Symfony\Component\EventDispatcher\Event $event)
    {
        if(!is_null($this->dispatcher)) {
            switch($eventType) {
                case TaskEvents::START:
                case TaskEvents::COMPLETE:
                case TaskEvents::FAIL:
                case TaskEvents::ERROR:
                case TaskEvents::HALT:
                case TaskEvents::START_SUBTASK:
                    $this->dispatcher->dispatch($eventType, $event);
                    break;

                default:
                    throw new TaskException("Unrecognised task event type: $eventType");
            }
        }

        return $event;
    }

    /**
     * @return bool
     */
    protected function shouldBeSkipped()
    {
        return $this->skip;
    }

    /**
     * @abstract
     * @return bool
     */
    abstract protected function doProcess();
}