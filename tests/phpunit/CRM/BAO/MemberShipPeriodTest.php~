<?php

use CRM_Membershipperiod_ExtensionUtil as E;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

/**
 * FIXME - Add test description.
 *
 * Tips:
 *  - With HookInterface, you may implement CiviCRM hooks directly in the test class.
 *    Simply create corresponding functions (e.g. "hook_civicrm_post(...)" or similar).
 *  - With TransactionalInterface, any data changes made by setUp() or test****() functions will
 *    rollback automatically -- as long as you don't manipulate schema or truncate tables.
 *    If this test needs to manipulate schema or truncate tables, then either:
 *       a. Do all that using setupHeadless() and Civi\Test.
 *       b. Disable TransactionalInterface, and handle all setup/teardown yourself.
 *
 * @group headless
 */
class CRM_BAO_MemberShipPeriodTest extends \PHPUnit_Framework_TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {

protected $mockedContact;
protected $mockedContactParams = [
'contact_type' => 'Individual',
'first_name' => 'Jhon',
'last_name' => 'Deo',
 ];

protected $mockedMembershipTypeId;
protected $mockedMembershipTypeParams = [
  'name' => 'test type',
  'domain_id' => 1,
  'description' => 'Test membership type',
  'minimum_fee' => 10,
  'duration_unit' => 'year',
  'member_of_contact_id' => 1,
  'period_type' => 'fixed',
  'duration_interval' => 1,
  'financial_type_id' => 1,
  'visibility' => 'Public',
];
protected $mockedMembership;
protected $mockedMembershipParams = [
  'contact_id' => 0,
  'membership_type_id' => 0,
  'join_date' => '',
  'start_date' => '',
  'end_date' => '',
  'source' => 'Payment',
  'is_override' => 1,
  'status_id' => 1,
]; 
protected $mockedMembershipPeriod = [];
protected $mockedMembershipPeriodParams = [      
  'membership_id' => 0,
  'start_date' => '',
  'end_date' => '',
];

protected $mockedContributionId;
protected $mockedContributionParams = [
    'contact_id' => 0,
    'currency' => 'USD',
    'financial_type_id' => 1,
    'contribution_status_id' => 1,
    'payment_instrument_id' => 1,
    'source' => 'STUDENT',
    'receive_date' => '20080522000000',
    'receipt_date' => '20080522000000',
    'non_deductible_amount' => 0.00,
    'total_amount' => 200.00,
    'fee_amount' => 5,
    'net_amount' => 195,
    'trxn_id' => '22ereerwww444444',
    'invoice_id' => '86ed39c9e9ee6ef6031621ce0eafe7eb81',
    'thankyou_date' => '20080522',
];

protected $membershipPaymentParams = [
    'membership_id' => 0,
    'contribution_id' => 0,
];

protected $membershipPeriodParams = [
    'membership_id' => 0,
    'start_date' => '',
    'end_date' => '',
    'contribution_id' => null
];



protected function setupContact()
{
    $mockedContact = \CRM_Core_DAO::createTestObject('CRM_Contact_DAO_Contact', $this->mockedContactParams);
    return $mockedContact;
}

protected function setupMembershipType()
{
    $mockedMembershipType = \CRM_Core_DAO::createTestObject('CRM_Member_BAO_MembershipType', $this->mockedMembershipTypeParams);
    return $mockedMembershipType;
}

protected function setupMembership($startDate = '2017-01-21', $endDate = '2017-12-21')
{
    $contact = $this->setupContact();
    $membershipType = $this->setupMembershipType();

    $this->mockedMembershipParams["contact_id"] = $contact->id;
    $this->mockedMembershipParams["join_date"] = date('Ymd', strtotime('2017-01-21'));
    $this->mockedMembershipParams["start_date"] = date('Ymd', strtotime($startDate));
    $this->mockedMembershipParams["end_date"] = date('Ymd', strtotime($endDate));
    $this->mockedMembershipParams["membership_type_id"] = $membershipType->id;

    $mockedMembership = \CRM_Core_DAO::createTestObject('CRM_Member_DAO_Membership', $this->mockedMembershipParams);

    return $mockedMembership;
}

protected function updateMembership()
{
    $mockedMembership = \CRM_Core_DAO::createTestObject('CRM_Member_DAO_Membership', $this->mockedMembershipParams);
    $membershipPeriod = CRM_Membershipperiod_BAO_MemberShipPeriod::manageMembershipPeriod('create', 'Membership', $mockedMembership->id, $mockedMembership);
    
    return $mockedMembership;
}

protected function setupMembershipPeriod($contributionId = null)
{
    $this->membershipPeriodParams["membership_id"] = $this->mockedMembership->id;
    $this->membershipPeriodParams["start_date"] = date('Ymd', strtotime('2017-01-21'));
    $this->membershipPeriodParams["end_date"] = date('Ymd', strtotime('2017-12-21'));
    $this->membershipPeriodParams["contribution_id"] = $contributionId;
    
//    $mockedMembershipPeriod = \CRM_Core_DAO::createTestObject('CRM_Membershipperiod_DAO_MemberShipPeriod', $this->membershipPeriodParams);

    return $this->membershipPeriodParams;
}


protected function setupContribution()
{
    $this->mockedContributionParams['contact_id'] = $this->mockedContact->id;
    $this->mockedContributionParams['receive_date'] = date('Ymd', strtotime('2017-01-21'));
    $this->mockedContributionParams['receipt_date'] = date('Ymd', strtotime('2017-01-21'));
    $mockedContribution = \CRM_Core_DAO::createTestObject('CRM_Contribute_DAO_Contribution', $this->mockedContributionParams);
    $this->mockedContributionId = $mockedContribution->id;
    return $mockedContribution;
}

private function createPayment($membershipId, $contributionId) {
    $this->membershipPaymentParams['membership_id'] = $membershipId;
    $this->membershipPaymentParams['contribution_id'] = $contributionId;
    
    $mockedMembershipPayment = \CRM_Core_DAO::createTestObject('CRM_Member_DAO_MembershipPayment', $this->membershipPaymentParams);
    
    return $mockedMembershipPayment;
}

private static function getLatestMembershipPeriod($membershipId)
{
    $membershipPeriod = civicrm_api3('MemberShipPeriod', 'get', [
            'membership_id' => $membershipId,
            'sequential' => 1,
            'limit' => 1,
            'sort' => 'id DESC'
        ]);
    return isset($membershipPeriod['values'][0]) ? $membershipPeriod: null;
}

/**
     * Calculate the next date based on interval, unit and date
     *
     * @param $unit
     * @param $interval
     * @param $startDate
     * @return DateTime|false
     */
    private function getDateInterval($unit, $interval, $startDate) {
        $interval = $interval . ' ' . $unit;
        $date = date_create($startDate);
        $newEndDate = date_add($date, date_interval_create_from_date_string($interval));
        return $newEndDate;
    }

public function setUpHeadless() {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  public function setUp() {
    $mockedContact = \CRM_Core_DAO::createTestObject('CRM_Contact_DAO_Contact', $this->mockedContactParams);
    $this->mockedContact = $mockedContact;
    $mockedMembershipType = \CRM_Core_DAO::createTestObject('CRM_Member_BAO_MembershipType', $this->mockedMembershipTypeParams);
    
    $this->mockedMembershipTypeId = $mockedMembershipType->id;
    $this->mockedMembershipParams["contact_id"] = $mockedContact->id;
    $this->mockedMembershipParams["join_date"] = date('Ymd', strtotime('2017-01-21'));
    $this->mockedMembershipParams["start_date"] = date('Ymd', strtotime('2017-01-21'));
    $this->mockedMembershipParams["end_date"] = date('Ymd', strtotime('2017-12-21'));
    $this->mockedMembershipParams["membership_type_id"] = $mockedMembershipType->id;
    $mockedMembership = \CRM_Core_DAO::createTestObject('CRM_Member_DAO_Membership', $this->mockedMembershipParams);
    $this->mockedMembership = $mockedMembership;  
    parent::setUp();
  }

  public function tearDown() {
    parent::tearDown();
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
    private static function validateMembershipData($startDate, $endDate)
    {
        $startDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);
        $dateDifference = $startDate->diff($endDate);

        if ($dateDifference->format('%R%a') < 0) {
            throw new Exception(ts("End date must be the greater or equal than start date."));
            return;
        }
        
        $params = [
            "membership_id"     => $this->mockedMembership->id,
            "start_date"        => $startDate,
            "end_date"          => $endDate,            
        ];
        return $params;
    }
  
  public function testCreateMembershipPeriodWithValidData()
  {          
    $membershipPeriod = CRM_Membershipperiod_BAO_MemberShipPeriod::manageMembershipPeriod('create', 'Membership', $this->mockedMembership->id, $this->mockedMembership);
    $this->assertObjectHasAttribute('id', $membershipPeriod);
   
  }
  
  public function testCreateMembershipPeriodWithInvalidData()
  {
      try{
          $membership = $this->setupMembership('2017-01-21', '2017-01-20');
          $membershipPeriod = CRM_Membershipperiod_BAO_MemberShipPeriod::manageMembershipPeriod('create', 'Membership', $membership->id, $membership);
          
      }catch (Exception $e) {
          $this->assertEquals($e->getMessage(), 'End date must be the greater or equal than start date.');
      }
  }
  
  public function testCreateMembershipPeriodWithContribution()
  {
      $contribution = $this->setupContribution();
      $membershipPayment = $this->createPayment($this->mockedMembership->id, $contribution->id);
      $membershipPeriodParams = $this->setupMembershipPeriod();
      
      $membershipPeriod = CRM_Membershipperiod_BAO_MemberShipPeriod::create($membershipPeriodParams);
      $membershipPeriod->contribution_id = $contribution->id;
      
      $membershipPeriod = CRM_Membershipperiod_BAO_MemberShipPeriod::manageMembershipPeriod('update', 'MembershipPayment', $this->mockedMembership->id, $membershipPeriod);
      //Membership period should have a link of contribution as memberhsip payment
      $this->assertEquals($membershipPayment->contribution_id, $membershipPeriod->contribution_id);
      
  }
  
  public function testUpdateMembershipPeriod()
  {
      $membership = $this->mockedMembership;
      
      // When i changed the start or end date of contact membership
        $newStartDate = date_format(
            $this->getDateInterval("year", 1, $membership->start_date),
            'Y-m-d'
        );
        $newEndDate = date_format(
            $this->getDateInterval("year", 1, $membership->end_date),
            'Y-m-d'
        );
        
      $this->mockedMembershipParams["id"] = $membership->id;
      $this->mockedMembershipParams["start_date"] = date('Ymd', strtotime($newStartDate));
      $this->mockedMembershipParams["end_date"] = date('Ymd', strtotime($newEndDate));
      $newMembership = $this->updateMembership();
      
      $latestMembershipPeriod = $this->getLatestMembershipPeriod($newMembership->id);
      
      // The latest periodn should be same as the updated membership period.
      $this->assertEquals($newStartDate, $latestMembershipPeriod["values"][0]["start_date"]);
      $this->assertEquals($newEndDate, $latestMembershipPeriod["values"][0]["end_date"]);
  }
  
  public function testLifetimeMembership()
  {
      
  }
}
