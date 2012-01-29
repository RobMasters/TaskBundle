<?php

namespace RobMasters\Bundle\TaskBundle\Listener;

use RobMasters\Bundle\TaskBundle\TaskEvents;
use RobMasters\Bundle\TaskBundle\Event\TaskEvent;
use RobMasters\Bundle\TaskBundle\Event\ExceptionEvent;
use RobMasters\Bundle\TaskBundle\Event\SubTaskEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 *
 * @author Rob Masters <rob@enginepowered.co.uk>
 */
abstract class BaseListener implements EventSubscriberInterface
{
    public function onStart(TaskEvent $event) {}

    public function onComplete(TaskEvent $event) {}

    public function onFail(TaskEvent $event) {}

    public function onError(ExceptionEvent $event) {}

    public function onStartSubTask(SubTaskEvent $event) {}

    public function onHalt(SubTaskEvent $event) {}
    
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    static function getSubscribedEvents()
    {
        return array(
            TaskEvents::START           => 'onStart',
            TaskEvents::COMPLETE        => 'onComplete',
            TaskEvents::FAIL            => 'onFail',
            TaskEvents::ERROR           => 'onError',
            TaskEvents::START_SUBTASK   => 'onStartSubTask',
            TaskEvents::HALT            => 'onHalt'
        );
    }

}
