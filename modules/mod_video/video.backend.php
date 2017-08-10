<?
// ================================================================================================
//    System     : PrCSM05
//    Module     : Video
//    Version    : 1.0.0
//    Date       : 01.07.2010
//    Licensed To: Yaroslav Gyryn 
//    Purpose    : Class definition for Video - moule
// ================================================================================================
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/admin/include/defines.inc.php' );
include_once( SITE_PATH.'/modules/mod_video/video.defines.php' );

if(!defined("_LANG_ID")) {$pg = new PageAdmin();} 

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part) 
if ( !$pg->logon->isAccessToScript($module)) exit;

if( !isset( $_REQUEST['task'] ) ) $task = 'show';
else $task = $_REQUEST['task'];

if( !isset( $_REQUEST['display'] ) ) $display = 20;
else $display = $_REQUEST['display'];

if( !isset( $_REQUEST['start'] ) ) $start = 0;
else $start = $_REQUEST['start'];

if( !isset( $_REQUEST['sort'] ) ) $sort = NULL;
else $sort = $_REQUEST['sort'];

if( !isset( $_REQUEST['fltr'] ) ) $fltr=NULL;
else $fltr = $_REQUEST['fltr'];

 if( !isset( $_REQUEST['id'] ) ) $id = NULL;
 else $id = $_REQUEST['id'];

 if( !isset( $_REQUEST['dttm'] ) ) $dttm = NULL;
 else $dttm = $_REQUEST['dttm'];

 if( !isset( $_REQUEST['category'] ) ) $category = NULL;
 else $category = $_REQUEST['category'];

 if( !isset( $_REQUEST['status'] ) ) $status = NULL;
 else $status = $_REQUEST['status'];

 if( !isset( $_REQUEST['position'] ) ) $position = NULL;
 else $position = $_REQUEST['position'];

 if( !isset( $_REQUEST['name'] ) ) $name = NULL;
 else $name = $_REQUEST['name'];

 if( !isset( $_REQUEST['short'] ) ) $short = NULL;
 else $short = $_REQUEST['short'];

 if( !isset( $_REQUEST['full'] ) ) $full = NULL;
 else $full = $_REQUEST['full'];

if( !isset( $_REQUEST['sel'] ) ) $sel = NULL;
else $sel = $_REQUEST['sel'];

if( !isset($_REQUEST['id_tag']) ) $id_tag=NULL;
else $id_tag = $_REQUEST['id_tag'];

if( !isset( $_REQUEST['id_del'] ) ) $id_del = NULL;
else $id_del = $_REQUEST['id_del'];

if( !isset( $_REQUEST['move'] ) ) $move = NULL;
else $move = $_REQUEST['move'];

if( !isset( $_REQUEST['title'] ) ) $title = NULL;
else $title = $_REQUEST['title'];

if( !isset( $_REQUEST['keywords'] ) ) $keywords = NULL;
else $keywords = $_REQUEST['keywords'];

if( !isset( $_REQUEST['description'] ) ) $description = NULL;
else $description = $_REQUEST['description'];

if( !isset( $_REQUEST['translit'] ) ) $translit = NULL;
else $translit = $_REQUEST['translit']; 

if( !isset( $_REQUEST['translit_old'] ) ) $translit_old = NULL;
else $translit_old = $_REQUEST['translit_old']; 

if( isset($_REQUEST['saveimg']) ) $task='saveimg';
if( isset($_REQUEST['updimg']) ) $task='updimg'; 
if( isset($_REQUEST['delimg']) ) $task='delimg'; 
if( isset($_REQUEST['cancel']) ) $task='cancel';

$Video = new VideoCtrl($logon->user_id, $module);
$Video->id = $id;
$Video->id_tag = $id_tag;
$Video->dttm = $dttm;
$Video->category = $category;
$Video->status = $status;
$Video->position = $position;
$Video->name = $name;
$Video->short = $short;
$Video->title = $title;
$Video->full = $full;

$Video->task = $task;
$Video->display = $display;
$Video->start = $start;
$Video->sort = $sort;
$Video->fltr = $fltr;
$Video->sel = $sel;
$Video->sel = $sel;
$Video->move=$move;
$Video->keywords = $keywords;
$Video->description = $description;
$Video->translit=$translit;
$Video->translit_old=$translit_old;

if( $task=='savereturn') {$task='save'; $action='return';}
else $action=NULL;

if( !isset( $_REQUEST['fln'] ) ) $Video->fln = _LANG_ID;
else $Video->fln = $_REQUEST['fln'];

$script = 'module='.$Video->module.'&display='.$Video->display.'&start='.$Video->start.'&sort='.$Video->sort.'&fltr='.$Video->fltr;
$script = $_SERVER['PHP_SELF']."?$script";

switch( $task ) {
    case 'show':      
            $Video->show(); 
            break;

    case 'new':     
    case 'edit':      
            $Video->edit(); 
            break;

    case 'save':        
        if ( $Video->CheckFields()!=NULL ) {
           $Video->edit();
           return false;
        }
        if ( $Video->save() ){
            if( $Video->UploadImages->SaveImages($Video->id) and  $Video->UploadVideo->SaveVideos($Video->id) ){
                if( $action=='return' ){
                    echo "<script>window.location.href='".$script."&task=edit&id=".$Video->id."';</script>";
                }else{
                    echo "<script>window.location.href='$script';</script>";
                }
            }
            else{
                if( $action=='return' ){
                    echo "<script>window.location.href='".$script."&task=edit&id=".$Video->id."';</script>";
                }else{
                    echo "<script>window.location.href='$script';</script>";
                }
            }
        }
        break;

    case 'delete':
           $del = $Video->del( $id_del );
           if( $del==0 ) $Video->Msg->show_msg('_ERROR_DELETE');
           //else echo "<script>window.alert('Deleted OK! ($del records)');</script>";
           //echo "<script>window.location.href='$script';</script>";
           break;
    case 'cancel':
           echo "<script>window.location.href='$script';</script>";
           break;
    case 'up':
            $Video->up( $move );
            echo "<script>window.location.href='$script';</script>";
            break;

    case 'down':
            $Video->down( $move );
            echo "<script>window.location.href='$script';</script>";
            break;

    case 'preview':
                    $Video->preview();
                    break;
}
?>