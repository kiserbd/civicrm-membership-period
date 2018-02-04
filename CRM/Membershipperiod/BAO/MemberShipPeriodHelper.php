<?php
namespace CRM\Membershipperiod\BAO;

class MemberShipPeriodHelper 
{    
    /**
     * Get membership deration details
     *
     * @param $membershipId
     * @return array
     */
    public static function getMembershipDurationDetails($membershipId) {
        $membership = civicrm_api3('Membership','getsingle',array(
            'id'        => $membershipId,
            'return'    => [
                'membership_type_id.duration_interval',
                'membership_type_id.duration_unit'
            ],
        ));
        return $membership;
    }
   
  /**
     * This functions will retrieve the latest id of the membership period for a contact membership
     *
     * @param $objectRef
     * @param $id
     * @param bool $isRenewal
     * @return mixed
     */
    public static function getLatestMembershipPeriodId(&$objectRef, $membershipId, $isRenewal = false) {
        $membershipPeriod = civicrm_api3('MemberShipPeriod', 'get', [
            'membership_id' => $membershipId,
            'sequential' => 1,
            'limit' => 1,
            'sort' => 'id DESC'
        ]);
        if ($isRenewal && self::isRenewal($objectRef, $membershipId, $membershipPeriod)) {
            //Check if no such membership period, known case is, already exist membership before install the extension
            if(isset($membershipPeriod['values'][0])){
                $newStartDate = self::getDateInterval('day', '1', $membershipPeriod['values'][0]['end_date']);
                $objectRef->start_date =  date_format($newStartDate, 'Y-m-d');                
            }
            
            return;
        }

        return isset($membershipPeriod['values'][0]) ? $membershipPeriod['values'][0]['id']: null;
    }

    /**
     * Calculate the next date based on interval, unit and date
     *
     * @param $unit
     * @param $interval
     * @param $startDate
     * @return DateTime|false
     */
    public static function getDateInterval($unit, $interval, $startDate) {
        $interval = $interval . ' ' . $unit;
        $date = date_create($startDate);
        $newEndDate = date_add($date, date_interval_create_from_date_string($interval));
        return $newEndDate;
    }

    /**
     * Confirm if renewal or new record should be created
     * based on membership duration interval and unit
     *
     * @param $objectRef
     * @param $membershipId
     * @param $period
     * @return bool
     */
    public static function isRenewal($objectRef, $membershipId, $membershipPeriod) {
        $membership = self::getMembershipDurationDetails($membershipId);

        if (count($membershipPeriod['values']) == 0) {
            return false;
        }

        // get the last period
        $newEndDate = self::getDateInterval(
            $membership['membership_type_id.duration_unit'],
            $membership['membership_type_id.duration_interval'],
            $membershipPeriod['values'][0]['end_date']
        );
        $newEndDate = date_format($newEndDate, 'Y-m-d');
        if ($objectRef->end_date == $newEndDate) {
            return true;
        }
        return false;
    }
  
}
