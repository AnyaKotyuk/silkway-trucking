<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 30.07.14
 * Time: 11:16
 */
include_once( $_SERVER['DOCUMENT_ROOT'].'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_vip/vip.define.php' );

$Page = check_init('PageUser', 'PageUser');
$vip = check_init('vip', 'vip');
$Page->FrontendPages->lang_id = $vip->lang_id;
$Page->FrontendPages->page = PAGE_VIP;
$Page->FrontendPages->page_txt = $Page->FrontendPages->GetPageTxt($Page->FrontendPages->page);

if(!isset($_REQUEST['task'])) $vip->task = 'show';
else $vip->task=$vip->Form->GetRequestTxtData($_REQUEST['task'], 1);

if(!isset($_REQUEST['start'])) $vip->start=0;
else $vip->start=$vip->Form->GetRequestTxtData($_REQUEST['start'], 1);

if(!isset($_REQUEST['display'])) $vip->display=10;
else $vip->display=$vip->Form->GetRequestTxtData($_REQUEST['display'], 1);

if(!isset($_REQUEST['page'])) $vip->page=1;
else $vip->page = $vip->Form->GetRequestTxtData($_REQUEST['page'], 1);
//echo '<br />$vip->display='.$vip->display;
if($vip->page>1) $vip->start = ($vip->page-1)*$vip->display;
if(strval($vip->page)=='all') {
    $vip->start = 0;
    $vip->display = 999999;
}

if(!isset($_REQUEST['str_vip'])) $vip->cod=NULL;
else{
    $vip->cod = $vip->getCodvipByStr($vip->Form->GetRequestTxtData($_REQUEST['str_vip'], 1));
}
//необходим для 301-го редиректа с УРЛ одной язsковой версии на корректную другую
if(!empty($vip->redirectLang_id) && $vip->redirectLang_id!=$vip->lang_id){
    $vip->initPageTxt();
    $new_link = $vip->Link($vip->pageTxt['translit']);
    echo '<br>$new_link='.$new_link;
    header ('HTTP/1.1 301 Moved Permanently');
    header ('Location: '.$new_link);
    exit();
}


if(!isset($_REQUEST['city_cod'])) $vip->city_cod=NULL;
else $vip->city_cod=$vip->Form->GetRequestTxtData($_REQUEST['city_cod'], 1);

if (!isset($_REQUEST['map'])) $vip->map = 0;
else{
    $vip->map = $_REQUEST['map'];
    if($vip->task != 'show') $vip->task = 'map';
}

ob_start();
if(empty($vip->cod)){
    $Page->FrontendPages->GetTitle()==NULL          ? $vip->title = META_TITLE               : $vip->title = $Page->FrontendPages->GetTitle();
    $Page->FrontendPages->GetDescription()==NULL    ? $vip->description = META_DESCRIPTION   : $vip->description = $Page->FrontendPages->GetDescription();
    $Page->FrontendPages->GetKeywords()==NULL       ? $vip->keywords = META_KEYWORDS         : $vip->keywords = $Page->FrontendPages->GetKeywords();
    $Page->FrontendPages->showContent();
}else{
    $vip->setSeoData();
    $Page->h1 = $vip->h1;
    $Page->breadcrumb = $vip->ShowPath($Page->FrontendPages->ShowPath($Page->FrontendPages->page,NULL,true));
    if(!is_ajax)$Page->linkList = _LINK.'vip/';
}
$Page->SetTitle( $vip->title );
$Page->SetDescription( $vip->description );
$Page->SetKeywords( $vip->keywords );
$Page->SetMultiUrls($vip->getMultiUrls());
$Page->cityCod = $vip->city_cod;
//echo '$vip->task='.$vip->task;
switch($vip->task){
    case 'show':
        $vip->showvipList();
        break;
    case 'showfull':
        $vip->showvipFull();
        break;
    case 'map':
        $vip->showvipMap();
        break;
}

$Page->content = ob_get_clean();
$Page->out();