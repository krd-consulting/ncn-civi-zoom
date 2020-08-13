<?php

use Firebase\JWT\JWT;
use Zttp\Zttp;

use CRM_NcnCiviZoom_ExtensionUtil as E;

/**
 * This class contains all the function that are called using AJAX (jQuery)
 */
class CRM_NcnCiviZoom_Page_AJAX {

	public static function checkEventWithZoom(){

		if(empty($_POST) || empty($_POST["account_id"])
			|| empty($_POST["entityID"])
			|| empty($_POST["entity"])){
			$result = ['status' => null , 'message' => "Parameters missing"];
			CRM_Utils_JSON::output($result);
		}
		$result = CRM_CivirulesActions_Participant_AddToZoom::checkEventWithZoom($_POST);
		if(!empty($result)){
			CRM_Utils_JSON::output($result);
		}else{
			$result = ['status' => null, 'message' => 'Error occured please try again'];
			CRM_Utils_JSON::output($result);
		}
	}

}
