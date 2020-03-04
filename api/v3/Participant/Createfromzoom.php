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
function _civicrm_api3_participant_createfromzoom_spec(&$spec) {
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
function civicrm_api3_participant_createfromzoom($params) {

	$key = $_ENV['ZOOM_API_SECRET'];
	$payload = array(
	    "iss" => $_ENV['ZOOM_API_KEY'],
	    "exp" => strtotime('+1 hour')
	);
	$jwt = JWT::encode($payload, $key);

	return [
		'values' => [
			$_POST
		]
	];
}
