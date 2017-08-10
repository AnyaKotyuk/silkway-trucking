<?php
// ================================================================================================
// System : SEOCMS
// Module : job.defines.php
// Version : 1.0.0
// Date : 24.10.2006
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
// Ditriy Kerest	demetrius2006@gmail.com
// Purpose : All Definitions for module of Job opportunity
//
// ================================================================================================

include_once( $_SERVER['DOCUMENT_ROOT'].'/include/defines.php' );
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_job/job.class.php' );
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_job/backend/job_ctrl.class.php' );
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_job/jobLayout.class.php' );
  

define("TblModJob","mod_job");
define("TblModJobSprPosition","mod_job_spr_position");
define("TblModJobSprEducation","mod_job_spr_education");
define("TblModJobSprExperience","mod_job_spr_experience");
define("TblModJobSprContacts","mod_job_spr_contacts");
define("TblModJobSprTxt","mod_job_spr_txt");
define("TblModJobCategory","mod_job_category");
define("TblModJobStatuses","mod_job_statuses");
?>