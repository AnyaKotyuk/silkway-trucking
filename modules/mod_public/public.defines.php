<?php
// ================================================================================================
// System : PrCSM05
// Module : catalog.defines.php
// Version : 1.0.0
// Date : 21.03.2006
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : All Definitions for module of Catalog
//
// ================================================================================================

include_once( SITE_PATH.'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_public/public.class.php' );
include_once( SITE_PATH.'/modules/mod_public/publicLayout.class.php' ); 
include_once( SITE_PATH.'/modules/mod_public/backend/public_ctrl.class.php' ); 

define("MOD_PUBLIC", true);

define("TblModPublic","mod_public");
define("TblModPublicSprCateg","mod_public_spr_categ"); 
define("TblModPublicSprStatus","mod_public_spr_status");  
define("TblModPublicSprGroup","mod_public_spr_group"); 
define("TblModPublicSet","mod_public_set");
define("TblModPublicSprTxt","mod_public_spr_txt");
define("PublicUploadFilesPath","/images/mod_public/");

define("LIFE_PERIOD", 730); //730 суток
?>
