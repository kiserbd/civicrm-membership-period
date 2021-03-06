<?php

require_once 'membershipperiod.civix.php';
use CRM_Membershipperiod_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function membershipperiod_civicrm_config(&$config) {      
    _membershipperiod_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function membershipperiod_civicrm_xmlMenu(&$files) {
  _membershipperiod_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function membershipperiod_civicrm_install() {
  _membershipperiod_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function membershipperiod_civicrm_postInstall() {
  _membershipperiod_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function membershipperiod_civicrm_uninstall() {
  _membershipperiod_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function membershipperiod_civicrm_enable() {
  _membershipperiod_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function membershipperiod_civicrm_disable() {
  _membershipperiod_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function membershipperiod_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _membershipperiod_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function membershipperiod_civicrm_managed(&$entities) {
  _membershipperiod_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function membershipperiod_civicrm_caseTypes(&$caseTypes) {
  _membershipperiod_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function membershipperiod_civicrm_angularModules(&$angularModules) {
  _membershipperiod_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function membershipperiod_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _membershipperiod_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function membershipperiod_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_entityTypes.
 */
function membershipperiod_civicrm_entityTypes(&$entityTypes) {
	$entityTypes['CRM_Membershipperiod_DAO_MemberShipPeriod'] = array(
		'name'  => 'MemberShipPeriod',
		'class' => 'CRM_Membershipperiod_DAO_MemberShipPeriod',
		'table' => 'civicrm_membership_period',
	);
}

/**
  * Implements hook_civicrm_links().
  * Implementing this to add a custom link to view membership periods for each membership of a contact.
  */

function membershipperiod_civicrm_links($op, $objectName, $objectId, &$links, &$mask, &$values) {
    if($op == 'membership.tab.row' || $op == 'membership.selector.row') {
        $links[] = [
           'name' => ts('Membership Periods'),
           'url' => 'civicrm/contact/view/membership-period',
           'qs' => 'id=%%id%%',
           'title' => 'MembershipPeriod',
         ];
    }
}

/**
 * Implement the civicrm_post hook.
 * @param type $op
 * @param type $objectName
 * @param type $objectId
 * @param type $objectRef
 * @return type
 * @throws Exception
 */
function membershipperiod_civicrm_post($op, $objectName, $objectId, &$objectRef)
{
    try {
        if ($objectName != 'Membership' && $objectName != 'MembershipPayment' || $op == 'delete') {
            return;
        }
        
        // Call BAO method to create/update MembershipPeriod 
        CRM_Membershipperiod_BAO_MemberShipPeriod::manageMembershipPeriod($op, $objectName, $objectId, $objectRef);        
        
    } catch (Exception $e) {
        throw new Exception($e->getMessage(), 400);
    }
}
