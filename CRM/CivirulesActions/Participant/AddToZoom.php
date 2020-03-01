<?php

use Zttp\Zttp;

class CRM_CivirulesActions_Participant_AddToZoom extends CRM_Civirules_Action{

	/**
	 * Method processAction to execute the action
	 *
	 * @param CRM_Civirules_TriggerData_TriggerData $triggerData
	 * @access public
	 *
	 */
	public function processAction(CRM_Civirules_TriggerData_TriggerData $triggerData) {
	  $contactId = $triggerData->getContactId();
	  
	  $event = $triggerData->getEntityData('Event');

	  $webinar = $this->getWebinarID($event['id']);

	  $participant = $this->getContactData($contactId);

	  $this->addParticipant($participant, $webinar);
	}

	/**
	 * Get an event's webinar id
	 * @param  int $event The event's id
	 * @return string The event's webinar id
	 */
	private function getWebinarID($event) {
		$result;

		try {
			$result = civicrm_api3('Event', 'get', [
			  'sequential' => 1,
			  'return' => ["custom_48"],
			  'id' => $event,
			])['values']['custom_48'];
		} catch (Exception $e) {

		}

		return $result;
	}

	/**
	 * Get given contact's email, first_name, last_name,
	 * city, state/province, country, post code
	 *
	 * @param int $id An existing CiviCRM contact id
	 *
	 * @return array Retrieved contact info
	 */
	private function getContactData($id) {
		$result = [];

		try {
			$result = civicrm_api3('Contact', 'get', [
			  'sequential' => 1,
			  'return' => ["email", "first_name", "last_name", "street_address", "city", "state_province_name", "country", "postal_code"],
			  'id' => "",
			])['values'][0];
		} catch (Exception $e) {
			watchdog(
			  'NCN-Civi-Zoom CiviRules Action (AddToZoom)',
			  'Something went wrong with getting contact data.',
			  array(),
			  WATCHDOG_INFO
			);
		}

		return $result;
	}

	private function addParticipant($participant, $webinar) {
		$url = $_ENV['ZOOM_BASE_URL'] . "/webinars/$webinar/registrants";

		watchdog(
		  'NCN-Civi-Zoom CiviRules Action (AddToZoom)',
		  'participant: @participant url: @url',
		  array(
		  	'@participant' => print_r($participant, TRUE),
		  	'url' => $url
		  ),
		  WATCHDOG_INFO
		);

		$response = Zttp::withHeaders([
			'Content-Type' => 'application/json;charset=UTF-8',
			'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhdWQiOm51bGwsImlzcyI6IkxST1lFdEQ0UTJDRGE0RlV0N0p2LUEiLCJleHAiOjE1ODM2MzA1OTgsImlhdCI6MTU4MzAyNTgxNH0.yPLF7Vo3j0HyC_Q759nXHzKbbptsVknu71vr1Ox7u4s'
		])->post($url, $participant);

		watchdog(
			  'NCN-Civi-Zoom CiviRules Action (AddToZoom)',
			  'Zoom Response: @response',
			  array(
			  	'@response' => $response->isOk()
			  ),
			  WATCHDOG_INFO
			);
	}

	private function createJWTToken() {

	}

	/**
	 * Method to return the url for additional form processing for action
	 * and return false if none is needed
	 *
	 * @param int $ruleActionId
	 * @return bool
	 * @access public
	 */
	public function getExtraDataInputUrl($ruleActionId) {
  	return FALSE;
	}

}