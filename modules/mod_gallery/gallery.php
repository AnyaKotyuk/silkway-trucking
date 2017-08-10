<?
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_gallery/gallery.defines.php' );

$Page = check_init('PageUser', 'PageUser');
if(empty($Page->FrontendPages))
    $Page->FrontendPages = check_init('FrontendPages', 'FrontendPages');

if(!isset ($Page->Gallery))
    $Page->Gallery = check_init('GalleryLayout', 'GalleryLayout');
$Gallery = &$Page->Gallery;

if( !isset( $_REQUEST['task'] ) ) $task = '';
else $task = $_REQUEST['task'];

if( !isset( $_REQUEST['start'] ) ) $start = 0;
else $start = $_REQUEST['start'];

if( !isset( $_REQUEST['sort'] ) ) $sort = NULL;
else $sort = $_REQUEST['sort'];

if( !isset( $_REQUEST['display'] ) ) $display = 6;
else $display = $_REQUEST['display'];

if(!isset($_REQUEST['page'])) $page=1;
else $page=$_REQUEST['page'];
if($page>1) $start = ($page-1)*$display;
if($page=='all') {
   $start = 0;
   $display = 999999;
}
$getNewLink = false;
if( !isset( $_REQUEST['cat'] ) ) $cat = NULL; // category cod
else {
    $Gallery->lang_id_for_redirect = $Gallery->lang_id;
    $cat = $_REQUEST['cat'];
    $idCat= $Gallery->GetCategoryIdByTranslit($cat);

    if(!$idCat)
        $Page->Set_404();
    $Gallery->fltr = ' AND `category`='.$idCat;
    $Gallery->cat  = $idCat;
    if($Gallery->lang_id_for_redirect != $Gallery->lang_id) $getNewLink = true;
}
//echo '$getNewLink='.$getNewLink.' $Gallery->cat='.$Gallery->cat;
if( !isset( $_REQUEST['position'] ) ) $position = NULL;
else {
    $Gallery->lang_id_for_redirect = $Gallery->lang_id;
    $position = $_REQUEST['position'];
    $idPosition= $Gallery->GetPositionIdByTranslit($position, $idCat);
    if(!$idPosition) $Page->Set_404();
    $Gallery->id = $idPosition;
    if($Gallery->lang_id_for_redirect != $Gallery->lang_id) $getNewLink = true;
}

if($getNewLink){
    $new_link = $Gallery->Link($Gallery->cat,$Gallery->id,true);
}
//echo '$new_link='.$new_link.' $Gallery->id='.$Gallery->id;

//необходим для 301-го редиректа с УРЛ одной язsковой версии на корректную другую
if(!empty($new_link)){
    //echo '<br>$new_link='.$new_link;
    header ('HTTP/1.1 301 Moved Permanently');
    header ('Location: '.$new_link);
    exit();
}

$Gallery->task = $task;
$Gallery->display = $display;
$Gallery->page = $page;
$Gallery->start = $start;
$Gallery->sort = $sort;

$Page->FrontendPages->lang_id = _LANG_ID;
$Page->FrontendPages->page = PAGE_GALLERY;

$Gallery->SetMetaData($Page->FrontendPages->page);

if ( empty($Gallery->title) ) $Title = $Gallery->multi['TXT_GALLERY_TITLE'];
else $Title = $Gallery->title;
if ( empty($Gallery->description) ) $Description = $Gallery->multi['TXT_GALLERY_TITLE'];
else $Description = $Gallery->description;
if ( empty($Gallery->keywords) ) $Keywords = $Gallery->multi['TXT_GALLERY_TITLE'];
else $Keywords = $Gallery->keywords;

$Page->SetTitle( $Title );
$Page->SetDescription( $Description );
$Page->SetKeywords( $Keywords );
$Page->SetMultiUrls($Gallery->getMultiUrls());

ob_start();
$Gallery->PageUser=$Page;
$show_link = true;
if(empty($Gallery->task)) $show_link = false;
$path_page = $Page->FrontendPages->ShowPath($Page->FrontendPages->page,NULL,$show_link);
$path = $Gallery->showPath($path_page);
$Gallery->PageUser->breadcrumb = $path;

switch( $Gallery->task ){
    case 'last':
    case 'showall':
        $Gallery->fltr = $Gallery->fltr." AND `status`='a'";
        $Gallery->ShowGallerysByPages();
        break;

    case 'cat':
        if($cat!=NULL) {
            $Gallery->fltr = $Gallery->fltr." AND `status`='a'";
            $Gallery->ShowGallerysByPages();
        }
        else
            $Gallery->ShowGalleryCat();
        break;

    case 'position':
        $Gallery->ShowGalleryFull();
        /*?><div class="banner"><?$Page->Banner->GetBanner(15,1); //Детально відео, фото 1?></div><?
        $Gallery->GalleryCatLast($Gallery->cat, 4);

        if(!isset($Gallery->Comments))
            $Gallery->Comments = new FrontComments($Gallery->module, $Gallery->id);
        $Gallery->Comments->ShowCommentsByModuleAndItem();
        $Gallery->Comments->VkontakteComments();
        $Gallery->Comments->FacebookComments();
        ?><div class="banner"><?$Page->Banner->GetBanner(16,1); //Детально відео, фото 2?></div><?*/
        break;

    case 'add':
        $Gallery->ShowAddForm();
        break;

    /*case 'save_data':
        print_r($_REQUEST);
        $Gallery->CheckNewData();
        if($Gallery->Err!='') $Gallery->ShowAddForm();
        else {
            //$res = $Gallery->SaveData();
            if(!$Gallery->SaveData()) echo "<p class=err> Данные НЕ сохранены!</p>";
            else echo "<p>Предложение добавлено. После проверки модератором станет доступным.</p>";
        }
        break;*/

    default:
        $Gallery->fltr = $Gallery->fltr." AND `status`='a'";
        $Gallery->ShowGallerysByPages();

}
//$Gallery->ShowGalleryTask();
$Page->content = ob_get_clean();
$Page->out();
?>