<?php                              /* shop_settings.backend.php */
// ================================================================================================
// System : SEOCMS
// Module : shop_settings.backend.php
// Version : 1.0.0
// Date : 30.09.2008
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Purpose : script for all actions with settings for ShopExport on the back-end
//
// ================================================================================================
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_shop_export/shop.defines.php' );


if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//============================================================================================
// START
// Blocking to execute a script from outside (not Admin-part) 
//============================================================================================
$Msg = new ShowMsg();
$goto = "http://".NAME_SERVER."/admin/index.php?logout=1";
//echo '<br>$goto='.$goto;
if ( !isset($_SESSION[ 'session_id']) OR empty($_SESSION[ 'session_id']) OR empty( $module ) ) {
     //$Msg->show_msg( '_NOT_AUTH' );
     //return false;
     ?><script>window.location.href="<?=$goto?>";</script><?;
}

$logon = new  Authorization();
if (!$logon->LoginCheck()) {
    //return false;
    ?><script>window.location.href="<?=$goto?>";</script><?; 
}
//=============================================================================================
// END
//=============================================================================================

if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $task='show';
else $task=$_REQUEST['task'];

if( !isset($_REQUEST['name']) ) $name=null;
else $name=$_REQUEST['name'];

if( !isset($_REQUEST['company']) ) $company=null;
else $company=$_REQUEST['company'];

if( !isset($_REQUEST['url']) ) $url=null;
else $url = $_REQUEST['url'];

if( !isset($_REQUEST['delivery']) ) $delivery=0;
else $delivery=1;

if( !isset($_REQUEST['currency']) ) $currency=0;
else $currency=1;

if( !isset($_REQUEST['nadavi']) ) $nadavi=0;
else $nadavi=1;

if( !isset($_REQUEST['meta']) ) $meta=0;
else $meta=1;

if( !isset($_REQUEST['bigmir']) ) $bigmir=0;
else $bigmir=1;

if( !isset($_REQUEST['price_ua']) ) $price_ua=0;
else $price_ua=1;

if( !isset($_REQUEST['pay_ua']) ) $pay_ua=0;
else $pay_ua=1;

if( !isset($_REQUEST['marketgid']) ) $marketgid=0;
else $marketgid=1;

if( !isset($_REQUEST['yandex']) ) $yandex=0;
else $yandex=1;

if( !isset($_REQUEST['hotline']) ) $hotline=0;
else $hotline=1;

if( !isset($_REQUEST['hotprice']) ) $hotprice=0;
else $hotprice=1;

if( !isset($_REQUEST['e-katalog']) ) $ekatalog=0;
else $ekatalog=1;

if( !isset($_REQUEST['iua']) ) $iua=0;
else $iua=1;

$Shop = new Shop_settings($logon->user_id, $module,NULL,NULL,NULL,"500");
$Shop->name = $name;
$Shop->company = $company; 
$Shop->url = $url; 
$Shop->delivery = $delivery; 
$Shop->currency = $currency; 
$Shop->nadavi = $nadavi;
$Shop->meta = $meta;
$Shop->bigmir = $bigmir; 
$Shop->price_ua = $price_ua;
$Shop->pay_ua = $pay_ua;
$Shop->marketgid = $marketgid; 
$Shop->yandex = $yandex; 
$Shop->hotline = $hotline;
$Shop->hotprice = $hotprice;
$Shop->ekatalog = $ekatalog;
$Shop->iua = $iua;
$scriptact = $_SERVER['PHP_SELF']."?module=$Shop->module";
 switch( $task ) {
    case 'show':      
                $Shop->ShowSettings();
                break;
    case 'save':      
                if ( $Shop->SaveSettings() ) echo "<script>window.location.href='$scriptact';</script>";
                break;
 }
?>
