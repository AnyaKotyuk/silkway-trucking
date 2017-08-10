<?php
/**
* news.php
* script for all actions with news on front-end
* @package News Package of SEOCMS
* @author Ihor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 22.07.2011
* @copyright (c) 2010+ by SEOTM
*/
include_once( $_SERVER['DOCUMENT_ROOT'].'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_news/news.defines.php' );

$Page = check_init('PageUser', 'PageUser');

$News = check_init('NewsLayout', 'NewsLayout');


if( !isset( $_REQUEST['task'] ) ) $News->task = '';
else $News->task = $News->Form->GetRequestTxtData($_REQUEST['task'], 1);

if(!isset($_REQUEST['sort'])) $News->sort=NULL;
else $News->sort=$News->Form->GetRequestTxtData($_REQUEST['sort'], 1);

if(!isset($_REQUEST['start'])) $News->start=0;
else $News->start=$News->Form->GetRequestTxtData($_REQUEST['start'], 1);

if(!isset($_REQUEST['display'])) $News->display=9;
else $News->display=$News->Form->GetRequestTxtData($_REQUEST['display'], 1);

if(!isset($_REQUEST['page'])) $News->page=1;
else $News->page = $News->Form->GetRequestTxtData($_REQUEST['page'], 1);

if(!isset($_REQUEST['tag'])) $News->tag=NULL;
else $News->tag = $News->Form->GetRequestTxtData($_REQUEST['tag'], 1);

if(!isset($_REQUEST['monthForAjax'])) $News->monthForAjax=NULL;
else $News->monthForAjax = $_REQUEST['monthForAjax'];

if(!isset($_REQUEST['yearForAjax'])) $News->yearForAjax=NULL;
else $News->yearForAjax = $_REQUEST['yearForAjax'];
//var_dump($_REQUEST);
$News->startTop = 0;
$News->displayTop = 9;
if($News->page>1) $News->startTop = ($News->page-1)*$News->displayTop;
//echo '<br />$News->display='.$News->display;
if($News->page>1) $News->start = ($News->page-1)*$News->display;
if(strval($News->page)=='all') {
    $News->start = 0;
    $News->display = 999999;
}

if( !isset( $_REQUEST['day'] ) )$News->day = NULL;
else $News->day = $News->Form->GetRequestTxtData($_REQUEST['day'], 1);
/*else $News->day = $_REQUEST['day'];*/

if( !isset( $_REQUEST['category'] ) ) $News->category = NULL;
else {
    $News->category = $News->Form->GetRequestTxtData($_REQUEST['category'], 1);
    $News->fltr .= " AND `".TblModNews."`.`id_category`=".$News->category;
}

if( !isset ( $_REQUEST['id'] ) ) $News->id = NULL;
else {
    $News->id = $News->Form->GetRequestTxtData($_REQUEST['id'], 1);
    $News->fltr .= " AND `".TblModNews."`.`id`=".$News->id;
}
// $str_cat - for mod_rewrite

$new_link = '';
if( !isset( $_REQUEST['str_cat'] ) ) $News->str_cat = NULL;
else{
    $News->str_cat = $News->Form->GetRequestTxtData($_REQUEST['str_cat'], 1);
    //var_dump($News->str_cat);
    $News->id_cat = $News->GetIdCatByStr($News->str_cat);
    //var_dump($News->id_cat);
    if( empty($News->id_cat) ) $Page->Set_404();
    else{
        $sub_levels = $News->Spr->GetSubLevelsInStr(TblModNewsCategory, $News->id_cat, $News->lang_id);
        if(!empty($sub_levels)){
            $sub_levels = $News->id_cat.','.$sub_levels;
        }else{
            $sub_levels = $News->id_cat;
        }
        $News->fltr .= " AND `".TblModNews."`.`id_cat` IN (".$sub_levels.")";
    }

    if($News->translit_lang_id!=$News->lang_id AND !empty($News->translit_lang_id)){
        $new_link = $News->Link($News->id_cat);
    }
}
//echo '<br>$News->str_cat='.$News->str_cat.' $News->category='.$News->category;

// $str_news - for mod_rewrite
if( !isset( $_REQUEST['str_news'] ) ) $News->str_news = NULL;
else{
    $News->str_news = $News->Form->GetRequestTxtData($_REQUEST['str_news'], 1);
    $News->id = $News->GetIdNewsByStrNews($News->str_news);
    if(empty($News->id)) $Page->Set_404();
    else $News->fltr .= " AND `".TblModNews."`.`id`=".$News->id;

    if( ($News->translit_prop_lang_id!=$News->lang_id AND !empty($News->translit_prop_lang_id)) OR !empty($new_link)){
        $new_link = $News->Link($News->id_cat, $News->id);
    }
}

//необходим для 301-го редиректа с УРЛ одной язsковой версии на корректную другую
if(!empty($new_link)){
    header ('HTTP/1.1 301 Moved Permanently');
    header ('Location: '.$new_link);
    exit();
//    var_dump($new_link);
}

