<?php
use CRM_Ncnciviapi_ExtensionUtil as E;

use Firebase\JWT\JWT;
use Zttp\Zttp;


/**
 * Participant.GenerateWebinarAttendance specification
 * 
 * Makes sure that the verification token is provided as a parameter
 * in the request to make sure that request is from a reliable source.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_participant_generatewebinarattendance_spec(&$spec) {
	$spec['verification_token']['api.required'] = 1;
}

/**
 * Participant.GenerateWebinarAttendance API
 *
 * Designed to be called by a Zoom Event Subscription (event: webinar.ended).
 * Once invoked, it gets the absent registrants from the webinar that just ended.
 * 
 * Then, it gets the event associated with the webinar, as well as, the
 * registered participants of the event.
 *
 * Absent registrants are then subtracted from registered participants and,
 * the remaining participants' statuses are set to Attended.
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
	$verification_token = $_ENV['ZOOM_VERIFICATION_TOKEN'];

	if($verification_token != $params['verification_token']) {
		throw new \Civi\API\Exception\UnauthorizedException('Invalid verification token.');
	}


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

	$token = $jwt;

	$page = 0;
	// Get and loop through all of webinar registrants
	$url = $_ENV['ZOOM_BASE_URL'] . "/past_webinars/$webinar/absentees?page=$page";

	// Get absentees from Zoom API
	$response = Zttp::withHeaders([
		'Content-Type' => 'application/json;charset=UTF-8',
		'Authorization' => "Bearer $token"
	])->get($url);

	$pages = $response->json()['page_count'];

	// Store registrants who did not attend the webinar
	$absentees = $response->json()['registrants'];

	$absenteesEmails = [];

	$attendees = [];

	while($page < $pages) {
		foreach($absentees as $absentee) {
			$email = $absentee['email'];

			array_push($absenteesEmails, "'$email'");
		}

		$attendees = array_merge($attendees, selectAttendees($absenteesEmails, $event));

		$page++;

		// Get and loop through all of webinar registrants
		$url = $_ENV['ZOOM_BASE_URL'] . "/past_webinars/$webinar/absentees?page=$page";

		// Get absentees from Zoom API
		$response = Zttp::withHeaders([
			'Content-Type' => 'application/json;charset=UTF-8',
			'Authorization' => "Bearer $token"
		])->get($url);

		// Store registrants who did not attend the webinar
		$absentees = $response->json()['registrants'];

		$absenteesEmails = [];
	}

	updateAttendeesStatus($attendees, $event);

	return civicrm_api3_create_success($attendees, $params, 'Participant');
}

/**
 * Queries for the registered participants that weren't absent
 * during the webinar.
 * @param  array $absenteesEmails emails of registrants absent from the webinar
 * @param  int $event the id of the webinar's associated event
 * @return array participants (email, participant_id, contact_id) who weren't absent
 */
function selectAttendees($absenteesEmails, $event) {
	$absenteesEmails = implode(',', $absenteesEmails);

	$selectAttendees = <<<SQL
		SELECT 
			`civicrm_email`.`email`,
			`civicrm_participant`.`contact_id`,
			`civicrm_participant`.`id` AS `participant_id`
		FROM `civicrm_participant` 
		LEFT JOIN `civicrm_email` ON `civicrm_participant`.`contact_id` = `civicrm_email`.`contact_id`
		WHERE 
			`civicrm_email`.`email` NOT IN ($absenteesEmails) AND
	    	`civicrm_participant`.`event_id` = $event
SQL;

	// Run query
	$query = CRM_Core_DAO::executeQuery($selectAttendees);

	$attendees = [];

	while($query->fetch()) {
		array_push($attendees, [
			'email' => $query->email,
			'contact_id' => $query->contact_id,
			'participant_id' => $query->participant_id
		]);
	}

	return $attendees;
}

/**
 * Set the status of the registrants who weren't absent to Attended.
 * @param  array $attendees registrants who weren't absent
 * @param  int $event the event associated with the webinar
 * 
 */
function updateAttendeesStatus($attendees, $event) {
	foreach($attendees as $attendee) {
		civicrm_api3('Participant', 'create', [
		  'event_id' => $event,
		  'id' => $attendee['participant_id'],
		  'status_id' => "Attended",
		]);
	}
}
