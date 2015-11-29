<?php

/**
 * Description of Events
 *
 * @author macha
 */
class Events {

    /**
     * instance for singleton
     *
     * @var Events
     */
    private static $_instance = null;

    /**
     * list of registered events
     *
     * @var array
     */
    protected $events = array();

    private function __construct() {
        
    }

    public static function getInstance() {

        if (is_null(self::$_instance)) {
            self::$_instance = new static();
        }

        return self::$_instance;
    }

    /**
     * register callback of event
     *
     * @param string $event_name
     * @param Closure $callback
     */
    public static function listen($event_name, Closure $callback) {
        $instance = self::getInstance();
        if (!isset($instance->events[$event_name])) {
            $instance->events[$event_name] = array();
        }
        array_push($instance->events[$event_name], $callback);
    }

    /**
     * fire registered callback for event name
     *
     * @param string $event_name
     * @param array $arguments
     */
    public function fire($event_name, array $arguments) {
        $instance = self::getInstance();
        if (isset($instance->events[$event_name])) {

            foreach ($instance->events[$event_name] as $fn) {
                call_user_func_array($fn, $arguments);
            }
        }
    }

}
