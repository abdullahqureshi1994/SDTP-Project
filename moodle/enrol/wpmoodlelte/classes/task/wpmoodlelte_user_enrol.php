<?php
namespace enrol_wpmoodlelte\task;

class wpmoodlelte_user_enrol extends \core\task\scheduled_task {

    public function get_name() {
        // Shown on admin screens
        return 'wpmoodlelte User Enrol';// get_string('wpmoodlelte_user_enrol', 'wpmoodlelte_enrol'); //get the string from lang/en/
    }

    public function execute() {
        global $CFG;
        require_once($CFG->dirroot . '/enrol/wpmoodlelte/lib.php');
        enrol_users_via_wpmoodlelte(); //function to execute
    }
}
?>