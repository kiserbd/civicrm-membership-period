DROP TABLE IF EXISTS `civicrm_membership_period`;

-- /*******************************************************
-- *
-- * civicrm_membership_period
-- *
-- * Membership period log table
-- *
-- *******************************************************/
CREATE TABLE `civicrm_membership_period` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique MemberShipPeriod ID',
     `start_date` date    COMMENT 'Membership start date',
     `end_date` date    COMMENT 'Membership end date',
     `membership_id` int unsigned NOT NULL   COMMENT 'FOREIGN KEY of Membership',
     `contribution_id` int unsigned    COMMENT 'FOREIGN KEY of Contribution' 
,
        PRIMARY KEY (`id`)
 
 
,          CONSTRAINT FK_civicrm_membership_period_membership_id FOREIGN KEY (`membership_id`) REFERENCES `civicrm_membership`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_membership_period_contribution_id FOREIGN KEY (`contribution_id`) REFERENCES `civicrm_contribution`(`id`) ON DELETE CASCADE  
)  ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci  ;

