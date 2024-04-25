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
 * @package   enrol_easy
 * @copyright 2017 Dearborn Public Schools
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->dirroot.'/user/lib.php');

global $PAGE;
$PAGE->requires->js('/enrol/wpmoodlelte/script.js');


function enrol_users_via_wpmoodlelte ()
{
    global $DB, $CFG;

    try{
        $check_total_count = "SELECT count(id) as total FROM {enrol_wpmoodlelte} where paid_status = 'Not Paid' and deleted_at is NULL";
        $total_records = $DB->get_field_sql($check_total_count);
        
        $limit = 50;

        for($i=0; $i < ceil($total_records / $limit ); $i++){
            //Start Db transaction
            $transaction = $DB->start_delegated_transaction();
            try{
                $sql = "SELECT GROUP_CONCAT(c_id SEPARATOR ',') AS challan_ids from (SELECT challan_id as c_id FROM {enrol_wpmoodlelte} where paid_status = 'Not Paid' and deleted_at is NULL LIMIT ".($limit*$i).", $limit) as c_tab";
                $wpmoodlelte_ids = $DB->get_field_sql($sql);

                $url = $CFG->wpmoodlelte_api_url.'/wpmoodleltes?ids='.$wpmoodlelte_ids;
                $headers = array('Content-Type:application/json','Authorization: Bearer '.$CFG->wpmoodlelte_access_token);
                $response = http_request($url, 'GET', null, $headers );
                $response = json_decode($response);
                $paid_challan_ids = [];
                if($response->code == 200) {
                    if(is_array($response->data)) {
                        foreach ($response->data as $row) {
                            if ($row->paid_status == 'Paid') {
                                array_push($paid_challan_ids, $row->challan_no);
                                $paid_wpmoodlelte = $DB->get_record("enrol_wpmoodlelte",
                                    array('challan_id' => $row->challan_no, 'paid_status' => 'Not Paid', 'deleted_at' => NULL )
                                );
                                if($paid_wpmoodlelte->user_id != "0"){
                                    $DB->execute("Update {enrol_wpmoodlelte} set paid_status='" . $row->paid_status . "', paid_date = '".$row->paid_date."', paid_date_formatted='".$row->paid_date_formatted."', paid_interface = '".$row->paid_interface."' where challan_id=" . ($row->challan_no));
                                    $coursecontext = \context_course::instance($paid_wpmoodlelte->course_id);
                                    
                                    if(!is_enrolled($coursecontext, $paid_wpmoodlelte->user_id)){
                                        $plugin_instance = $DB->get_record("enrol", array('id' => $paid_wpmoodlelte->enrol_id, 'enrol' => 'wpmoodlelte'));
                                        $plugin = enrol_get_plugin('wpmoodlelte');
                                        $plugin->enrol_user($plugin_instance, $paid_wpmoodlelte->user_id, 5);
                                        $user = $DB->get_record("user", array('id' => $paid_wpmoodlelte->user_id ));
                                        $plugin->email_welcome_message($plugin_instance, $user );
                                    }
                                }
                                else{
                                    // Create User Account, if email doesn't exist in the user table
                                    //  if username already exists
                                    // Enroll User in Course
                                    $fullName = explode(" ",$paid_wpmoodlelte->name);
                                    $firstname = $fullName[0];
                                    $lastname = (!empty($fullName[1])) ? $fullName[1] : '';
                                    $username = explode('@',$paid_wpmoodlelte->email)[0];
                                    $usernew = [
                                        "course" => 1,
                                        "username" => $username,
                                        "auth" => "manual",
                                        "suspended" => "0",
                                        "preference_auth_forcepasswordchange" => 0,
                                        "firstname" => $firstname,
                                        "lastname" => $lastname,
                                        "email" => $paid_wpmoodlelte->email,
                                        "maildisplay" => "2",
                                        "moodlenetprofile" => "",
                                        "city" => "",
                                        "country" => "",
                                        "timezone" => "99",
                                        "lang" => "en",
                                        "description_editor" => [
                                            "text" => "",
                                            "format" => "1"
                                        ],
                                        "mform_isexpanded_id_moodle_picture" => 1,
                                        "imagefile" => 771895340,
                                        "imagealt" => "",
                                        "firstnamephonetic" => "",
                                        "lastnamephonetic" => "",
                                        "middlename" => "",
                                        "alternatename" => "",
                                        "interests" => [],
                                        "idnumber" => "",
                                        "institution" => "",
                                        "department" => "",
                                        "phone1" => "",
                                        "phone2" => "",
                                        "address" => "",
                                        "submitbutton" => "Create user",
                                        "timemodified" => 1709716405,
                                        "descriptiontrust" => 0,
                                        "description" => "",
                                        "descriptionformat" => "1",
                                        "mnethostid" => "1",
                                        "confirmed" => 1,
                                        "timecreated" => 1709716405,
                                        "password" => ""
                                    ];
        
                                    $userid = 0;
                                    //check if email already exists
                                    $user_data = $DB->get_record("user", array('email' => $paid_wpmoodlelte->email ));
        
                                    if(empty($user_data)){
                                        // check for unique username
                                        $username_check = $DB->get_record("user", array('username' => $username ));
                                        if(!empty($username_check)){
                                            $usernew['username'] = $username.'-'.dechex(rand(100,10000));
                                        }
                                        $userid = user_create_user($usernew, false, false);
                                        $user_data = $DB->get_record('user', array('id' => $userid));
                                        setnew_password_and_mail($user_data);
                                        unset_user_preference('create_password', $user_data);
                                        set_user_preference('auth_forcepasswordchange', 1, $user_data);
                                    }
                                    else{
                                        $userid = $user_data->id;
                                    }
        
                                    $DB->execute("Update {enrol_wpmoodlelte} set paid_status='" . $row->paid_status . "', user_id= ".$userid.", paid_date='".$row->paid_date."', paid_date_formatted= '".$row->paid_date_formatted."', paid_interface='".$row->paid_interface."' where challan_id=" . ($row->challan_no));
                                    $coursecontext = \context_course::instance($paid_wpmoodlelte->course_id);
        
                                    if(!is_enrolled($coursecontext, $userid)){
                                        $plugin_instance = $DB->get_record("enrol", array('id' => $paid_wpmoodlelte->enrol_id, 'enrol' => 'wpmoodlelte'));
                                        $plugin = enrol_get_plugin('wpmoodlelte');
                                        // Student role ID = 5
                                        $plugin->enrol_user($plugin_instance, $userid, 5);
                                        $plugin->email_welcome_message($plugin_instance, $user_data );
                                    }
                                }
                            }
                        }
                        $transaction->allow_commit();
                    }
                    else{
                        $row = $response->data;
                        if ($row->paid_status == 'Paid') {
                            array_push($paid_challan_ids, $row->challan_no);
                            $paid_wpmoodlelte = $DB->get_record("enrol_wpmoodlelte", array('challan_id' => $row->challan_no, 'paid_status' => 'Not Paid', 'deleted_at' => NULL ));
        
                            if($paid_wpmoodlelte->user_id != "0"){
                                $DB->execute("Update {enrol_wpmoodlelte} set paid_status='" . $row->paid_status . "', paid_date= '".$row->paid_date."', paid_date_formatted= '".$row->paid_date_formatted."', paid_interface='".$row->paid_interface."' where challan_id=" . ($row->challan_no));
                                
                                $coursecontext = \context_course::instance($paid_wpmoodlelte->course_id);
                                if(!is_enrolled($coursecontext, $paid_wpmoodlelte->user_id)){
                                    $plugin_instance = $DB->get_record("enrol", array('id' => $paid_wpmoodlelte->enrol_id, 'enrol' => 'wpmoodlelte'));
                                    $plugin = enrol_get_plugin('wpmoodlelte');
                                    $plugin->enrol_user($plugin_instance, $paid_wpmoodlelte->user_id, 5);
                                    $user = $DB->get_record("user", array('id' => $paid_wpmoodlelte->user_id ));
                                    $plugin->email_welcome_message($plugin_instance, $user );
                                }
                            }
                            else{
                                $fullName = explode(" ",$paid_wpmoodlelte->name);
                                $firstname = $fullName[0];
                                $lastname = (!empty($fullName[1])) ? $fullName[1] : '';
                                $username = explode('@',$paid_wpmoodlelte->email)[0];
                                $usernew = [
                                    "course" => 1,
                                    "username" => $username,
                                    "auth" => "manual",
                                    "suspended" => "0",
                                    "preference_auth_forcepasswordchange" => 0,
                                    "firstname" => $firstname,
                                    "lastname" => $lastname,
                                    "email" => $paid_wpmoodlelte->email,
                                    "maildisplay" => "2",
                                    "moodlenetprofile" => "",
                                    "city" => "",
                                    "country" => "",
                                    "timezone" => "99",
                                    "lang" => "en",
                                    "description_editor" => [
                                        "text" => "",
                                        "format" => "1"
                                    ],
                                    "mform_isexpanded_id_moodle_picture" => 1,
                                    "imagefile" => 771895340,
                                    "imagealt" => "",
                                    "firstnamephonetic" => "",
                                    "lastnamephonetic" => "",
                                    "middlename" => "",
                                    "alternatename" => "",
                                    "interests" => [],
                                    "idnumber" => "",
                                    "institution" => "",
                                    "department" => "",
                                    "phone1" => "",
                                    "phone2" => "",
                                    "address" => "",
                                    "submitbutton" => "Create user",
                                    "timemodified" => 1709716405,
                                    "descriptiontrust" => 0,
                                    "description" => "",
                                    "descriptionformat" => "1",
                                    "mnethostid" => "1",
                                    "confirmed" => 1,
                                    "timecreated" => 1709716405,
                                    "password" => ""
                                ];
        
                                $userid = 0;
                                //check if email already exists
                                $user_data = $DB->get_record("user", array('email' => $paid_wpmoodlelte->email ));
        
                                if(empty($user_data)){
                                    // check for unique username
                                    $username_check = $DB->get_record("user", array('username' => $username ));
                                    if(!empty($username_check)){
                                        $usernew['username'] = $username.'-'.dechex(rand(100,10000));
                                    }
                                    $userid = user_create_user($usernew, false, false);
                                    $user_data = $DB->get_record('user', array('id' => $userid));
                                    setnew_password_and_mail($user_data);
                                    unset_user_preference('create_password', $user_data);
                                    set_user_preference('auth_forcepasswordchange', 1, $user_data);
                                }
                                else{
                                    $userid = $user_data->id;
                                }
        
                                $DB->execute("Update {enrol_wpmoodlelte} set paid_status='" . $row->paid_status . "', user_id= ".$userid.", paid_date='".$row->paid_date."', paid_date_formatted='".$row->paid_date_formatted."', paid_interface='".$row->paid_interface."' where challan_id=" . ($row->challan_no));
                                $coursecontext = \context_course::instance($paid_wpmoodlelte->course_id);
        
                                if(!is_enrolled($coursecontext, $userid)){
                                    $plugin_instance = $DB->get_record("enrol", array('id' => $paid_wpmoodlelte->enrol_id, 'enrol' => 'wpmoodlelte'));
                                    $plugin = enrol_get_plugin('wpmoodlelte');
                                    // Student role ID = 5
                                    $plugin->enrol_user($plugin_instance, $userid, 5);
                                    $plugin->email_welcome_message($plugin_instance, $user_data );
                                }
                            }
                        }
                        $transaction->allow_commit();
                    }
                }
                else{
                    $transaction->rollback();
                    error_log('Invalid API Response => '.json_encode($response));
                }
            }
            catch (Exception $exp){
                $transaction->rollback($exp);
                echo 'Error...'.$exp->getMessage();
                error_log('enrol_wpmoodlelte\task\wpmoodlelte_user_enrol___scheduled_task___'.$exp->getTraceAsString);
            }
        }
    }
    catch (Exception $exp){
        echo 'Error...'.$exp->getMessage();
        error_log('enrol_wpmoodlelte\task\wpmoodlelte_user_enrol___scheduled_task___'.$exp->getTraceAsString);
    }



    /*$email_user1 = new stdClass;
    $email_user1>email= "abc@example.com";
    $email_user1>firstname=" ";
    $email_user1>lastname;
    $email_user1>maildisplay = true;
    $email_user1>mailformat = 1; // 0 (zero) text-only emails, 1 (one) for HTML/Text emails.
    $email_user1>id=-99;
    $a= email_to_user($email_user, $email_user1, $subject, $content);*/

    echo 'enrol_users_via_wpmoodlelte has been executed successfully';
}

