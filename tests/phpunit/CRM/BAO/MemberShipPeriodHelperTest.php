<?php

use CRM_Membershipperiod_ExtensionUtil as E;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;
use CRM\Membershipperiod\BAO\MemberShipPeriodHelper;
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
class CRM_BAO_MemberShipPeriodHelperTest extends \PHPUnit_Framework_TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {
    
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
    protected $mockedContributionParams = [];

    protected static function setupContact()
    {
        $mockedContact = \CRM_Core_DAO::createTestObject('CRM_Contact_DAO_Contact', $this->mockedContactParams);
        return $mockedContact;
    }

    protected static function setupMembershipType()
    {
        $mockedMembershipType = \CRM_Core_DAO::createTestObject('CRM_Member_BAO_MembershipType', $this->mockedMembershipTypeParams);
        return $mockedMembershipType;
    }

    protected static function setupMembership()
    {
        $contact = self::setupContact();
        $membershipType = self::setupMembershipType();
        
        $this->mockedMembershipParams["contact_id"] = $contact->id;
        $this->mockedMembershipParams["join_date"] = date('Ymd', strtotime('2017-01-21'));
        $this->mockedMembershipParams["start_date"] = date('Ymd', strtotime('2017-01-21'));
        $this->mockedMembershipParams["end_date"] = date('Ymd', strtotime('2017-12-21'));
        $this->mockedMembershipParams["membership_type_id"] = $membershipType->id;
        
        $mockedMembership = \CRM_Core_DAO::createTestObject('CRM_Member_DAO_Membership', $this->mockedMembershipParams);
        
        return $mockedMembership;
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
     * Get membership deration details
     *
     * @param $membershipId
     * @return array
     */
    private static function getMembershipDurationDetails($membershipId) {
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
  
  public function testIsRenew()
  {
      $membership = self::getMembershipDurationDetails($this->mockedMembership->id);
      $this->assertEquals(false, count($this->mockedMembershipPeriod), 'No existing membership to renew.');
      
      
//        // get the last period
        $newEndDate = self::getDateInterval(
            $membership['membership_type_id.duration_unit'],
            $membership['membership_type_id.duration_interval'],
            '2017-12-21'
        );
        
        $newEndDate = date_format($newEndDate, 'Y-m-d');
        
        //Check if the new endDate is 2018-12-21 as it will add 1 year with the current date(2017-12-21) 
        $this->assertEquals($newEndDate, '2018-12-21', 'Renew the existing membership');
        
  }
  
  public function testNoRenewMembership()
  {
      $membership = self::getMembershipDurationDetails($this->mockedMembership->id);      
      
        // get the last period
        $newEndDate = self::getDateInterval(
            $membership['membership_type_id.duration_unit'],
            $membership['membership_type_id.duration_interval'],
            ''
        );
        
        $newEndDate = date_format($newEndDate, 'Y-m-d');
        
        //Check if the new endDate is 2018-12-21 as it will add 1 year with the current date(2017-12-21) 
        $this->assertNotEquals($newEndDate, '2018-12-21', 'Renew the existing membership');
  }  

}
