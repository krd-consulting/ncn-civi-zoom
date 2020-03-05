<?php
use CRM_Ncnciviapi_ExtensionUtil as E;

use Firebase\JWT\JWT;
use Zttp\Zttp;


/**
 * Contact.FindOrCreate API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_participant_generatewebinarattendance_spec(&$spec) {
	$spec['verification_token']['api.required'] = 1;
}

/**
 * Contact.FindOrCreate API
 *
 * Queries for an individual using first_name, last_name, and email and creates a contact if
 * no contacts are returned. 
 *
 * @param array $params
 *
 * @return array
 *   Array containing data of found or newly created contact.
 *
 * @see civicrm_api3_create_success
 *
 */
function civicrm_api3_event_generatewebinarattendance($params) {
	$verification_token = "H1O4Lg6dQ0Oq3__Xms1qUw";

	$key = $_ENV['ZOOM_API_SECRET'];
	$payload = array(
	    "iss" => $_ENV['ZOOM_API_KEY'],
	    "exp" => strtotime('+1 hour')
	);
	$jwt = JWT::encode($payload, $key);

	// Get request body
	$data = json_decode(file_get_contents('php://input'), true);
	$webinar = $data['payload']['object']['id'];

	// Get event of webinar
	$event = civicrm_api3('Event', 'get', [
	  'sequential' => 1,
	  'custom_48' => $webinar,
	  'limit' => 1
	])['values'][0]['id'];

	$page = 1;

	// Get and loop through all of webinar registrants
	$url = $_ENV['ZOOM_BASE_URL'] . "/past_webinars/$webinar/absentees?page=$page";
	$token = $jwt;

	// Get absentees from Zoom API
	$response = Zttp::withHeaders([
		'Content-Type' => 'application/json;charset=UTF-8',
		'Authorization' => "Bearer $token"
	])->get($url);

	$pages = $response->json()['page_number'];
	$page++;

	// Store registrants who did not attend the webinar
	$absentees = $response->json()['registrants'];

	$absenteesEmails = [];

	foreach($absentees as $absentee) {
		array_push($absenteesEmails, $absentee['email']);
	}

	$attendees = selectAttendees($absenteesEmails);

	// while($page <= $pages) {
	// 	for($absentee in $absentees) {
	// 		array_push($absenteesEmails, $absentee['email']);
	// 	}



	// 	$page++;
	// }

	return [
		'values' => [
			$attendees
		]
	];
}

function selectAttendees($absenteesEmails) {
	$selectAttendees = <<<SQL
		SELECT * FROM `civicrm_participant` 
		LEFT JOIN `civicrm_email` ON `civicrm_participant`.`contact_id` = `civicrm_email`.`contact_id`
		WHERE 
			`civicrm_email`.`email` NOT IN ($absenteesEmails) AND
	    	`civicrm_participant`.`event_id` = 5
SQL;

	// Run query
	$query = CRM_Core_DAO::executeQuery($selectAttendees);

	$attendees = [];

	while($query->fetch()) {
		array_push($attendees, $query->email);
	}

	return $attendees;
}