function http_request($url, $method, $data = null, $headers = [] ){

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);

    if($method == 'POST'){
        $json_string = json_encode($data);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
    }
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );

    $data = curl_exec($curl);

    curl_close($curl);

    return $data;
}



class enrol_wpmoodlelte_plugin extends enrol_plugin {

    public function show_enrolme_link(stdClass $instance) {

        if (true !== $this->can_self_enrol($instance, false)) {
            return false;
        }

        return true;
    }

    public function can_self_enrol(stdClass $instance, $checkuserenrolment = true) {
        return true;
    }

    public function email_welcome_message($instance, $user) {
        global $CFG, $DB;
    
        $course = $DB->get_record('course', array('id'=>$instance->courseid), '*', MUST_EXIST);
        $context = context_course::instance($course->id);
    
        $a = new stdClass();
        $a->coursename = format_string($course->fullname, true, array('context'=>$context));
        $a->userfirstname = format_string($user->firstname);
        $a->profileurl = "$CFG->wwwroot/user/view.php?id=$user->id&course=$course->id";
        $a->courselink = "$CFG->wwwroot/course/view.php?id=$course->id";
    
        if (trim($instance->customtext1) !== '') {
            $message = $instance->customtext1;
            $key = array('{$a->coursename}', '{$a->profileurl}', '{$a->fullname}', '{$a->email}');
            $value = array($a->coursename, $a->profileurl, fullname($user), $user->email);
            $message = str_replace($key, $value, $message);
            if (strpos($message, '<') === false) {
                // Plain text only.
                $messagetext = $message;
                $messagehtml = text_to_html($messagetext, null, false, true);
            } else {
                // This is most probably the tag/newline soup known as FORMAT_MOODLE.
                $messagehtml = format_text($message, FORMAT_MOODLE, array('context'=>$context, 'para'=>false, 'newlines'=>true, 'filter'=>true));
                $messagetext = html_to_text($messagehtml);
            }
        } else {
            $messagetext = get_string('welcometocoursetext', 'enrol_wpmoodlelte', $a);
            $messagehtml = text_to_html($messagetext, null, false, true);
        }
        //format_string($course->fullname, true, array('context'=>$context))
        $subject = get_string('welcometocourse', 'enrol_wpmoodlelte', 'Welcome Aboard! Access to LUMSx LMS' );
    
        $sendoption = $instance->customint4;
        $contact = $this->get_welcome_email_contact($sendoption, $context);
    
        // Directly emailing welcome message rather than using messaging.
        //dd($user, $contact, $subject, $messagetext, $messagehtml, $instance, $context);
        email_to_user($user, $contact, $subject, $messagetext, $messagehtml);
    }

