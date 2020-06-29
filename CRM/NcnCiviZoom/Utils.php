<?php

/**
 *  NcnCiviZoom utils functions
 *
 * @package CiviCRM
 */
class CRM_NcnCiviZoom_Utils {

	//Function to retrieve zoom settings
	public static function getZoomSettings(){
		$settings = CRM_Core_BAO_Setting::getItem(ZOOM_SETTINGS, 'zoom_settings');
		return $settings;
	}

	public static function getCustomField(){
		$settings = self::getZoomSettings();
		$customId = CRM_Utils_Array::value('custom_field_id', $settings, NULL);
		$customField = (!empty($customId))? 'custom_'.$customId : NULL;
		return $customField;
	}

	public static function getMeetingCustomField(){
		$settings = self::getZoomSettings();
		$customId = CRM_Utils_Array::value('custom_field_id_meeting', $settings, NULL);
		$customField = (!empty($customId))? 'custom_'.$customId : NULL;
		return $customField;
	}
}
