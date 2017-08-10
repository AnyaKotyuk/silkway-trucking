<?php                                     /* shop.backend.php */
// ================================================================================================
// System : SEOCMS
// Module : shop.backend.php
// Date : 30.09.2008
// Licensed To:  Igor Trokhymchuk ihoru@mail.ru
// Purpose : script for all actions with ShopExport on the back-end
// ================================================================================================
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/modules/mod_shop_export/shop.defines.php' );
error_reporting(0);

if(!defined("_LANG_ID")) { $pg = new PageAdmin();}

header('Content-type: text/xml; charset=windows-1251');

if( !isset($_REQUEST['sel']) ) $sel=NULL;
else $sel = $_REQUEST['sel'];

$Shop = new ShopExport();
$arr = $Shop->GetData();
//print_r($arr);
//echo "<br>Позиций на экспорт: ".count($arr)."<br />";
if ( count($arr)==0){
    echo '<br />'.$Shop->multi['TXT_EXPORT_CATALOG_NO_POSITIONS'];
    return false;
}
switch($sel){
    case 'nadavi':
        $Shop->SaveNadaviXML($arr);
        $filename = SITE_PATH.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel];
        break;

    case 'meta':
        $Shop->SaveMetaXML($arr);
        $filename = SITE_PATH.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel];
        break;

    case 'bigmir':
        $Shop->SaveBigmirXML($arr);
        $filename = SITE_PATH.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel];
        break;
    case 'e-katalog':
        $Shop->SaveEkatalogXML($arr);
        $filename = SITE_PATH.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel];
        break;
    case 'hotline':
        $Shop->SaveHotLineXML($arr);
        $filename = SITE_PATH.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel];
        break;
    case 'hotprice':
        $Shop->SaveHotPriceXML($arr);
        $filename = SITE_PATH.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel];
        break;
    case 'pay_ua':
        $Shop->SavePayUaXML($arr);
        $filename = SITE_PATH.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel];
        break;
    case 'price_ua':
        $Shop->SavePrice_uaXML($arr);
        $filename = SITE_PATH.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel];
        break;
    case 'yandex':
        $Shop->SaveYandexXML($arr);
        $filename = SITE_PATH.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel];;
        break;
    case 'iua':
        $Shop->SaveYandexXML($arr);
        $filename = SITE_PATH.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel];
        break;
    case 'marketgid':
        $Shop->SaveMarketgidXML($arr);
        $filename = SITE_PATH.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel];
        break;

    default:
        echo $Shop->multi['TXT_MARKET_EXPORT_CATALOG_NO_SELECT'];
        break;
 }

 if(isset($filename)) {
     //header('Content-type: text/xml; charset=windows-1251');
     $handle = fopen($filename, "r");
     $contents = fread($handle, filesize($filename));
     fclose($handle);
     echo  $contents;
 }
 else {
     echo $Shop->multi['TXT_MARKET_EXPORT_CATALOG_ERROR'];
 }
?>
