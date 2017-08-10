<?php
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/include/defines.php' );

$Page = new PageUser();

$ModulesPlug = check_init('ModulesPlug', 'ModulesPlug');
$id_module = $ModulesPlug->GetModuleIdByPath( 'mod_dealers/dealers.backend.php' );
$Page->module=$id_module;
if(isset ($Page->FrontendPages))
    $FrontendPages = &$Page->FrontendPages;
else
    $FrontendPages = check_init('FrontendPages', 'FrontendPages');
$FrontendPages->lang_id = _LANG_ID;
$FrontendPages->page = PAGE_DEALERS;
$FrontendPages->page_txt = $FrontendPages->GetPageTxt($FrontendPages->page);


if( !isset( $_REQUEST['task'] ) ) $task = '';
else $task = $_REQUEST['task'];

if( !isset( $_REQUEST['cat'] ) ) $cat = NULL;
else $cat = $_REQUEST['cat'];

if (!isset($task)) $task = 'all';
if (!isset($item)) $item = NULL;

$Dealer = new DealerLayout();
$Dealer->city_id=$cat;
if($cat!=NULL)$Dealer->cat=$Dealer->GetCodFoTranslit($cat);
 !empty($item) ? $title = $Dealer->GetNameOfDealer($item).' |  '.META_TITLE              :
    $FrontendPages->GetTitle()==NULL          ? $title = META_TITLE               : $title = $FrontendPages->GetTitle();
 !empty($item) ? $Description = $Dealer->GetNameOfDealer($item).META_DESCRIPTION    :
    $FrontendPages->GetDescription()==NULL    ? $Description = META_DESCRIPTION   : $Description = $FrontendPages->GetDescription();
 !empty($item) ? $Keywords = $Dealer->GetNameOfDealer($item).', '.META_KEYWORDS          :
    $FrontendPages->GetKeywords()==NULL       ? $Keywords = META_KEYWORDS         : $Keywords = $FrontendPages->GetKeywords();


$Page->SetTitle( $title );
$Page->SetDescription( $Description );
$Page->SetKeywords( $Keywords );

$Page->WriteHeader();
$path=$FrontendPages->ShowPath(PAGE_DEALERS).$Dealer->GetPathFoidCat($title,$Dealer->cat);;
$Page->Form->WriteContentHeader(false, false,$path);
//echo '$task='.$task.' $cat='.$cat;;
switch($task){
    case 'all':
        $Dealer->ShowAllDealers();
        break;
    case 'item':
        $Dealer->ShowDetailDealer();
        break;
    default:
        $Dealer->ShowAllDealers();
}
$Page->Form->WriteContentFooter();
$Page->WriteFooter();
?>