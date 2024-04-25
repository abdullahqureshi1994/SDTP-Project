<?php

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');

require_login();

$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/enrol/wpmoodlelte/process_request.php'));
$PAGE->set_title(get_string('wpmoodlelteform_pagetitle', 'enrol_wpmoodlelte'));
$PAGE->set_heading(get_string('wpmoodlelteform_heading', 'enrol_wpmoodlelte'));


$url = 'https://testphp8.lums.edu.pk/bankgateway/lumsx/api/wpmoodleltes?ids=1222202211432,1222200000041';

$curl = curl_init();

$request = array(
    'name' => $_POST["wpmoodlelteform_student_full_name"],
    'email' => $_POST["wpmoodlelteform_student_email"],
    'mobile' => $_POST["wpmoodlelteform_student_contact_no"],
    'total_amount' => $_POST["wpmoodlelteform_amount"],
    'due_date' => "2021-01-10 17:50:00",
    'order_id' => $_POST["wpmoodlelteform_order_id"],
    'reserved' => $_POST["wpmoodlelteform_reserved"],
    'items' => [
        [
            "title" => $_POST['wpmoodlelteform_course_title'],
            "quantity" => (int)"1",
            'amount' => $_POST["wpmoodlelteform_amount"]
        ]
    ]
);
$json_string = json_encode($request);

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization: Bearer a66a6cbaa0fb0f589ea9170098c20a3fe3d64460add0bf00e43d191aba9ed0ff'));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );

$data = curl_exec($curl);

curl_close($curl);

dd($data ,$json_string, json_decode($data) , $_POST );

/*$mform = new wpmoodlelteformnew();
$mform->get_data();*/

//dd($_POST);


/*$url = 'localhost:8080/snacks';
$asdata = [
        'asid'=> 2,
        'userid' => 3,
        'sbm_fpth' => 5
    ];
$builtquery = http_build_query($asdata);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $builtquery);

$result = curl_exec($ch);
echo $result;

*/
/*$url = 'https://testphp8.lums.edu.pk/bankgateway/lumsx/api';
$params = array();


$header = array();
$header[] = 'Accept: application/json';
$header[] = 'Content-type: application/json';
$header[] = 'Authorization: Bearer b55a6cbaa0fb0f589ea9170098c20a3fe3d64460add0bf00e43d191aba9ed0ab';

curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
curl_setopt($curl, CURLOPT_URL, $url);

$result = curl_exec($curl);

//$result = $curl->get($url.'/wpmoodleltes/1222202211432', $params);
print_object($result);

dd($result);*/
