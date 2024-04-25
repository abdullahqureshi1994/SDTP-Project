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


$string['pluginname'] = 'wpmoodlelte Enrollments';
$string['pluginname_desc'] = 'Allows wpmoodlelte enrollment via a wpmoodlelte form.';


$string['wpmoodlelteform_student_full_name'] = 'Full Name';
$string['wpmoodlelteform_student_email'] = 'Email';
$string['generate_wpmoodlelte'] = 'Generate wpmoodlelte';
$string['generate_new_wpmoodlelte'] = 'Generate New wpmoodlelte';
$string['wpmoodlelteform_heading'] = 'Send wpmoodlelte Request';
$string['wpmoodlelteform_pagetitle'] = 'wpmoodlelte Request';

$string['wpmoodlelteform_student_contact_no'] = 'Contact No.';
$string['wpmoodlelteform_course_title'] = 'Course Title';
$string['wpmoodlelteform_amount'] = 'Amount';
$string['wpmoodlelte_user_enrol'] = 'wpmoodlelte User Enrol';
$string['course_price'] = 'Course Price (Rs:)';

$string['enrolinstancedefaults_desc'] = 'Default enrolment settings in new courses.';
$string['enrolinstancedefaults'] = 'Add instance to new courses';
$string['defaultenrol_desc'] = 'It is possible to add this plugin to all new courses by default.';
$string['defaultenrol'] = 'Add instance to new courses';




$string['enrolform_course_code'] = 'Enrollment Code';
$string['enrolform_submit'] = 'Enroll';
$string['enrolform_heading'] = 'Enroll in a Course';
$string['enrolform_pagetitle'] = 'Enroll in a Course';

$string['header_coursecodes'] = 'Enrollment codes';

$string['status'] = 'Enabled';
$string['status_help'] = 'Set to "Yes" to enabled enrollment. Set to "No" to disable enrollment.';
$string['enrolstartdate'] = 'Enrollment Begins';
$string['enrolstartdate_help'] = 'Students will be unable to enroll prior to this date.';

$string['enrolenddate'] = 'Enrollment Ends';
$string['enrolenddate_help'] = 'Students will be unable to enroll after this date.';

$string['regenerate_codes'] = 'Regenerate Codes';
$string['regenerate_codes_help'] = 'Check this and click "Save changes" to re-create all above enrollment codes.';

$string['qrenabled'] = 'Enable Enrol via QR Codes';
$string['qrenableddesc'] = 'Enable Enrol via QR Codes';

$string['showqronmobile'] = 'Enable QR Code Reader on Mobile';
$string['showqronmobiledesc'] = 'Enable Enrol via QR Codes on mobile devices. May not work on all mobile browsers.  Preferred use of QR codes is in the Chrome browser and on a desktop, laptop, or Chromebook.';

$string['easy:unenrolself'] = 'Unenroll from course';
$string['easy:config'] = 'Configure Easy Enrollment instances';
$string['easy:delete'] = 'Delete Easy Enrollment instances';
$string['easy:manage'] = 'Manage Easy Enrollment instances';
$string['easy:unenrol'] = 'Unenrol from Easy Enrollment instances';
$string['unenrolselfconfirm'] = 'Do you really want to unenrol yourself from course "{$a}"?';

$string['error_disabled_global'] = 'Easy enrollment is disabled site-wide.';
$string['error_disabled_global'] = 'Easy enrollment is disabled for this course.';
$string['error_enrolstartdate'] = 'Enrollment has not begin for this course yet.';
$string['error_enrolenddate'] = 'Enrollment for this course has ended.';
$string['error_invalid_code'] = 'Invalid enrollment code.';

$string['coursetext'] = 'Course:  ';
$string['grouptext'] = 'Group:  ';
$string['welcometocourse'] = '{$a}';
// $string['welcometocoursetext'] = 'Welcome to {$a->coursename}!';
$string['welcometocoursetext'] = '<p style="margin-block-start:0em;" >Dear {$a->userfirstname},</p><p>We hope this message finds you in great spirits.</p><p>Congratulations on successfully enrolling in the <a target="_blank" href="{$a->courselink}" ><strong>{$a->coursename}</strong></a> course at LUMSx! We are delighted to welcome you to this transformative learning experience.</p><p>As a valued participant, we are pleased to inform you that your access to our Learning Management System (LMS) is now active. You can log in using the credentials you provided during the enrollment process. The LMS will be your central hub for all course-related materials, discussions, and updates.</p><p>We will be sharing the course materials with you one week prior. If you encounter any issues with your LMS access or have any questions, please do not hesitate to reach out to us at <a href="mailto:m_qureshi@lums.edu.pk">m_qureshi@lums.edu.pk</a>.</p><p>Thank you once again for choosing LUMSx for your professional development journey. We are excited to embark on this learning adventure with you.</p><p>Warm regards,</p><p><strong>LUMSx Team</strong></p><p><a href="https://lumsx.lums.edu.pk">lumsx.lums.edu.pk</a></p>';
$string['wpmoodleltenewemail']= '<div> <p>Dear {$a->userfirstname},</p> <p>Thank you for your interest in <strong>{$a->course}</strong>. To secure your spot in this course please complete your registration by following the steps below:</p><ol> <li><strong>Download your wpmoodlelte</strong> </li> <li><strong>Pay Online:</strong> Opt for this option for instant payment processing. It\'s quick, convenient, and hassle-free. <a target="_blank" href="{$a->payonline}">Click here to Pay Online</a></li> <li><strong>Pay via Bank:</strong> If you prefer to make a bank deposit, please find attached a challan file. Simply download it and proceed with the bank payment for seamless processing.</li> </ol> <p>After you have made your payment, you will receive an email with your enrolment credentials, use these credentials to log in to our portal & access course materials.</p> <p>If you have any questions, please don\'t hesitate to reach out to us. You can contact us at <a href="mailto:lumsxonline@gmail.com">lumsxonline@gmail.com</a> or at <a href="tel:0321-0667775">0321-0667775</a>. Our team is available from 9 am to 3 pm, Monday to Friday (excluding national holidays).</p> <p>We look forward to welcoming you on board!</p> <p>Best regards,<br> <strong>LUMSx Team</strong></p></div>';