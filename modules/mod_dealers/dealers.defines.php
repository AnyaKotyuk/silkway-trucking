<?php
// ================================================================================================
// System : SEOCMS
// Module : dealers.defines.php
// Version : 1.0.0
// Date : 15.11.2006
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : All Definitions for module of Dealers
//
// ================================================================================================

include_once( SITE_PATH.'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_dealers/dealers.class.php' );
include_once( SITE_PATH.'/modules/mod_dealers/dealersLayout.class.php' ); 

define("MOD_DEALERS", true);

define("TblModDealers","mod_dealers");
define("TblModDealerSprName","mod_dealers_spr_name"); 
define("TblModDealerSprCity","mod_dealers_spr_city");
define("TblModDealerSprCityFonImg","mod_dealers_spr_city_fonimg");
define("TblModDealerSprGroup","mod_dealers_spr_group");
define("Dealer_Path", "/modules/mod_dealers/"); 
define("Dealer_Full_Path", SITE_PATH."/modules/mod_dealers/");   
define("Dealer_Img_Path", "/images/mod_dealers/"); 
define("Dealer_Img_Full_Path", SITE_PATH."/images/mod_dealers/");
define("Dealer_Img_Path_City", "/images/spr/mod_dealers_spr_city/"); 
define("Dealer_Img_Full_Path_City", SITE_PATH."/images/spr/mod_dealers_spr_city/");  

?>
