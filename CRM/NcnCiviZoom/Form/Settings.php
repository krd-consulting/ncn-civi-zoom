<?php

use Firebase\JWT\JWT;
use Zttp\Zttp;

use CRM_NcnCiviZoom_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_NcnCiviZoom_Form_Settings extends CRM_Core_Form {
  public function buildQuickForm() {

    // add form elements
    $this->add('password', 'api_key', ts('Api Key'), array(
      'size' => 48,
    ), TRUE);
    $this->add('password', 'secret_key', ts('Secret Key'), array(
      'size' => 48,
    ), TRUE);
    $this->add('text', 'base_url', ts('Base Url'), array(
      'size' => 48,
    ), TRUE);
    $this->add(
      'select',
      'custom_field_id',
      'Custom Field',
      $this->getEventCustomFields(),
      TRUE,
      array('multiple' => FALSE)
    );
    $this->add(
      'select',
      'custom_field_id_meeting',
      'Custom Field For Meeting',
      $this->getEventCustomFields(),
      TRUE,
      array('multiple' => FALSE)
    );

    $buttons = [
      [
        'type' => 'submit',
        'name' => E::ts('Save'),
        'isDefault' => TRUE,
      ],
    ];

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());

    //Set default Values
    $defaults = CRM_NcnCiviZoom_Utils::getZoomSettings();
    if (!empty($defaults)) {
      $buttons[] = [
        'type' => 'upload',
        'name' => ts('Test Settings'),
        'subName' => 'done',
      ];
    }

    $this->addButtons($buttons);

    $this->setDefaults($defaults);
    parent::buildQuickForm();
  }

  /**
   * @return array
   */
  public static function getEventCustomFields() {
    $cFields = array('' => '- select -');
    $cGroupResult = civicrm_api3('CustomGroup', 'get', array(
      'sequential' => 1,
      'extends' => "Event",
      'options' => array('limit' => 0),
    ));

    if (empty($cGroupResult['values'])) {
      return $cFields;
    }

    foreach ($cGroupResult['values'] as $cgKey => $cgValue) {
      $cFieldResult = civicrm_api3('CustomField', 'get', array(
        'sequential' => 1,
        'custom_group_id' => $cgValue['id'],
        'options' => array('limit' => 0),
      ));

      if (!empty($cFieldResult['values'])) {
        foreach ($cFieldResult['values'] as $cfKey => $cfValue) {
          $cFields[$cfValue['id']] = $cfValue['label'];
        }
      }
    }

    return $cFields;
  }

  public function postProcess() {

    $buttonName = $this->controller->getButtonName();
    if ($buttonName == $this->getButtonName('upload', 'done')) {
      $result = self::testAPIConnectionSettings();
    } else {
      $values = $this->exportValues();
      $zoomSettings['api_key']      = $values['api_key'];
      $zoomSettings['secret_key']   = $values['secret_key'];
      $zoomSettings['base_url']     = $values['base_url'];
      $zoomSettings['custom_field_id'] = $values['custom_field_id'];
      $zoomSettings['custom_field_id_meeting'] = $values['custom_field_id_meeting'];
      CRM_Core_BAO_Setting::setItem($zoomSettings, ZOOM_SETTINGS, 'zoom_settings');
      $result['message'] = ts('Your Settings have been saved');
      $result['type'] = 'success';
    }

    CRM_Core_Session::setStatus($result['message'], ts('Zoom Settings'), $result['type']);
    $redirectUrl    = CRM_Utils_System::url('civicrm/Zoom/settings', 'reset=1');
    CRM_Utils_System::redirect($redirectUrl);
    parent::postProcess();
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = [];
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

  public static function testAPIConnectionSettings() {
    $settings = CRM_NcnCiviZoom_Utils::getZoomSettings();
    $url = $settings['base_url'] . "/report/daily";
    $token = self::createJWTToken();
    $params['year'] = date('Y');
    $params['month'] = date('m');
    $response = Zttp::withHeaders([
      'Content-Type' => 'application/json;charset=UTF-8',
      'Authorization' => "Bearer $token"
    ])->get($url, $params);
    // Alert to user on success.
    if ($response->isOk()) {
      $msg = "Connection settings are correct.";
      $type = 'success';
    } else {
      $result = $response->json();
      $msg = $result['message'];
      $type = 'alert';
    }

    $status['message'] = $msg;
    $status['type'] = $type;
    return $status;
  }

  public static function createJWTToken() {
    $settings = CRM_NcnCiviZoom_Utils::getZoomSettings();
    $key = $settings['secret_key'];
    $payload = array(
        "iss" => $settings['api_key'],
        "exp" => strtotime('+1 hour')
    );
    $jwt = JWT::encode($payload, $key);

    return $jwt;
  }

}
