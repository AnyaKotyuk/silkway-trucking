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

if(!defined("_LANG_ID")) {$pg = new PageAdmin();}

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part)
if ( !$pg->logon->isAccessToScript($module)) exit;

if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $task='show';
else $task=$_REQUEST['task'];

if( !isset($_REQUEST['id']) ) $id=NULL;
else $id = $_REQUEST['id'];

if( !isset($_REQUEST['sel']) ) $sel=NULL;
else $sel = $_REQUEST['sel'];

$Shop = new ShopExport($pg->logon->user_id, $module);
$Shop->user_id = $pg->logon->user_id;
$Shop->module = $module;
$Shop->task = $task;
//echo '<br>$Shop->task='.$Shop->task.' $Shop->id='.$Shop->id;
//echo 'Shop->task='.$Shop->task.'<br/>$sel = '.$sel;
switch( $Shop->task ) {
    case 'show':
        $Shop->show();
        break;
    case 'edit':
        if (!$Shop->edit()) echo "<script>window.location.href='".$Shop->script."';</script>";
        break;
    case 'new':
        $Shop->edit();
        break;
    case 'save_xml':
	    $arr = $Shop->GetData();
	    echo "<br>".$Shop->multi['TXT_EXPORT_CATALOG_POSITIONS_COUNT'].": ".count($arr)."<br />";
        if ( count($arr)==0){
            echo '<br />'.$Shop->multi['TXT_EXPORT_CATALOG_NO_POSITIONS'];
            return false;
        }
        switch($sel){
            case 'all':
                if($Shop->settings['nadavi']==1){
                    $Shop->SaveNadaviXML($arr);
                }
                if($Shop->settings['meta']==1){
                    $Shop->SaveMetaXML($arr);
                }
                if($Shop->settings['bigmir']==1){
                    $Shop->SaveBigmirXML($arr);
                }
                if($Shop->settings['price_ua']==1){
                    $Shop->SavePrice_uaXML($arr);
                }
                if($Shop->settings['pay_ua']==1){
                    $Shop->SavePayUaXML($arr);
                }
                if($Shop->settings['hotline']==1){
                    $Shop->SaveHotLineXML($arr);
                }
                if($Shop->settings['hotprice']==1){
                    $Shop->SaveHotPriceXML($arr);
                }
                if($Shop->settings['e-katalog']==1){
                    $Shop->SaveEkatalogXML($arr);
                }

                if($Shop->settings['marketgid']==1){
                    $Shop->SaveMarketgidXML($arr);
                }
                if($Shop->settings['yandex']==1){
                    $Shop->SaveYandexXML($arr);
                }
                echo $Shop->multi['TXT_MARKET_EXPORT_CATALOG_READY_ALL'];
                break;


            case 'nadavi':
                $Shop->SaveNadaviXML($arr);
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_FOR'].' '.$Shop->multi['TXT_MARKET_NADAVI'];
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_READY_FILE'].': <a href="'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'">'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'</a><br>';
                break;

            case 'meta':
                $Shop->SaveMetaXML($arr);
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_FOR'].' '.$Shop->multi['TXT_MARKET_META'];
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_READY_FILE'].': <a href="'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'">'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'</a><br>';
                break;

            case 'bigmir':
                $Shop->SaveBigmirXML($arr);
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_FOR'].' '.$Shop->multi['TXT_MARKET_BIGMIR'];
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_READY_FILE'].': <a href="'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'">'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'</a><br>';
          	    break;

            case 'e-katalog':
                $Shop->SaveEkatalogXML($arr);
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_FOR'].' '.$Shop->multi['TXT_MARKET_EKATALOG'];
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_READY_FILE'].': <a href="'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'">'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'</a><br>';
                break;

            case 'hotline':
                $Shop->SaveHotLineXML($arr);
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_FOR'].' '.$Shop->multi['TXT_MARKET_HOTLINE'];
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_READY_FILE'].': <a href="'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'">'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'</a><br>';
                break;

            case 'hotprice':
                $Shop->SaveHotPriceXML($arr);
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_FOR'].' '.$Shop->multi['TXT_MARKET_HOTPRICE'];
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_READY_FILE'].': <a href="'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'">'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'</a><br>';
                break;

            case 'pay_ua':
                $Shop->SavePayUaXML($arr);
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_FOR'].' '.$Shop->multi['TXT_MARKET_PAY_UA'];
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_READY_FILE'].': <a href="'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'">'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'</a><br>';
                break;

            case 'price_ua':
                $Shop->SavePrice_uaXML($arr);
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_FOR'].' '.$Shop->multi['TXT_MARKET_PRICE_UA'];
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_READY_FILE'].': <a href="'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'">'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'</a><br>';
         		break;

            case 'yandex':
                $Shop->SaveYandexXML($arr);
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_FOR'].' '.$Shop->multi['TXT_MARKET_YANDEX'];
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_READY_FILE'].': <a href="'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'">'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'</a><br>';
                break;

            case 'iua':
         	    $Shop->SaveIuaXML($arr);
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_FOR'].' '.$Shop->multi['TXT_MARKET_I_UA'];
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_READY_FILE'].': <a href="'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'">'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'</a><br>';
         		break;

            case 'marketgid':
         	    $Shop->SaveMarketgidXML($arr);
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_FOR'].' '.$Shop->multi['TXT_MARKET_MARKETGID'];
                echo '<br>'.$Shop->multi['TXT_MARKET_EXPORT_READY_FILE'].': <a href="'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'">'.$Shop->exportFolder.$Shop->resultXMLFilenameArr[$sel].'</a><br>';
         		break;

            default:
                echo $Shop->multi['TXT_MARKET_EXPORT_CATALOG_NO_SELECT'];
                break;
         }
         //echo "<br>Export Ok!";
         break;
}
?>
