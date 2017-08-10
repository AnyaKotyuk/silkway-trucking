<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 03.01.14
 * Time: 15:17
 */
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_promo_cod/promo_cod.class.php' );

define("TblModPromoCod","mod_promo_cod");