<?
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_video/video.defines.php' );

$Page = check_init('PageUser', 'PageUser');

if(!isset ($Page->Video))
    $Page->Video = check_init('VideoLayout', 'VideoLayout');
$Video = &$Page->Video;

if( !isset( $_REQUEST['task'] ) ) $task = NULL;
else $task = $_REQUEST['task'];

if( !isset( $_REQUEST['start'] ) ) $start = 0;
else $start = $_REQUEST['start'];

if( !isset( $_REQUEST['sort'] ) ) $sort = NULL;
else $sort = $_REQUEST['sort'];

if( !isset( $_REQUEST['display'] ) ) $display = 8;
else $display = $_REQUEST['display'];

if(!isset($_REQUEST['page'])) $page=1;
else $page=$_REQUEST['page'];
if($page>1) $start = ($page-1)*$display;
if($page=='all') {
   $start = 0;
   $display = 999999;
}

if( !isset( $_REQUEST['cat'] ) ) $cat = NULL; // category cod
else {
    $cat = $_REQUEST['cat'];
    $idCat= $Video->GetCategoryIdByTranslit($cat);

    if(!$idCat)
        $Page->Set_404();
    $Video->fltr = ' AND `category`='.$idCat;
    $Video->cat  = $idCat;
}

if( !isset( $_REQUEST['position'] ) ) $position = NULL;
else {
    $position = $_REQUEST['position'];
    $idPosition= $Video->GetPositionIdByTranslit($position, $idCat);

    if(!$idPosition)
        $Page->Set_404();
    $Video->id = $idPosition;
}

$Video->task = $task;
$Video->display = $display;
$Video->page = $page;
$Video->start = $start;
$Video->sort = $sort;

if(isset ($Page->FrontendPages))
    $FrontendPages = &$Page->FrontendPages;
else
    $FrontendPages = check_init('FrontendPages', 'FrontendPages');

$FrontendPages->lang_id = _LANG_ID;
$FrontendPages->page = PAGE_VIDEO ;

$Video->SetMetaData($FrontendPages->page);

if ( empty($Video->title) ) $Title = $Video->multi['TXT_VIDEO_TITLE'];
else $Title = $Video->title;
if ( empty($Video->description) ) $Description = $Video->multi['TXT_VIDEO_TITLE'];
else $Description = $Video->description;
if ( empty($Video->keywords) ) $Keywords = $Video->multi['TXT_VIDEO_TITLE'];
else $Keywords = $Video->keywords;

$Page->SetTitle( $Title );
$Page->SetDescription( $Description );
$Page->SetKeywords( $Keywords );

ob_start();
$title_content = null;
/*if($Video->task=='position'){
    $title_content = $Video->Spr->GetNameByCod(TblModVideoTxt, $Video->id, $Video->lang_id, 1);
}
elseif($Video->task=='last'){
    $title_content = $Video->multi['TXT_FRONT_TITLE_LATEST'];
}
elseif($Video->task=='showall'){
    if( !empty($Video->category)) $title_content = $Video->Spr->GetNameByCod(TblModVideoCat, $Video->cat, $Video->lang_id, 1);
    else $title_content = $Video->multi['TXT_ALL_VIDEO'];
}
else*/{
    $title_content = $Video->multi['TXT_VIDEO_TITLE'];
}
//echo '<br/>$task='.$Video->task.'<br/> $cat='.$cat.'<br/> $art='.$art.'<br/> $page='.$page;
$Page->Form->WriteContentHeader($title_content,'icoVideo',false);
//echo '$Video->task ='.$Video->task;
switch( $Video->task ){
    case 'last':
    case 'showall':
        $Video->fltr = $Video->fltr." AND `status`='a'";
        $Video->ShowVideosByPages();
        break;

    case 'cat':
        if($cat!=NULL) {
            $Video->fltr = $Video->fltr." AND `status`='a'";
            $Video->ShowVideosByPages();
        }
        else
            $Video->ShowVideoCat();
        break;

    case 'position':
        $Video->ShowVideoFull();
        /*?><div class="banner"><?$Page->Banner->GetBanner(15,1); //Детально відео, фото 1?></div><?
        $Video->VideoCatLast($Video->cat, 4);
        if(!isset($Video->Comments))
            $Video->Comments = new FrontComments($Video->module, $Video->id);
        $Video->Comments->ShowCommentsByModuleAndItem();
        $Video->Comments->VkontakteComments();
        $Video->Comments->FacebookComments();
        ?><div class="banner"><?$Page->Banner->GetBanner(16,1); //Детально відео, фото 2?></div><?*/
        break;

    case 'add':
        $Video->ShowAddForm();
        break;

    /*case 'save_data':
        //print_r($_REQUEST);
        $Video->CheckNewData();
        if($Video->Err!='') $Video->ShowAddForm();
        else {
            //$res = $Video->SaveData();
            if(!$Video->SaveData()) echo "<p class=err> Данные НЕ сохранены!</p>";
            else echo "<p>Предложение добавлено. После проверки модератором станет доступным.</p>";
        }
        break;        */

    default:
      //$Video->ShowVideoCat();
        $Video->ShowVideosByPages();
}
//$Video->ShowVideoTask();
//$Video->ShowVideoCat();
/*?>
    </div>
</div>*/
$Page->Form->WriteContentFooter();
$Page->content = ob_get_clean();
$Page->out();