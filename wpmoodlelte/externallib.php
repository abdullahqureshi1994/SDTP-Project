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
 * Self enrol plugin external functions
 *
 * @package    enrol_self
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once(dirname(__FILE__) . '/locallib.php');

/**
 * Self enrolment external functions.
 *
 * @package   enrol_self
 * @copyright 2012 Rajesh Taneja <rajesh@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.6
 */
class enrol_wpmoodlelte_external extends external_api {

    /**
     * Returns description of get_instance_info() parameters.
     *
     * @return external_function_parameters
     */
    public static function get_instance_info_parameters() {
        return new external_function_parameters(
                array('instanceid' => new external_value(PARAM_INT, 'instance id of self enrolment plugin.'))
            );
    }

    /**
     * Return self-enrolment instance information.
     *
     * @param int $instanceid instance id of self enrolment plugin.
     * @return array instance information.
     * @throws moodle_exception
     */
    public static function get_instance_info($instanceid) {
        global $DB, $CFG;

        require_once($CFG->libdir . '/enrollib.php');

        $params = self::validate_parameters(self::get_instance_info_parameters(), array('instanceid' => $instanceid));

        // Retrieve self enrolment plugin.
        $enrolplugin = enrol_get_plugin('self');
        if (empty($enrolplugin)) {
            throw new moodle_exception('invaliddata', 'error');
        }

        self::validate_context(context_system::instance());

        $enrolinstance = $DB->get_record('enrol', array('id' => $params['instanceid']), '*', MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $enrolinstance->courseid), '*', MUST_EXIST);
        if (!core_course_category::can_view_course_info($course) && !can_access_course($course)) {
            throw new moodle_exception('coursehidden');
        }

        $instanceinfo = (array) $enrolplugin->get_enrol_info($enrolinstance);
        if (isset($instanceinfo['requiredparam']->enrolpassword)) {
            $instanceinfo['enrolpassword'] = $instanceinfo['requiredparam']->enrolpassword;
        }
        unset($instanceinfo->requiredparam);

