<?php
defined('MOODLE_INTERNAL') || die();
$tasks =
        array(
            array(
                'classname' => 'enrol_wpmoodlelte\task\wpmoodlelte_user_enrol',
                 'blocking' => 0,
                 'minute' => '*/15', //run after every 15 mins
                 'hour' => '*',
                 'day' => '*',
                 'dayofweek' => '*',
                 'month' => '*'
                )
        );
?>