<?php

use Firebase\JWT\JWT;
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

	  $meeting = $this->getMeetingID($event['id']);

	  if(!empty($meeting)){
	  	$this->addParticipant($participant, $meeting, $triggerData, 'Meeting');
	  } elseif (!empty($webinar)) {
	  	$this->addParticipant($participant, $webinar, $triggerData, 'Webminar');
	  }
	}

	/**
	 * Get an event's webinar id
	 * @param  int $event The event's id
	 * @return string The event's webinar id
	 */
	private function getWebinarID($event) {
		$result;
		$customField = CRM_NcnCiviZoom_Utils::getCustomField();
		try {
			$apiResult = civicrm_api3('Event', 'get', [
			  'sequential' => 1,
			  'return' => [$customField],
			  'id' => $event,
			]);
			// Remove any empty spaces
			$result = trim($apiResult['values'][0][$customField]);
			$result = str_replace(' ', '', $result);
		} catch (Exception $e) {
			throw $e;
		}

		return $result;
	}

	/**
	 * Get an event's Meeting id
	 * @param  int $event The event's id
	 * @return string The event's Meeting id
	 */
	private function getMeetingID($event) {
		$result;
		$customField = CRM_NcnCiviZoom_Utils::getMeetingCustomField();
		try {
			$apiResult = civicrm_api3('Event', 'get', [
			  'sequential' => 1,
			  'return' => [$customField],
			  'id' => $event,
			]);
			// Remove any empty spaces
			$result = trim($apiResult['values'][0][$customField]);
			$result = str_replace(' ', '', $result);
		} catch (Exception $e) {
			throw $e;
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
			  'id' => $id,
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

	/**
	 * Add's the given participant data as a single participant
	 * to a Zoom Webinar/Meeting with the given id.
	 *
	 * @param array $participant participant data where email, first_name, and last_name are required
	 * @param int $entityID id of an existing Zoom webinar/meeting
	 * @param string $entity 'Meeting' or 'Webminar'
	 */
	private function addParticipant($participant, $entityID, $triggerData, $entity) {
		$settings = CRM_NcnCiviZoom_Utils::getZoomSettings();
		if($entity == 'Webminar'){
			$url = $settings['base_url'] . "/webinars/$entityID/registrants";
		} elseif($entity == 'Meeting'){
			$url = $settings['base_url'] . "/meetings/$entityID/registrants";
		}
		$token = $this->createJWTToken();

		$response = Zttp::withHeaders([
			'Content-Type' => 'application/json;charset=UTF-8',
			'Authorization' => "Bearer $token"
		])->post($url, $participant);

		// Alert to user on success.
		if ($response->isOk()) {
			$firstName = $participant['first_name'];
			$lastName = $participant['last_name'];
			$msg = 'Participant Added to Zoom. $entity ID: '.$entityID;
			$this->logAction($msg, $triggerData, \PSR\Log\LogLevel::INFO);

			CRM_Core_Session::setStatus(
				"$firstName $lastName was added to Zoom $entity $entityID.",
				ts('Participant added!'),
				'success'
			);
		} else {
			$result = $response->json();
			$msg = $result['message'].' $entity ID: '.$entityID;
			$this->logAction($msg, $triggerData, \PSR\Log\LogLevel::ALERT);
		}
	}

	private function createJWTToken() {
		$settings = CRM_NcnCiviZoom_Utils::getZoomSettings();
		$key = $settings['secret_key'];
		$payload = array(
		    "iss" => $settings['api_key'],
		    "exp" => strtotime('+1 hour')
		);
		$jwt = JWT::encode($payload, $key);

		return $jwt;
	}

	public function getJoinUrl($object){
		$eventId = $object->event_id;
		$settings = CRM_NcnCiviZoom_Utils::getZoomSettings();
		$webinar = $object->getWebinarID($eventId);
		$meeting = $object->getMeetingID($eventId);
		$url = '';
	  if(!empty($meeting)){
	  	$url = $settings['base_url'] . "/meetings/".$meeting;
	  } elseif (!empty($webinar)) {
	  	$url = $settings['base_url'] . "/webinars/".$webinar;
	  } else {
	  	return null;
	  }
	  $token = $object->createJWTToken();
		$response = Zttp::withHeaders([
			'Content-Type' => 'application/json;charset=UTF-8',
			'Authorization' => "Bearer $token"
		])->get($url);
		$result = $response->json();
		$joinUrl = $result['join_url'];
		return empty($joinUrl)? NULL : $joinUrl;
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