<?php
use CRM_Membershipperiod_ExtensionUtil as E;

/**
 * MemberShipPeriod.create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
//function _civicrm_api3_member_ship_period_create_spec(&$spec) {
//   $spec['membership_id']['api.required'] = 1;
//   $spec['start_date']['api.required'] = 1;
//   $spec['end_date']['api.required'] = 1;
//}

/**
 * MemberShipPeriod.create API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_member_ship_period_create($params) {
  return _civicrm_api3_basic_create(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}

/**
 * MemberShipPeriod.delete API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_member_ship_period_delete($params) {
  return _civicrm_api3_basic_delete(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}

/**
 * MemberShipPeriod.get API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_member_ship_period_get($params) {
  return _civicrm_api3_basic_get(_civicrm_api3_get_BAO(__FUNCTION__), $params);
}

function _civicrm_api3_member_ship_period_get_spec(&$spec) {
   $spec['membership_id']['api.required'] = 1;
   $spec['start_date']['api.required'] = 0;
   $spec['end_date']['api.required'] = 0;
}