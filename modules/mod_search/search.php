<?php
include_once( $_SERVER['DOCUMENT_ROOT'].'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_search/search.defines.php' );

//========= FIRST DEFINE PAGE LANGUAGE  BEGIN ===========
$Page = check_init('PageUser', 'PageUser');
//========= FIRST DEFINE PAGE LANGUAGE BEGIN  ===========

if(defined("MOD_NEWS") AND MOD_NEWS AND empty($Page->News) )
    $Page->News = check_init('NewsLayout', 'NewsLayout');

if(defined("MOD_PAGES") AND MOD_PAGES AND empty($Page->FrontendPages) )
    $Page->FrontendPages = check_init('FrontendPages', 'FrontendPages');

if(defined("MOD_CATALOG") AND MOD_CATALOG AND empty($Page->Catalog) )
    $Page->Catalog = check_init('CatalogLayout', 'CatalogLayout');

if(defined("MOD_ARTICLE") AND MOD_ARTICLE  AND empty($Page->Article) )
    $Page->Article = check_init('ArticleLayout', 'ArticleLayout');

if (defined("MOD_PUBLIC") AND MOD_PUBLIC AND empty($Page->Public))
$Page->Public = check_init('PublicLayout', 'PublicLayout');

if (defined("MOD_GALLERY") AND MOD_GALLERY AND empty($Page->Gallery))
$Page->Gallery = check_init('GalleryLayout', 'GalleryLayout');

$Search = check_init('Search', 'Search');

if( !isset ( $_REQUEST['task'] ) ) $task = NULL;
else $task = $_REQUEST['task'];

if(!isset($_REQUEST['start'])) $Search->start=0;
else $Search->start=$Search->Form->GetRequestTxtData($_REQUEST['start'], 1);

if(!isset($_REQUEST['display'])) $Search->display=10;
else $Search->display=$Search->Form->GetRequestTxtData($_REQUEST['display'], 1);

if(!isset($_REQUEST['page'])) $Search->page=1;
else $Search->page = $Search->Form->GetRequestTxtData($_REQUEST['page'], 1);
//echo '<br />$Search->display='.$Search->display;
if($Search->page>1) $Search->start = ($Search->page-1)*$Search->display;
if(strval($Search->page)=='all') {
    $Search->start = 0;
    $Search->display = 999999;
}

if( !isset ( $_REQUEST['query'] ) ) $query = '';
else {
    $query = addslashes(substr(strip_tags(trim($_REQUEST['query'])), 0,64));
    // cut unnormal symbols
    $query=preg_replace("/[^\w\x7F-\xFF\s\-]/", " ", $query);
    // delete double spacebars
    $query=str_replace(" +", " ", $query);
}

if( !isset ( $_REQUEST['modname'] ) ) $Search->modname = 'all';
else $Search->modname = $_REQUEST['modname'];


if($task==Null){
    $Title = $Page->multi['TXT_FRONT_MOD_SEARCH_ON_SITE'];
}
else{
    $Title = $Page->multi['TXT_FRONT_MOD_SEARCH_RESULT'];
}

$Description = $Page->multi['TXT_FRONT_MOD_SEARCH_ON_SITE'];
$Keywords = $Page->multi['TXT_FRONT_MOD_SEARCH_ON_SITE'];


$Page->SetTitle( $Title );
$Page->SetDescription( $Description );
$Page->SetKeywords( $Keywords );

$Page->searchKeywords = $query;
$Page->h1 = $Page->multi['TXT_FRONT_MOD_SEARCH_RESULT'].' "'.$query.'"';
$Search->Page = $Page;
ob_start();
//echo '<br>$task='.$task.' $query='.$query.' $Search->modname='.$Search->modname;
$flag = TRUE;
if($task=='search' and strlen($query)>=3){
    $flag = FALSE;
    $Search->ip = $_SERVER['REMOTE_ADDR'];
    $Search->query = $query;
    if(defined("USE_CACHE") AND USE_CACHE){
        $Search->result = Cache::instance()->get('Search.'.$Search->modname.'.'.$Search->query.'.'.$Page->lang_id);
    }
    //echo '<br>$Search->modname='.$Search->modname.' $Search->result='.$Search->result;
    if(empty($Search->result)){
        if($Search->modname!='all'){
            $arrRes = $Search->searchSwitch($Search->modname);
        }else{
            $arrRes = array();
            if (defined("MOD_CATALOG") AND MOD_CATALOG){
                $arrRes = $Search->searchSwitch('catalog',$arrRes);
            }
            if (defined("MOD_PAGES") AND MOD_PAGES){
                $arrRes = $Search->searchSwitch('pages',$arrRes);
            }
            if (defined("MOD_NEWS") AND MOD_NEWS){
                $arrRes = $Search->searchSwitch('news',$arrRes);
            }
            if (defined("MOD_ARTICLE") AND MOD_ARTICLE){
                $arrRes = $Search->searchSwitch('articles',$arrRes);
            }
            if (defined("MOD_PUBLIC") AND MOD_PUBLIC){
                $arrRes = $Search->searchSwitch('public',$arrRes);
            }
            if (defined("MOD_GALLERY") AND MOD_GALLERY){
                $arrRes = $Search->searchSwitch('gallery',$arrRes);
            }
            if (defined("MOD_VIDEO") AND MOD_VIDEO){
                $arrRes = $Search->searchSwitch('video',$arrRes);
            }
            if (defined("MOD_DEALERS") AND MOD_DEALERS){
                $arrRes = $Search->searchSwitch('dealers',$arrRes);
            }
        }
        $arrRes = $Search->sortForRelav($arrRes);
    }else{
        $arrRes = $Search->result;
    }
    //var_dump($arrRes);
    if(defined("USE_CACHE") AND USE_CACHE){
        Cache::instance()->set('Search.'.$Search->modname.'.'.$Search->query.'.'.$Page->lang_id, $arrRes);
    }
    if(empty($arrRes)){
        $Page->FrontendPages->Form->ShowTextMessages($Page->multi['SEARCH_NO_RES']);
    }else{
        $Search->ShowSearchInSmall($arrRes);
    }
    $Search->save_search(count($arrRes));
}
$Page->content = ob_get_clean();
$Page->content = $Search->formSearchBig($flag).$Page->content;
$Page->out();
?>