    public function get_welcome_email_contact($sendoption, $context) {
        global $CFG;

        $contact = null;
        // Send as the first user assigned as the course contact.
        if ($sendoption == ENROL_SEND_EMAIL_FROM_COURSE_CONTACT) {
            $rusers = array();
            if (!empty($CFG->coursecontact)) {
                $croles = explode(',', $CFG->coursecontact);
                list($sort, $sortparams) = users_order_by_sql('u');
                // We only use the first user.
                $i = 0;
                do {
                    $userfieldsapi = \core_user\fields::for_name();
                    $allnames = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
                    $rusers = get_role_users($croles[$i], $context, true, 'u.id,  u.confirmed, u.username, '. $allnames . ',
                    u.email, r.sortorder, ra.id', 'r.sortorder, ra.id ASC, ' . $sort, null, '', '', '', '', $sortparams);
                    $i++;
                } while (empty($rusers) && !empty($croles[$i]));
            }
            if ($rusers) {
                $contact = array_values($rusers)[0];
            }
        } else if ($sendoption == ENROL_SEND_EMAIL_FROM_KEY_HOLDER) {
            // Send as the first user with enrol/self:holdkey capability assigned in the course.
            list($sort) = users_order_by_sql('u');
            $keyholders = get_users_by_capability($context, 'enrol/self:holdkey', 'u.*', $sort);
            if (!empty($keyholders)) {
                $contact = array_values($keyholders)[0];
            }
        }

        // If send welcome email option is set to no reply or if none of the previous options have
        // returned a contact send welcome message as noreplyuser.
        if ($sendoption == ENROL_SEND_EMAIL_FROM_NOREPLY || empty($contact)) {
            $contact = core_user::get_noreply_user();
        }

        return $contact;
    }


    public function get_instance_name($instance) {
        global $DB;

        if (empty($instance->name)) {
            if (!empty($instance->roleid) and $role = $DB->get_record('role', array('id'=>$instance->roleid))) {
                $role = ' (' . role_get_name($role, context_course::instance($instance->courseid, IGNORE_MISSING)) . ')';
            } else {
                $role = '';
            }
            $enrol = $this->get_name();
            return get_string('pluginname', 'enrol_'.$enrol) . $role;
        } else {
            return format_string($instance->name);
        }
    }

    function send_wpmoodlelte_email($firstname, $email, $payonline_url, $download_url, $challan_id, $course_name){
        global $DB;
        $tempDir = make_temp_directory('downloads');
        $filePath = $tempDir . '/' . "$challan_id.pdf";
        file_put_contents($filePath, file_get_contents($download_url));
        
        $user = $DB->get_record("user", array('email' => $email ));

        if(!$user){
            $user = (object)[ 'id' => hexdec(uniqid()), 'email' => $email, 'mailformat' => 1 ];
        }
        $a = new stdClass();
        $a->downloadlink = $download_url;
        $a->payonline = $payonline_url;
        $a->userfirstname = format_string($firstname);
        $a->course = format_string($course_name);
    
    
        $messagetext = get_string('wpmoodleltenewemail', 'enrol_wpmoodlelte', $a);
        $messagehtml = text_to_html($messagetext, null, false, true);
    
        $status = email_to_user($user, 'LUMSx', "LUMSx | $course_name | Course Registration", $messagehtml, $messagehtml, $filePath, "1222400000332.pdf" );
        
        unlink($filePath);
        return $status;        
    }

    public function add_instance($course, array $fields = null) {

        return parent::add_instance($course, $fields);
    }

    protected function get_status_options() {
        $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
            ENROL_INSTANCE_DISABLED => get_string('no'));
        return $options;
    }

