<?php
use CRM_Membershipperiod_ExtensionUtil as E;

class CRM_Membershipperiod_Page_MemberShipPeriod extends CRM_Core_Page {

  public function run() {
    
    // Get the membership id.
    $this->id = CRM_Utils_Request::retrieve('id', 'Positive', $this);
    $this->id = $this->id ? $this->id : 0;
    
    // Get the relevant entities
    try {
      $this->membership = civicrm_api3('Membership', 'getsingle', ['id' => $this->id,
          "return" => array("contact_id.display_name"),]);
    }
    catch (Exception $e) {
      // If we can't find a membership with id, redirect to the membership view
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/contact/view/membership'));
    }
    //Set dynamically title
    CRM_Utils_System::setTitle(ts('Membership Period of %1', [1 => $this->membership['contact_id.display_name']]));
    
    //Call the BAO method to fetch all the membershipperiods of current membership.
    $this->membership_periods = CRM_Membershipperiod_BAO_MemberShipPeriod::getMembershipPeriods($this->id);
    
    $this->assign('id', $this->id);
    $this->assign('membershipperiods', $this->membership_periods);

    parent::run();
  }

}
