<?php

/**
 * Description of Events
 *
 * @author macha
 */
class Events {

    private static $_instance = null;
    protected $events = array();

    private function __construct() {
        
    }

    public static function getInstance() {

        if (is_null(self::$_instance)) {
            self::$_instance = new static();
        }

        return self::$_instance;
    }

    public static function listen($event_name, $fn) {
        $instance = self::getInstance();
        if (!isset($instance->events[$event_name])) {
            $instance->events[$event_name] = array();
        }
        array_push($instance->events[$event_name], $fn);
    }

    public function fire($name, array $arguments) {
        $instance = self::getInstance();
        if (isset($instance->events[$name])) {
            
            foreach ($instance->events[$name] as $fn) {
                call_user_func_array($fn, $arguments);
            }
        }
    }

}
