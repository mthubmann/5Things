<?php


// Takes raw data from the request
$json = file_get_contents('php://input');
//echo file_get_contents('php://input');
// Converts it into a PHP object
$data = json_decode($json);
//echo $data->pw;

$return = new stdClass();
switch ($data->req){
	case "login":
		$return->response = "Request recieved";
		$return->text = "status: 400";
	break;
	default:
};
//$return = $data;

//$return = ["test","1234"];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($return);

?>