//echo '$News->id='.$News->id;
if( !isset ( $_REQUEST['categories'] ) ) $News->categories = NULL;
else $News->categories = $_REQUEST['categories'];

if( !isset( $_REQUEST['s_keywords'] ) ) $News->s_keywords = NULL;
else $News->s_keywords = $News->Form->GetRequestTxtData($_REQUEST['s_keywords'], 1);

if( !isset ( $_REQUEST['subscriber'] ) ) $News->subscriber = NULL;
else $News->subscriber = $News->Form->GetRequestTxtData($_REQUEST['subscriber'], 1);

if( !isset ( $_REQUEST['subscr_pass'] ) ) $News->subscr_pass = NULL;
else $News->subscr_pass = strip_tags(trim($_REQUEST['subscr_pass']));


if(isset ($Page->FrontendPages))
    $FrontendPages = &$Page->FrontendPages;
else
    $FrontendPages = check_init('FrontendPages', 'FrontendPages');

$FrontendPages->lang_id = $News->lang_id;
$FrontendPages->page = PAGE_NEWS;
$Page->FrontendPages->page_txt = $Page->FrontendPages->GetPageTxt($Page->FrontendPages->page);

$News->SetSeoData($Page->FrontendPages->page_txt);
$Page->News = $News;
//echo '<br>$News->title='.$News->title;
$Page->SetTitle( $News->title );
$Page->SetDescription( $News->description );
$Page->SetKeywords( $News->keywords );
$Page->SetMultiUrls($News->getMultiUrls());
$Page->h1 = $News->h1;
if( isset( $_REQUEST['id'] ) ) $News->fltr = ' AND `id`='.$_REQUEST['id'];
if(!is_ajax) {
    $Page->breadcrumb = $News->ShowNewsPath($Page->FrontendPages->page_txt);
}
ob_start();
//echo  '<br />$News->task='.$News->task;
switch( $News->task ){
    case 'ShowNewsByCalendar':
        echo $News->ShowNewsByCalendar();
        break;
    case 'drupal':
        $drupal = new DrupalToSeocms();
        break;
    case 'showsm':
        $News->fltr = $News->fltr." and `".TblModNews."`.status='a'";
        $News->ShowNewsByPages();
        break;

    case 'showa':
        $News->fltr = $News->fltr." and `".TblModNews."`.status='a'";
        $News->ShowNewsByPages();
        break;

    case 'showall':
        $News->fltr = $News->fltr." and `".TblModNews."`.status!='i'";
        $News->ShowNewsByPages();
        break;

    case 'arch':
        $News->fltr = $News->fltr." and `".TblModNews."`.status='e'";
        $News->ShowNewsByPages();
        break;

    case 'showbytags':
        $News->fltr = $News->fltr." and `".TblModNews."`.status!='i'";
        $News->ShowTags( $News->tag );
        break;

    case 'showfull':
        $Page->breadcrumb = $News->ShowPath($Page->FrontendPages->page_txt);
        $News->fltr = $News->fltr." and `".TblModNews."`.status!='i'";
        $News->NewsShowFull();
        break;

    case 'showrelart':
        $News->NewsRelartDisplay( $News->Form->GetRequestTxtData($_REQUEST['relart'], 1) );
        break;

    case 'news_by_date':
        $News->ShowNewsByDate($News->day);
        break;

    case 'search_result':
        $News->showSearchResult();
        break;

    case 'last_news':
        $News->showNewsLastWidget();
        break;

    case 'new_subscriber':
        $News->SubscrRegForm();
        break;

    case 'save_reg_data':
        if ( $News->CheckFields()!=NULL ){
            $News->SubscrRegForm();
            break;
        }
        if($News->SubscrSave()){
            if ( !$News->SendHTML() ) {
                $News->ShowTextMessages($News->multi['MSG_SUBSCRIBER_NOT_SENT']);
            }
            else {
                $News->ShowTextMessages($News->multi['MSG_SUBSCRIBER_SAVED']);
                $News->ShowTextMessages($News->multi['MSG_SUBSCRIBER_SENT_OK']);
            }
        }
        else {
            $News->ShowTextMessages($News->multi['MSG_SUBSCRIBER_NOT_SAVED']);
        }
        break;

    case 'activate':
        $News->ActivateUser($News->subscriber);
        break;

    case 'subscr_del':
        if($News->SubscrDel()) { $News->ShowTextMessages($News->multi['MSG_SUBSCRIBER_DEL_OK']); }
        else { $News->ShowTextMessages($News->multi['MSG_SUBSCRIBER_NOT_DEL']); }
        break;
    case 'mainnews':
        $News->showNewsLastWidget();
        break;
    case 'newstop':
        $News->showNewsTopLast();
        break;
    default:
        $Page->breadcrumb = $News->ShowPath($Page->FrontendPages->page_txt);
        $News->fltr = " and `".TblModNews."`.status='a'";
        $News->ShowNewsByPages();
        break;
}

//$News->ShowNewsLinks();

$Page->content = ob_get_clean();
$Page->out();
?>