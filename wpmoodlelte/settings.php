<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Allows course enrolment via a simple text code.
 *
 * @package   enrol_wpmoodlelte
 * @copyright 2017 Dearborn Public Schools
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading('enrol_wpmoodlelte_settings', '', get_string('pluginname_desc', 'enrol_wpmoodlelte')));

    //--- enrol instance defaults ----------------------------------------------------------------------------
//    $settings->add(new admin_setting_heading('enrol_manual_defaults',
//        get_string('enrolinstancedefaults', 'admin'), get_string('enrolinstancedefaults_desc', 'admin')));

    $settings->add(
        new admin_setting_configcheckbox(
            'enrol_wpmoodlelte/defaultenrol',
            get_string('defaultenrol', 'enrol_wpmoodlelte'),
            get_string('defaultenrol_desc', 'enrol_wpmoodlelte'),
            1)
    );
    $settings->add(new admin_setting_description(
        'enrol_wpmoodlelte/runcron',
        "Actions",
        '<form method="POST" id="wpmoodlelteActions"> <input type="button" id="run_cron" value="Run wpmoodlelte Cron Job" class="btn btn-primary btn-sm" /><br/><br/> <input type="button" id="activate_wpmoodlelte_plugin" value="Activate wpmoodlelte Plugin" class="btn btn-primary btn-sm" /><br/><br/></form><script>$("#run_cron").click(function (){ $.ajax({url:"/enrol/wpmoodlelte/run_cron.php", type: "GET", success: function(response){console.log("Response...",response); alert("Success: "+response); }, error: function(error) { alert("Error: "+ error);} }); }); $("#activate_wpmoodlelte_plugin").click(function (){$.ajax({url:"/enrol/wpmoodlelte/activate.php", type: "GET", success: function(response){console.log("Response...",response); alert("wpmoodlelte plugin activated on all courses");}, error: function(error) { alert("Error: "+ error);} }); });</script>'));

    /*$settings->add(new admin_setting_configcheckbox('enrol_wpmoodlelte/showqronmobile',
        get_string('showqronmobile', 'enrol_wpmoodlelte'), get_string('showqronmobiledesc', 'enrol_wpmoodlelte'), 0));*/

}

?>


