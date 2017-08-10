<?
// ================================================================================================
//    System     : SEOCMS
//    Module     : Department
//    Version    : 1.0.0
//    Date       : 01.07.2010   
//    Purpose    : Defines Department Module
//    Licensed To: Yaroslav Gyryn   
// ================================================================================================
include_once(SITE_PATH . '/include/defines.php');
include_once(SITE_PATH . '/modules/mod_department/department.class.php');
include_once(SITE_PATH . '/modules/mod_department/departmentCtrl.class.php');
include_once(SITE_PATH . '/modules/mod_department/departmentLayout.class.php');
include_once(SITE_PATH . '/modules/mod_department/department_settings.class.php');
include_once(SITE_PATH . '/modules/mod_department/departmentDoctorCtrl.class.php');

define("MOD_DEPARTMENT", true);

define("TblModDepartment", "mod_department");
define("TblModDepartmentCat", "mod_department_spr_category");
define("TblModDepartmentTxt", "mod_department_txt");
define("TblModDepartmentSprTxt", "mod_department_spr_txt");
define("TblModDepartmentDoctor", "mod_department_doctor");
define("TblModDepartmentDoctorTxt", "mod_department_doctor_txt");

// --------------- defines for news settings  ---------------
define("TblModDepartmentSet", "mod_department_set");
define("TblModDepartmentSetSprMeta", "mod_department_set_meta");
?>