        return $instanceinfo;
    }

    /**
     * Returns description of get_instance_info() result value.
     *
     * @return external_description
     */
    public static function get_instance_info_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'id of course enrolment instance'),
                'courseid' => new external_value(PARAM_INT, 'id of course'),
                'type' => new external_value(PARAM_PLUGIN, 'type of enrolment plugin'),
                'name' => new external_value(PARAM_RAW, 'name of enrolment plugin'),
                'status' => new external_value(PARAM_RAW, 'status of enrolment plugin'),
                'enrolpassword' => new external_value(PARAM_RAW, 'password required for enrolment', VALUE_OPTIONAL),
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function enrol_user_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Id of the course'),
                'instanceid' => new external_value(PARAM_INT, 'Instance id of self enrolment plugin.', VALUE_DEFAULT, 0),
                'name' => new external_value(PARAM_RAW, 'User Full Name'),
                'email' => new external_value(PARAM_RAW, 'Email'),
                'mobile' => new external_value(PARAM_RAW, 'Mobile', VALUE_DEFAULT, NULL),
            )
        );
    }

    /**
     * Self enrol the current user in the given course.
     *
     * @param int $courseid id of course
     * @param string $password enrolment key
     * @param int $instanceid instance id of self enrolment plugin
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function enrol_user($courseid, $instanceid = 0, $name, $email, $mobile) {
        global $CFG, $DB;
        
        require_once($CFG->libdir . '/enrollib.php');

        $params = self::validate_parameters(self::enrol_user_parameters(),
                                            array(
                                                'courseid' => $courseid,
                                                'instanceid' => $instanceid,
                                                'name' => rawurldecode($name),
                                                'email' => $email,
                                                'mobile' => $mobile
                                            ));
        
        $warnings = array();

        $course = get_course($params['courseid']);
        $context = context_course::instance($course->id);
        self::validate_context(context_system::instance());

        if (!core_course_category::can_view_course_info($course)) {
            throw new moodle_exception('coursehidden');
        }

        // Retrieve the vouchcer enrolment plugin.
        $enrol = enrol_get_plugin('wpmoodlelte');
        if (empty($enrol)) {
            throw new moodle_exception('canntenrol', 'enrol_self');
        }

        // We can expect multiple wpmoodlelte-enrolment instances.
        $instances = array();
        $enrolinstances = enrol_get_instances($course->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "wpmoodlelte") {
                // Instance specified.
                if (!empty($params['instanceid'])) {
                    if ($courseenrolinstance->id == $params['instanceid']) {
                        $instances[] = $courseenrolinstance;
                        break;
                    }
                } else {
                    $instances[] = $courseenrolinstance;
                }

            }
        }
        // dd($instances, $course->id );

        if (empty($instances)) {
            throw new moodle_exception('canntenrol', 'enrol_self');
        }

        // Try to enrol the user in the instance/s.
        $status = false;
        $data = [];
        foreach ($instances as $instance) {
            $enrolstatus = $enrol->can_self_enrol($instance);
            if ($enrolstatus === true) {

                // Call API to create a new wpmoodlelte
                // make the user_id nullable
                // if user tries to create a new wpmoodlelte show the already created if it is still valid
                // when payment status is paid then create a new user and send an email with credentials and enrol user as well.

                $enrol_class = new enrol_wpmoodlelte_plugin();
                $plugin_instance = $DB->get_record("enrol", array('id' => $params['instanceid'], 'enrol' => 'wpmoodlelte'));
                $response = $enrol_class->create_new_wpmoodlelte($course->id, $name = $params['name'], $email = $params['email'], $amount = $plugin_instance->cost, $mobile = $params['mobile']);

                if($response["code"] == 200){
                    $resp = $enrol_class->insertwpmoodlelte($response['data'], $course->id, null, $params['instanceid']);
                    $status = true;
                    $data = json_decode(json_encode($resp["data"]), true);
                }
                else{
                    $status = false;
                    error_log('LUMSx-LMS Create wpmoodlelte Error (via Endpoint).....'.(json_encode($response)));
                    //return redirect($enrol_url, 'Unexpected System Error!!!', null, \core\output\notification::NOTIFY_ERROR);
                }

                // Do the enrolment.
                // $data = array('enrolpassword' => $params['password']);
                // $enrol->enrol_self($instance, (object) $data);
                // $enrolled = true;
                // break;
            } else {
                $warnings[] = array(
                    'item' => 'instance',
                    'itemid' => $instance->id,
                    'warningcode' => '1',
                    'message' => $enrolstatus
                );
            }
        }

        $result = array();
        $result['status'] = $status;
        $result['warnings'] = $warnings;
        $result['data'] = $data;
        // dd($result);
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function enrol_user_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if the user is enrolled, false otherwise'),
                'warnings' => new external_warnings(),
                'data' => new external_single_structure(
                    array(
                        'course_id' => new external_value(PARAM_INT, 'course Id', VALUE_OPTIONAL),
                        'user_id' => new external_value(PARAM_INT, 'LMS User Id', VALUE_OPTIONAL),
                        'enrol_id' => new external_value(PARAM_INT, 'Enrol Id', VALUE_OPTIONAL),
                        'challan_id' => new external_value(PARAM_TEXT, 'Challan ID'),
                        'paid_status' => new external_value(PARAM_TEXT, 'Payment Status'),
                        'name' => new external_value(PARAM_TEXT, 'User\'s Name'),
                        'email' => new external_value(PARAM_TEXT, 'User\'s Email'),
                        'mobile' => new external_value(PARAM_TEXT, 'User\'s Mobile'),
                        'order_id' => new external_value(PARAM_TEXT, 'Order Id'),
                        'name' => new external_value(PARAM_TEXT, 'User Name'),
                        'challan_id' => new external_value(PARAM_TEXT, 'Challan ID'),
                        'due_date_formatted' => new external_value(PARAM_TEXT, 'Due Date'),
                        'total_amount_formatted' => new external_value(PARAM_TEXT, 'Total Amount'),
                        'url_for_online_payment' => new external_value(PARAM_TEXT, 'Online Payment'),
                        'url_for_download_wpmoodlelte' => new external_value(PARAM_TEXT, 'Download wpmoodlelte'),
                    ), 'data')
                
            )
        );
    }
}