    public function allow_unenrol(stdClass $instance) {
        return true;
    }

    public function allow_unenrol_user(stdClass $instance, stdClass $ue) {
        return true;
    }

    public function allow_manage(stdClass $instance) {
        return true;
    }

    public function use_standard_editing_ui() {
        return true;
    }

    public function can_add_instance($courseid) {
        global $DB;

        $context = context_course::instance($courseid, MUST_EXIST);

        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/wpmoodlelte:config', $context)) {
            return false;
        }

        if ($DB->record_exists('enrol', array('courseid' => $courseid, 'enrol' => 'easy'))) {
            return false;
        }

        return true;
    }

    public function can_delete_instance($instance) {
        $context = context_course::instance($instance->courseid);

        return has_capability('enrol/wpmoodlelte:delete', $context);
    }

    public function edit_instance_form($instance, MoodleQuickForm $mform, $context) {

        global $COURSE, $CFG, $DB, $OUTPUT;

        require_once(dirname(__FILE__) . '/locallib.php');

        $mform->addElement('text', 'cost', get_string('course_price', 'enrol_wpmoodlelte'));
        $mform->setType('cost', PARAM_TEXT);
        $mform->setDefault('course_price', "0.00");

        $mform->addElement('hidden', 'currency', 'PKR');
        $mform->setType('currency', PARAM_TEXT);

    }

    public function update_instance($instance, $data) {

        global $DB;

        $cost = $data->cost;
        $currency = $data->currency;
        $enrol_id = $data->id;


        /*$enrolmentcodes = $DB->get_records('enrol_easy', array('course_id' => $instance->courseid));

        $allcodesobj = $DB->get_records('enrol_easy');
        $allcodes = array();

        foreach($allcodesobj as $code) {
            $allcodes[] = $code;
        }

        if ($data->regenerate_codes) {

            foreach($enrolmentcodes as $enrolmentcode) {

                $code = randomstring(6);

                while (array_key_exists($code, $allcodes)) {
                    $code = randomstring(6);
                }

                $dataobj = new stdClass();
                $dataobj->id = $enrolmentcode->id;
                $dataobj->enrolmentcode = $code;

                $allcodes[] = $code;
                $DB->update_record('enrol_easy', $dataobj);

            }

        }
        parent::update_instance($instance, $data);*/

        return $DB->execute("Update {enrol} set cost='".$cost."', currency='".$currency."' where id='".$enrol_id."'");
        header('Location: ' . $data->returnurl);
        exit;
    }

    public function can_hide_show_instance($instance) {
        $context = context_course::instance($instance->courseid);

        return has_capability('enrol/wpmoodlelte:config', $context);
    }

    public function enrol_course_delete($course) {

        $enrolmentcodes = $DB->delete_records('enrol_wpmoodlelte', array('course_id' => $course->id));

        parent::enrol_course_delete($course);

    }
    public function enrol_page_hook(stdClass $instance){

        global $CFG, $USER, $DB, $OUTPUT;


        require_once("$CFG->dirroot/enrol/wpmoodlelte/locallib.php");

        $wpmoodlelte = $DB->get_record('enrol_wpmoodlelte', array('course_id' => $instance->courseid, 'user_id' => $USER->id, 'deleted_at' => NULL ),'*');

        $enrol_url = new moodle_url('/enrol/index.php'.(isset($_REQUEST["id"]) ? '?id='.$_REQUEST["id"]:''));

        $mform = new wpmoodlelteformnew($enrol_url, ['cost' => $instance->cost ? $instance->cost : 0.00 , 'course_id' => $instance->courseid, 'wpmoodlelte' => $wpmoodlelte ]);
        // dd($instance, $mform->get_data());

        if($mform->get_data()){

            /*
            $date=date_create();
            date_add($date,date_interval_create_from_date_string("10 days"));
            $date = date_format($date,"Y-m-d H:i:s");

            $url = $CFG->wpmoodlelte_api_url.'/wpmoodleltes';//?ids=1222202211432,1222200000041';
            $headers = array('Content-Type:application/json','Authorization: Bearer '.$CFG->wpmoodlelte_access_token);
            $course = $DB->get_record('course', array('id'=> $instance->courseid), '*', MUST_EXIST);
            $amount = $_POST["wpmoodlelteform_amount"];
            if(isset($_POST['discount_promo']) && strtolower($_POST['discount_promo']) == 'colabs20'){
                $amount = '24000';
            }
            $data = array(
                'name' => $_POST["wpmoodlelteform_student_full_name"],
                'email' => $_POST["wpmoodlelteform_student_email"],
                'mobile' => $USER->phone1 ? $USER->phone1 : ($USER->phone2 ? $USER->phone2 : ''),
                'total_amount' => $amount,
                'due_date' => $date,//"2021-01-10 17:50:00",
                'order_id' => rand(9999,999999999),
                'reserved' => "",
                'items' => [
                    [
                        "title" => $course->fullname,
                        "quantity" => (int)"1",
                        'amount' => $amount
                    ]
                ]
            );
            $response = http_request($url, 'POST', $data, $headers );
            $response = json_decode($response, true);
            
            */
            // $course_id, $name, $email, $amount, $mobile = '';
            $phone = $USER->phone1 ? $USER->phone1 : ($USER->phone2 ? $USER->phone2 : '');
            $response = $this->create_new_wpmoodlelte($instance->courseid, $name = $_POST['wpmoodlelteform_student_full_name'], $email = $_POST["wpmoodlelteform_student_email"], $amount = $_POST["wpmoodlelteform_amount"], $mobile = $phone);

            if($response["code"] == 200){
                $resp = $this->insertwpmoodlelte($response['data'], $instance->courseid, $USER->id, $instance->id);

                redirect($enrol_url, $resp['msg'], null, $resp['type']);
            }
            else{
                error_log('Moodle Create wpmoodlelte Error.....'.(json_encode($response)));
                return redirect($enrol_url, 'Unexpected System Error!!!', null, \core\output\notification::NOTIFY_ERROR);
            }
        }

        $data = new stdClass();
        $data->full_name = $USER->firstname.' '.$USER->lastname;
        $data->email = $USER->email;
        $mform->set_data($data);
        
        ob_start();
        $mform->display();
        $output = ob_get_clean();
        return $OUTPUT->box($output);
    }

    public function create_new_wpmoodlelte($course_id, $name, $email, $amount, $mobile = ''){
        global $CFG, $DB;
        //dd($course_id, $name, $email, $amount, $mobile);
        // $name = $form_data["wpmoodlelteform_student_full_name"];
        // $email = $form_data["wpmoodlelteform_student_email"];
        // $mobile = $form_data["wpmoodlelteform_student_mobile"] ?? '';
        // $amount = $form_data["wpmoodlelteform_amount"];

        if(empty($name) || empty($email) || empty($amount))
            return [];

        $date = date_create();
        date_add($date,date_interval_create_from_date_string("14 days"));
        $date = date_format($date,"Y-m-d H:i:s");

        $url = $CFG->wpmoodlelte_api_url.'/wpmoodleltes';//?ids=1222202211432,1222200000041';
        $headers = array('Content-Type:application/json','Authorization: Bearer '.$CFG->wpmoodlelte_access_token);
        $course = $DB->get_record('course', array('id'=> $course_id), '*', MUST_EXIST);

        //promo code
        /*if(isset($form_data['discount_promo']) && strtolower($form_data['discount_promo']) == 'colabs20'){
            $amount = '24000';
        }
        else if (isset($form_data['discount_promo']) && strtolower($form_data['discount_promo']) == 'tesl35dis'){
            $amount = '8125';
        }*/

        $email_domain = substr(strrchr($email, "@"), 1);

        if($email_domain == 'lums.edu.pk'){
            $amount = round($amount * 0.8 / 0.85);
        }

        $data = array(
            'name' => $name,
            'email' => $email,
            'mobile' => $mobile,
            'total_amount' => $amount,
            'due_date' => $date,//"2021-01-10 17:50:00",
            'order_id' => rand(9999,999999999),
            'reserved' => "",
            'items' => [
                [
                    "title" => $course->fullname,
                    "quantity" => (int)"1",
                    'amount' => $amount
                ]
            ]
        );
        $response = http_request($url, 'POST', $data, $headers );
        $response = json_decode($response, true);
        
        return $response;

    }

    public function http_request($url, $method, $data = null, $headers = [] ){

        $curl = curl_init();

        $json_string = json_encode($data);

        curl_setopt($curl, CURLOPT_URL, $url);

        if($method == 'POST'){
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );

        $data = curl_exec($curl);

        curl_close($curl);

        return $data;
    }

    public function insertwpmoodlelte($data, $course_id, $user_id, $enrol_id){
        try {
            global $DB;
            if ($data) {
                $dataobj = new stdClass();
                $dataobj->course_id = $course_id;
                $dataobj->user_id = (int)$user_id ?? NULL;
                $dataobj->enrol_id = (int)$enrol_id;
                $dataobj->challan_id = $data['challan_no'];
                $dataobj->access_code = $data['access_code'];
                $dataobj->due_date = $data['due_date'];
                $dataobj->created_date = $data['created_date'];
                $dataobj->total_amount = $data['total_amount'];
                $dataobj->paid_status = $data['paid_status'];
                $dataobj->paid_interface = $data['paid_interface'];
                $dataobj->name = $data['name'];
                $dataobj->email = $data['email'];
                $dataobj->mobile = $data['mobile'];
                $dataobj->order_id = $data['order_id'];
                $dataobj->reserved = $data['reserved'];
                $dataobj->due_date_formatted = $data['created_date_formatted'];
                $dataobj->paid_date_formatted = $data['paid_date_formatted'];
                $dataobj->total_amount_formatted = $data['total_amount_formatted'];
                $dataobj->url_for_online_payment = $data['url_for_online_payment'];
                $dataobj->url_for_download_wpmoodlelte = str_replace("https://wpmoodlelteapi2.lums.edu.pk:4433", "https://api-pms.lums.edu.pk", $data['url_for_download_wpmoodlelte']);
                
                    if(!empty($user_id)){
                        $DB->execute("Update {enrol_wpmoodlelte} set deleted_at=".time()." where course_id=$course_id AND user_id=$user_id");
                    }
                    else{
                        $DB->execute("Update {enrol_wpmoodlelte} set deleted_at=".time()." where course_id=$course_id AND email='".$data['email']."'");
                    }

                $enrol_wpmoodlelte = $DB->insert_record('enrol_wpmoodlelte', $dataobj);
                
                $course = $DB->get_record('course', ['id' => $course_id]);

                $dataItem = new stdClass();
                $dataItem->enrol_wpmoodlelte_id = $enrol_wpmoodlelte;
                $dataItem->title = $data['items'][0]['title'];
                $dataItem->course_id = $course_id;
                $dataItem->amount = $data['items'][0]['amount'];
                $enrol_wpmoodlelte = $DB->insert_record('enrol_wpmoodlelte_items', $dataItem);
                
                //Email wpmoodlelte to User 
                $this->send_wpmoodlelte_email($dataobj->name, $dataobj->email, $dataobj->url_for_online_payment, $dataobj->url_for_download_wpmoodlelte, $dataobj->challan_id, $course->fullname);

                return array('type' => \core\output\notification::NOTIFY_SUCCESS, 'data' => $dataobj, 'msg' => '<h3>wpmoodlelte Generated Successfully<h3>' );
            }
        }
        catch (Exception $exp){
            dump($exp);
            return array('type' => \core\output\notification::NOTIFY_ERROR,'data' => [], 'msg' => $exp->getMessage() );
        }
    }

}


