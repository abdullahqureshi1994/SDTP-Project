<?php

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_login();

$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/enrol/wpmoodlelte/activate.php'));

$plugin = 'wpmoodlelte';
$manualplugin = enrol_get_plugin($plugin);
$courses = $DB->get_recordset_select('course', 'category > 0', null, '', 'id');


foreach ($courses as $course) {
    $instanceid = null;
    $instances = enrol_get_instances($course->id, true);

    // Check if there wpmoodlelte instance exists for a course or not
    foreach ($instances as $inst) {
        if ($inst->enrol == $plugin) {
            $instanceid = (int)$inst->id;
            break;
        }
    }
    //If no wpmoodlelte instance found for a course then add its
    if (empty($instanceid)) {
        $instanceid = $manualplugin->add_default_instance($course);
        if (empty($instanceid)) {
            $instanceid = $manualplugin->add_instance($course);
        }
    }
}
