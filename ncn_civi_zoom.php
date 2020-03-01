<?php

require_once 'ncn_civi_zoom.civix.php';
require_once __DIR__.'/vendor/autoload.php';
use CRM_NcnCiviZoom_ExtensionUtil as E;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Dotenv\Dotenv;

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 
$configuration = Configuration::forAsymmetricSigner(
    // You may use RSA or ECDSA and all their variations (256, 384, and 512)
    new Signer\RSA\Sha256(),
    new Key($_ENV['ZOOM_API_SECRET']),
    new Key($_ENV['ZOOM_API_KEY'])
);

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/ 
 */
function ncn_civi_zoom_civicrm_config(&$config) {
  _ncn_civi_zoom_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function ncn_civi_zoom_civicrm_xmlMenu(&$files) {
  _ncn_civi_zoom_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function ncn_civi_zoom_civicrm_install() {
  _ncn_civi_zoom_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function ncn_civi_zoom_civicrm_postInstall() {
  _ncn_civi_zoom_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function ncn_civi_zoom_civicrm_uninstall() {
  _ncn_civi_zoom_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function ncn_civi_zoom_civicrm_enable() {
  _ncn_civi_zoom_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function ncn_civi_zoom_civicrm_disable() {
  _ncn_civi_zoom_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function ncn_civi_zoom_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  CRM_Civirules_Utils_Upgrader::insertActionsFromJson($this->extensionDir . DIRECTORY_SEPARATOR . 'civirules_actions.json');

  return _ncn_civi_zoom_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function ncn_civi_zoom_civicrm_managed(&$entities) {
  _ncn_civi_zoom_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function ncn_civi_zoom_civicrm_caseTypes(&$caseTypes) {
  _ncn_civi_zoom_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function ncn_civi_zoom_civicrm_angularModules(&$angularModules) {
  _ncn_civi_zoom_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function ncn_civi_zoom_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _ncn_civi_zoom_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function ncn_civi_zoom_civicrm_entityTypes(&$entityTypes) {
  _ncn_civi_zoom_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function ncn_civi_zoom_civicrm_themes(&$themes) {
  _ncn_civi_zoom_civix_civicrm_themes($themes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *
function ncn_civi_zoom_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 *
function ncn_civi_zoom_civicrm_navigationMenu(&$menu) {
  _ncn_civi_zoom_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _ncn_civi_zoom_civix_navigationMenu($menu);
} // */