/*$mform->updateAttributes(array('id' => 'enrol_easy_settings'));

        $options = $this->get_status_options();
        $mform->addElement('select', 'status', get_string('status', 'enrol_easy'), $options);
        $mform->setType('status', PARAM_NOTAGS);
        $mform->setDefault('status', $this->get_config('status', 'enrol_easy'));
        $mform->addHelpButton('status', 'status', 'enrol_easy');

        $options = array('optional' => true);
        $mform->addElement('date_time_selector', 'enrolstartdate', get_string('enrolstartdate', 'enrol_easy'), $options);
        $mform->setType('enrolstartdate', PARAM_NOTAGS);
        $mform->setDefault('enrolstartdate', 0);
        $mform->addHelpButton('enrolstartdate', 'enrolstartdate', 'enrol_easy');

        $options = array('optional' => true);
        $mform->addElement('date_time_selector', 'enrolenddate', get_string('enrolenddate', 'enrol_easy'), $options);
        $mform->setType('enrolenddate', PARAM_NOTAGS);
        $mform->setDefault('enrolenddate', 0);
        $mform->addHelpButton('enrolenddate', 'enrolenddate', 'enrol_easy');

        $mform->addElement('header', 'nameforyourheaderelement', get_string('header_coursecodes', 'enrol_easy'));

        $allcodesobj = $DB->get_records('enrol_easy');
        $allcodes = array();

        foreach($allcodesobj as $c) {
            $allcodes[] = $c;
        }

        $code = $DB->get_records('enrol_easy', array('course_id' => $COURSE->id, 'group_id' => null));

        if ($code && (count($code) > 1)) {
            $DB->delete_records('enrol_easy', array('course_id' => $COURSE->id, 'group_id' => null));
            $code = NULL;
        }
        else {
            $code = array_pop($code);
        }

        if (!$code) {
            $code = randomstring(6);

            while (array_key_exists($code, $allcodes)) {
                $code = randomstring(6);
            }

            $dataobj = new stdClass();
            $dataobj->course_id = $COURSE->id;
            $dataobj->enrolmentcode = $code;
            $DB->insert_record('enrol_easy', $dataobj);

            $allcodes[] = $code;

        }
        else {
            $code = $code->enrolmentcode;
        }

        $coursetext = get_string('coursetext', 'enrol_easy');

        $codetext = $mform->addElement('text', 'course_' . $COURSE->id, $coursetext . $COURSE->fullname, array('readonly' => ''));
        $mform->setType('course_' . $COURSE->id, PARAM_NOTAGS);
        $mform->setDefault('course_' . $COURSE->id,  $code);
        $mform->updateElementAttr('course_' . $COURSE->id, array('data-type' => 'enroleasycode')); // For whatever reason it refuses to set a class, so data attr it is.
        $mform->updateElementAttr('course_' . $COURSE->id, array('data-coursename' => $COURSE->fullname));

        $groups = $DB->get_records('groups', array('courseid' => $COURSE->id));

        foreach ($groups as $group) {

            $code = $DB->get_records('enrol_easy', array('group_id' => $group->id));

            if ($code && (count($code) > 1)) {
                $DB->delete_records('enrol_easy', array('course_id' => $COURSE->id, 'group_id' => $group->id));
                $code = NULL;
            }
            else {
                $code = array_pop($code);
            }

            if ($code && $code->course_id != $COURSE->id) {
                $DB->delete_records('enrol_easy', array('enrolmentcode' => $code->enrolmentcode));
                $code = NULL;
            }

            if (!$code) {
                $code = randomstring(6);

                while (array_key_exists($code, $allcodes)) {
                    $code = randomstring(6);
                }

                $dataobj = new stdClass();
                $dataobj->course_id = $COURSE->id;
                $dataobj->group_id = $group->id;
                $dataobj->enrolmentcode = $code;
                $DB->insert_record('enrol_easy', $dataobj);
                $allcodes[] = $code;
            }
            else {
                $code = $code->enrolmentcode;
            }

            $grouptext = get_string('grouptext', 'enrol_easy');

            $codetext = $mform->addElement('text', 'group_' . $group->id, $grouptext . $group->name, array('readonly' => '', 'value' => $code));
            $mform->setType('group_' . $group->id, PARAM_NOTAGS);
            $mform->setDefault('group_' . $group->id,  $code);
            $mform->updateElementAttr('group_' . $group->id, array('data-type' => 'enroleasycode')); // For whatever reason it refuses to set a class, so data attr it is.
            $mform->updateElementAttr('group_' . $group->id, array('data-coursename' => $COURSE->fullname));
            $mform->updateElementAttr('group_' . $group->id, array('data-groupname' => $group->name));

        }

        $mform->addElement('checkbox', 'regenerate_codes', get_string('regenerate_codes', 'enrol_easy'));
        $mform->setType('regenerate_codes', PARAM_NOTAGS);
        $mform->setDefault('regenerate_codes', $this->get_config('regenerate_codes'));
        $mform->addHelpButton('regenerate_codes', 'regenerate_codes', 'enrol_easy');


        if ($this->get_config('qrenabled')) {

            $jquery_url = new moodle_url('/enrol/easy/js/jquery-3.2.0.min.js');
            $qrcode_url = new moodle_url('/enrol/easy/js/jquery.qrcode.min.js');
            $js_url = new moodle_url('/enrol/easy/js/enrol_easy.js');

            $mform->addElement('html', '<script src="' . $jquery_url . '"></script>');
            $mform->addElement('html', '<script src="' . $qrcode_url . '"></script>');
            $mform->addElement('html', '<script src="' . $js_url . '"></script>');
        }*/


        /*
    public function get_form() {
        global $CFG, $OUTPUT, $USER;
        if (!enrol_is_enabled('easy') || !isloggedin()) {
            return '';
        }

        require_once(dirname(__FILE__) . '/locallib.php');

        $enrol_easy_qr = new moodle_url('/enrol/easy/qr.php');
        $enrol_easy_qr = str_replace("http://", "https://", $enrol_easy_qr);

        $data = array(

            'internal' => array(
                'sesskey' => $USER->sesskey
            ),
            'pages' => array(
                'enrol_easy' => new moodle_url('/enrol/easy/index.php'),
                'enrol_easy_qr' => $enrol_easy_qr
            ),
            'component' => array(
                'main_javascript' => new moodle_url('/enrol/easy/js/enrol_easy.js'),
                'jquery' => new moodle_url('/enrol/easy/js/jquery-3.2.0.min.js'),

            ),
            'config' => array(
                'qrenabled' => $this->get_config('qrenabled') && ($this->get_config('showqronmobile') || !isMobile()),
            ),
            'lang' => array(
                'enrolform_course_code' => get_string('enrolform_course_code', 'enrol_easy'),
                'enrolform_submit' => get_string('enrolform_submit', 'enrol_easy')
            ),

        );

        return $OUTPUT->render_from_template('enrol_easy/form', $data);

    }

    public function use_standard_editing_ui() {
        return true;
    }



    public function edit_instance_form($instance, MoodleQuickForm $mform, $context) {

        global $COURSE, $CFG, $DB, $OUTPUT;

        require_once(dirname(__FILE__) . '/locallib.php');

        $mform->updateAttributes(array('id' => 'enrol_easy_settings'));

        $options = $this->get_status_options();
        $mform->addElement('select', 'status', get_string('status', 'enrol_easy'), $options);
        $mform->setType('status', PARAM_NOTAGS);
        $mform->setDefault('status', $this->get_config('status', 'enrol_easy'));
        $mform->addHelpButton('status', 'status', 'enrol_easy');

        $options = array('optional' => true);
        $mform->addElement('date_time_selector', 'enrolstartdate', get_string('enrolstartdate', 'enrol_easy'), $options);
        $mform->setType('enrolstartdate', PARAM_NOTAGS);
        $mform->setDefault('enrolstartdate', 0);
        $mform->addHelpButton('enrolstartdate', 'enrolstartdate', 'enrol_easy');

        $options = array('optional' => true);
        $mform->addElement('date_time_selector', 'enrolenddate', get_string('enrolenddate', 'enrol_easy'), $options);
        $mform->setType('enrolenddate', PARAM_NOTAGS);
        $mform->setDefault('enrolenddate', 0);
        $mform->addHelpButton('enrolenddate', 'enrolenddate', 'enrol_easy');

        $mform->addElement('header', 'nameforyourheaderelement', get_string('header_coursecodes', 'enrol_easy'));

        $allcodesobj = $DB->get_records('enrol_easy');
        $allcodes = array();

        foreach($allcodesobj as $c) {
            $allcodes[] = $c;
        }

        $code = $DB->get_records('enrol_easy', array('course_id' => $COURSE->id, 'group_id' => null));

        if ($code && (count($code) > 1)) {
            $DB->delete_records('enrol_easy', array('course_id' => $COURSE->id, 'group_id' => null));
            $code = NULL;
        }
        else {
            $code = array_pop($code);
        }

        if (!$code) {
            $code = randomstring(6);

            while (array_key_exists($code, $allcodes)) {
                $code = randomstring(6);
            }

            $dataobj = new stdClass();
            $dataobj->course_id = $COURSE->id;
            $dataobj->enrolmentcode = $code;
            $DB->insert_record('enrol_easy', $dataobj);

            $allcodes[] = $code;

        }
        else {
            $code = $code->enrolmentcode;
        }

        $coursetext = get_string('coursetext', 'enrol_easy');

        $codetext = $mform->addElement('text', 'course_' . $COURSE->id, $coursetext . $COURSE->fullname, array('readonly' => ''));
        $mform->setType('course_' . $COURSE->id, PARAM_NOTAGS);
        $mform->setDefault('course_' . $COURSE->id,  $code);
        $mform->updateElementAttr('course_' . $COURSE->id, array('data-type' => 'enroleasycode')); // For whatever reason it refuses to set a class, so data attr it is.
        $mform->updateElementAttr('course_' . $COURSE->id, array('data-coursename' => $COURSE->fullname));

        $groups = $DB->get_records('groups', array('courseid' => $COURSE->id));

        foreach ($groups as $group) {

            $code = $DB->get_records('enrol_easy', array('group_id' => $group->id));

            if ($code && (count($code) > 1)) {
                $DB->delete_records('enrol_easy', array('course_id' => $COURSE->id, 'group_id' => $group->id));
                $code = NULL;
            }
            else {
                $code = array_pop($code);
            }

            if ($code && $code->course_id != $COURSE->id) {
                $DB->delete_records('enrol_easy', array('enrolmentcode' => $code->enrolmentcode));
                $code = NULL;
            }

            if (!$code) {
                $code = randomstring(6);

                while (array_key_exists($code, $allcodes)) {
                    $code = randomstring(6);
                }

                $dataobj = new stdClass();
                $dataobj->course_id = $COURSE->id;
                $dataobj->group_id = $group->id;
                $dataobj->enrolmentcode = $code;
                $DB->insert_record('enrol_easy', $dataobj);
                $allcodes[] = $code;
            }
            else {
                $code = $code->enrolmentcode;
            }

            $grouptext = get_string('grouptext', 'enrol_easy');

            $codetext = $mform->addElement('text', 'group_' . $group->id, $grouptext . $group->name, array('readonly' => '', 'value' => $code));
            $mform->setType('group_' . $group->id, PARAM_NOTAGS);
            $mform->setDefault('group_' . $group->id,  $code);
            $mform->updateElementAttr('group_' . $group->id, array('data-type' => 'enroleasycode')); // For whatever reason it refuses to set a class, so data attr it is.
            $mform->updateElementAttr('group_' . $group->id, array('data-coursename' => $COURSE->fullname));
            $mform->updateElementAttr('group_' . $group->id, array('data-groupname' => $group->name));

        }

        $mform->addElement('checkbox', 'regenerate_codes', get_string('regenerate_codes', 'enrol_easy'));
        $mform->setType('regenerate_codes', PARAM_NOTAGS);
        $mform->setDefault('regenerate_codes', $this->get_config('regenerate_codes'));
        $mform->addHelpButton('regenerate_codes', 'regenerate_codes', 'enrol_easy');


        if ($this->get_config('qrenabled')) {

            $jquery_url = new moodle_url('/enrol/easy/js/jquery-3.2.0.min.js');
            $qrcode_url = new moodle_url('/enrol/easy/js/jquery.qrcode.min.js');
            $js_url = new moodle_url('/enrol/easy/js/enrol_easy.js');

            $mform->addElement('html', '<script src="' . $jquery_url . '"></script>');
            $mform->addElement('html', '<script src="' . $qrcode_url . '"></script>');
            $mform->addElement('html', '<script src="' . $js_url . '"></script>');
        }

    }
    public function get_instance_defaults() {
        $fields = array();

        return $fields;
    }
    public function edit_instance_validation($data, $files, $instance, $context) {

        $errors = array();

        return $errors;

    }
    public function update_instance($instance, $data) {
        global $DB;

        $enrolmentcodes = $DB->get_records('enrol_easy', array('course_id' => $instance->courseid));

        $allcodesobj = $DB->get_records('enrol_easy');
        $allcodes = array();

        foreach($allcodesobj as $code) {
            $allcodes[] = $code;
        }

        if ($data->regenerate_codes) {

            foreach($enrolmentcodes as $enrolmentcode) {

                $code = randomstring(6);

                while (array_key_exists($code, $allcodes)) {
                    $code = randomstring(6);
                }

                $dataobj = new stdClass();
                $dataobj->id = $enrolmentcode->id;
                $dataobj->enrolmentcode = $code;

                $allcodes[] = $code;
                //$DB->update_record('enrol_easy', $dataobj);

            }

        }
        parent::update_instance($instance, $data);
        header('Location: ' . $data->returnurl);
        exit;
    }


    public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue) {
        $actions = array();
        $context = $manager->get_context();
        $instance = $ue->enrolmentinstance;
        $params = $manager->get_moodlepage()->url->params();
        $params['ue'] = $ue->id;
        if ($this->allow_unenrol_user($instance, $ue) && has_capability("enrol/easy:unenrol", $context)) {
            $url = new moodle_url('/enrol/unenroluser.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/delete', ''), get_string('unenrol', 'enrol'), $url, array('class'=>'unenrollink', 'rel'=>$ue->id));
        }
        if ($this->allow_manage($instance) && has_capability("enrol/easy:manage", $context)) {
            $url = new moodle_url('/enrol/editenrolment.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/edit', ''), get_string('edit'), $url, array('class'=>'editenrollink', 'rel'=>$ue->id));
        }
        return $actions;
    }

    public function add_default_instance($course) {
        $fields = $this->get_instance_defaults();

        return $this->add_instance($course, $fields);

    }

    */