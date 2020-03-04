<?php
use CRM_Ncnciviapi_ExtensionUtil as E;


/**
 * Contact.FindOrCreate API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_participant_createfromzoom_spec(&$spec) {
  $spec['contact_type']['api.required'] = 1;
  $spec['first_name']['api.required'] = 1;
  $spec['last_name']['api.required'] = 1;
  $spec['email']['api.required'] = 1;
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

	// Find contact using given $params.
	$response = civicrm_api3_contact_get($params);

	// No results? Create the contact.
	if(empty($response['values'])) 
		$response = civicrm_api3_contact_create($params);

	return [
		'values' => [
			$response['id'] => [
				'id' => $response['id'],
			]
		]
	];
}
