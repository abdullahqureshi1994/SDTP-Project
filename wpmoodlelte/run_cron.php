<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/classes/task/wpmoodlelte_user_enrol.php');
require_login();

$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/enrol/wpmoodlelte/run_cron.php'));


$output = [];
//$output = &$output;
$result_code = 0;
//$result_code = &$result_code;

$command = "php ../../admin/cli/scheduled_task.php --execute=\\enrol_wpmoodlelte\\task\\wpmoodlelte_user_enrol";
//$command = "php ../../admin/cli/cron.php && pwd";
exec( $command, $output, $result_code);

echo implode( "\n",$output);

?>