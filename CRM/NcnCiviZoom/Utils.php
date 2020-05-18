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
		if(!empty($settings['']))
		return['custom_field_id'];
	}
}
