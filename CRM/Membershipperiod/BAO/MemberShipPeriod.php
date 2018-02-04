<?php
require_once 'MemberShipPeriodHelper.php';

use CRM_Membershipperiod_ExtensionUtil as E;
use CRM\Membershipperiod\BAO\MemberShipPeriodHelper;

class CRM_Membershipperiod_BAO_MemberShipPeriod extends CRM_Membershipperiod_DAO_MemberShipPeriod {

  /**
   * Create a new MemberShipPeriod based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Membershipperiod_DAO_MemberShipPeriod|NULL
   */
  public static function create($params) {
    $className = 'CRM_Membershipperiod_DAO_MemberShipPeriod';
    $entityName = 'MemberShipPeriod';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  } 

  /**
   * Add new or update membership period as well manage contribution for new or renew membership period.
   * 
   * @param type $op
   * @param type $objectName
   * @param type $objectId
   * @param type $objectRef
   * @return type
   * @throws Exception
   */
  public static function manageMembershipPeriod($op, $objectName, $objectId, &$objectRef)
  {
      try {
            $membershipId = ($objectName == "Membership") ? $objectId : $objectRef->membership_id;
            $membershipDuration = MemberShipPeriodHelper::getMembershipDurationDetails($membershipId);
            
            if ($membershipDuration && $membershipDuration["membership_type_id.duration_unit"] == "lifetime") {
                return;
            }

            $params = [];
            // Create/ Edit membership period if Membership object is called
            if ($objectName == "Membership") {
                $params = self::validateMembershipData($objectRef, $membershipId, $op);
            }
            // Update membership period with contribution update if MembershipPayment object is called
            else if ($objectName == "MembershipPayment") {
                $params = [
                    "id"                => MemberShipPeriodHelper::getLatestMembershipPeriodId($objectRef, $membershipId),
                    "contribution_id"   => $objectRef->contribution_id
                ];
                
            }

           return self::create($params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 400);
        }
    }
    
    /**
     * This method will process and validate membership data to add/update membership period
     * 
     * @param type $objectRef
     * @param type $objectId
     * @param type $op
     * @return type
     * @throws Exception
     */
    private static function validateMembershipData($objectRef, $objectId, $op)
    {
        $startDate = new DateTime($objectRef->start_date);
        $endDate = new DateTime($objectRef->end_date);
        $dateDifference = $startDate->diff($endDate);

        if ($dateDifference->format('%R%a') < 0) {
            throw new Exception(ts("End date must be the greater or equal than start date."));
            return;
        }

        //Call this method to get start and end date of membership period if it renew.        
        $id = ($op == "create") ? null : MemberShipPeriodHelper::getLatestMembershipPeriodId($objectRef, $objectId, true);
        
        $params = [
            "membership_id"     => $objectId,
            "id"                => $id,
            "start_date"        => $objectRef->start_date,
            "end_date"          => $objectRef->end_date,            
        ];
        return $params;
    }

  /**
   * Retrive all the membership periods by membership id.
   * 
   * @param type $membershipId
   * @param type $isRefactor
   * @return array
   */  
  public static function getMembershipPeriods($membershipId, $isRefactor = true)
  {
      $membershipPeriods = civicrm_api3("MemberShipPeriod",'get',array(
        "membership_id"=> $membershipId,
        "sequential" => 1,
        "return" => array("start_date","end_date", "membership_id.contact_id","contribution_id","contribution_id.total_amount","contribution_id.currency"),
        "options" => array(
          "sort" => "id DESC"
        )
    ));
    
      //Return empty array if don't have any membership period.
    if($membershipPeriods["count"]== 0){
        return [];
    }
    
    //Refactor the original data get by API.    
    if($isRefactor){
    
        return self::refactorMembershipPeriodData($membershipPeriods["values"]);
    }
    
    return $membershipPeriods;
  }

  /**
   * This method will 
   * 
   * @param array $membershipPeriods
   * @return type
   */
  private static function refactorMembershipPeriodData(array $membershipPeriods)
  {
      foreach($membershipPeriods as $index => $membershipPeriod) 
      {        
        //Check if the membership period have any contribution.
        if(isset($membershipPeriod['contribution_id']))
        {  
            $contributionUrl = CRM_Utils_System::url("civicrm/contact/view/contribution",
                'reset=1&action=view&cid=' . $membershipPeriod['membership_id.contact_id'] . '&id=' . $membershipPeriod['contribution_id']
            );
            $membershipPeriods[$index]["contribution_url"] = $contributionUrl;
            $membershipPeriods[$index]["total_contribution_amount"] = $membershipPeriod["contribution_id.total_amount"];
            $membershipPeriods[$index]["contribution_currency"] = $membershipPeriod["contribution_id.currency"];
        }
    }
    
    return $membershipPeriods;
  }
  
  /**
   * Get the count of the periods for membership
   *
   * @param $contactId
   *
   * @return array|bool
   */
  public static function getMembershipPeriodCount($contactId) {
    // Get count of all period for membership
    return;
  }
}
