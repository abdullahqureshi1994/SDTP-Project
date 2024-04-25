<?php
/**
 * Allows course enrolment via a simple text code.
 *
 * @package   enrol_easy
 * @copyright 2017 Dearborn Public Schools
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/formslib.php");
require_once(dirname(__FILE__) . '/../../config.php');

/*function randomstring($length = 10): string
{
    //$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $characters = '23456789abcdefghijkmnpqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}*/
/*
 * https://stackoverflow.com/questions/4117555/simplest-way-to-detect-a-mobile-device
 */
/*function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}*/

/*class enrolform extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('text', 'enrolform_course_code', get_string('enrolform_course_code', 'enrol_easy'));
        $mform->setType('enrolform_course_code', PARAM_NOTAGS);

        $mform->addElement('submit', 'enrolform_submit', get_string('enrolform_submit', 'enrol_easy'));
    }
    function validation($data, $files) {
        return array();
    }
}*/

class wpmoodlelteformnew extends moodleform {

    public function definition() {

        global $CFG , $USER, $DB;

        $mform = $this->_form;

        //Full Name
        $mform->addElement('hidden', 'wpmoodlelteform_student_full_name', $USER->firstname.' '.$USER->lastname);
        $mform->setType('wpmoodlelteform_student_full_name', PARAM_TEXT);
        //$mform->setDefault('wpmoodlelteform_student_full_name', $USER->firstname.' '.$USER->lastname);

        //Email
        $mform->addElement('hidden', 'wpmoodlelteform_student_email', $USER->email);
        $mform->setType('wpmoodlelteform_student_email', PARAM_EMAIL);
        //$mform->setDefault('wpmoodlelteform_student_email', $USER->email);

        //Phone
        /*$mform->addElement('text', 'wpmoodlelteform_student_contact_no', get_string('wpmoodlelteform_student_contact_no', 'enrol_wpmoodlelte'));
        $mform->setType('wpmoodlelteform_student_contact_no', PARAM_TEXT);
        $mform->setDefault('wpmoodlelteform_student_contact_no', '0000-0000000');*/
        //$USER->phone1 ? $USER->phone1 : ($USER->phone2 ? $USER->phone2 : '')

        /*//Course Title
        $mform->addElement('hidden', 'wpmoodlelteform_course_title', $course->fullname);
        $mform->setType('wpmoodlelteform_course_title', PARAM_TEXT);
        $mform->setDefault('wpmoodlelteform_course_title',$course->fullname);

        //Order Id
        $mform->addElement('hidden', 'wpmoodlelteform_order_id', rand(9999,999999999));
        $mform->setType('wpmoodlelteform_order_id', PARAM_TEXT);

        //Reserved
        $mform->addElement('hidden', 'wpmoodlelteform_reserved', "");
        $mform->setType('wpmoodlelteform_reserved', PARAM_TEXT);*/

        $costarray = array();
        $costarray[] =& $mform->createElement('html', '<h3 style="padding: 1rem 0rem;" >Rs: '.number_format($this->_customdata['cost']).'</h3>');
        $mform->addGroup($costarray, 'buttonar', '<h3>Price</h3>', array(' '), false);

        //Amount
        $mform->addElement('hidden', 'wpmoodlelteform_amount', $this->_customdata['cost']);
        $mform->setType('wpmoodlelteform_amount', PARAM_TEXT);
        //$mform->setDefault('wpmoodlelteform_amount', $this->_customdata['cost'] );
        //Promo
        $mform->addElement('text','discount_promo','Promo Code');
        $mform->setType('discount_promo', PARAM_TEXT);
        
        //Download Button
        if($this->_customdata['wpmoodlelte']) {
            $buttonarray = array();
            $buttonarray[] =& $mform->createElement('html', '<input type="submit" class="btn btn-default wpmoodlelte-btn" name="wpmoodlelteform_submit" id="id_wpmoodlelteform_submit" value="Generate New wpmoodlelte">');
            $buttonarray[] =& $mform->createElement('html', '<a class="btn btn-info mt-3 wpmoodlelte-btn" target="_blank" href="' . $this->_customdata['wpmoodlelte']->url_for_online_payment . '" >Online Payment</a>');            
            $buttonarray[] =& $mform->createElement('html', '<a class="btn btn-info ml-0 mr-3 mt-3 wpmoodlelte-btn" target="_blank" href="' . $this->_customdata['wpmoodlelte']->url_for_download_wpmoodlelte . '" >Download</a>');
            $mform->addGroup($buttonarray, 'wpmoodlelte_btn_array', '', array(' '), false);
            //$mform->addElement('submit', 'wpmoodlelteform_submit', get_string('generate_new_wpmoodlelte', 'enrol_wpmoodlelte'));
        }
        else{
            $mform->addElement('submit', 'wpmoodlelteform_submit', get_string('generate_wpmoodlelte', 'enrol_wpmoodlelte'));
        }
    }
    function validation($data, $files) {
        return array();
    }
}
