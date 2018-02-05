## This extension is created as a test for Compucorp application process and is not ready for production deployment

# civicrm-membership-period

## Issue

Currently, when a membership is renewed in CiviCRM the “end date” field on the membership itself is extended by the length of the membership as defined in CiviCRM membership type configuration but no record of the actual length of any one period or term is recorded. As such it is not possible to see how many “terms” or “periods” of membership a contact may have had. 

I.e. If a membership commenced on 1 Jan 2014 and each term was of 12 months in length, by 1 Jan 2016 the member would be renewing for their 3rd term. The terms would be:

Term/Period 1: 1 Jan 2014 - 31 Dec 2014
Term/Period 2: 1 Jan 2015 - 31 Dec 2015
Term/Period 3: 1 Jan 2016 - 31 Dec 2016

The aim of this extension is to extend the CiviCRM membership component so that when a membership is created or renewed a record for the membership “period” is recorded. 

The membership period should be connected to a contribution record if a payment is taken for this membership or renewal.

## User's guide 

After installation [extension installation
documentation](https://docs.civicrm.org/user/en/latest/introduction/extensions/#installing-extensions) this extention on your `civicrm` instance, it will automatically log the membership period record with every create/renew of membership.

*Steps to follow*
1. Open the membership dashboard from membership nav menu. It will show all the contact membership. 
We assume that you already have contacts on your `civicrm` instance.
2. Click on the `name` of the contact link to see the contact details.
3. From the tab list please click on the membership tab to see memberships of that contact if available. 
4. Select any of the membership row by click on the renew menu. 
It will expand and can get the `Membership Period` menu to get membership period records. On the membership period view, you can see 
the contribution if have any. And can open it by click it's view.  


## Developer's guide

# New Entity

With the installation it will create a new DB table called `civicrm_membership_period` to store membership period records. 
It will `DROP` the table with uninstall the extension.

# Implementing civicrm `hooks`

Here we have implemented civicrm post hook [hook_civicrm_post](https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_post/) to store
membership period records with add/renew the membership as well membership payment.

Another hook called [hook_civicrm_links] (https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_links/) also implemented here to alter the link to view the membership period per membership

# Implementing BAO

We have membershipperiod BAO class `MemberShipPeriod`and it's  helper class `MemberShipPeriodHelper`. Those two classes are responsible
to manage and validate membership period add/update automatically along with add/renew membership and membership payment with the help of hook_civicrm_post hook.

# CIVICRM API

The following are usage of some of the API interaction endpoints available for the 
Membership Period extension.

#### Get Membership Periods

```text
REST:

http://example.com/sites/all/modules/civicrm/extern/rest.php?entity=MemberShipPeriod&action=get&api_key=userkey&key=sitekey&json={"sequential":1}
```

```php
PHP: 

$result = civicrm_api3('MemberShipPeriod', 'get', array(
  'sequential' => 1,
));
```
#### Create or Update Membership Period
```text
REST:

http://dev.local.civicrm/sites/all/modules/civicrm/extern/rest.php?entity=MemberShipPeriod&action=create&api_key=userkey&key=sitekey&json={"sequential":1,"start_date":"2017-01-01","end_date":"2019-12-01","membership_id":22}
```

```php
PHP

$result = civicrm_api3('MemberShipPeriod', 'create', array(
  'sequential' => 1,
  'start_date' => "2017-01-21",
  'end_date' => "2017-12-21",
  'membership_id' => 33,
));
```

For full details of other options, refer to [the CiviCRM API](https://docs.civicrm.org/dev/en/latest/api/)

**NOTE:**
>No need to call membership period `create` endpoint. As all period records store automatically along with membership add/renew.

## Authors
- [Abdullah Kiser]

## License
The extension is licensed under [AGPL-3.0](LICENSE.txt)
