<?
// ================================================================================================
//    System     : PrCSM05
//    Module     : Department
//    Version    : 1.0.0
//    Date       : 01.07.2010
//    Licensed To: Yaroslav Gyryn 
//    Purpose    : Class definition for Department - moule
// ================================================================================================
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/admin/include/defines.inc.php' );
include_once( SITE_PATH.'/modules/mod_department/department.defines.php' );

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


 $department = new DepartmentCtrl($logon->user_id, $module);
 $department->id = $id;
 $department->category = $category;
 $department->status = $status;
 $department->position = $position;
 $department->name = $name;
 $department->short = $short;
 $department->title = $title;
 $department->full = $full;

 $department->task = $task;
 $department->display = $display;
 $department->start = $start;
 $department->sort = $sort;
 $department->fltr = $fltr;
 $department->sel = $sel;
 $department->sel = $sel;
 $department->move=$move;
 $department->keywords = $keywords;
 $department->description = $description;   
 $department->translit = $translit;
 $department->translit_old = $translit_old;
 
 if( !isset( $_REQUEST['fln'] ) ) $department->fln = _LANG_ID;
 else $department->fln = $_REQUEST['fln'];

 $script = 'module='.$department->module.'&display='.$department->display.'&start='.$department->start.'&sort='.$department->sort.'&fltr='.$department->fltr;
 $script = $_SERVER['PHP_SELF']."?$script";

//echo "<br>task=".$task;
switch( $task ) {

    case 'show':      
            $department->show(); 
            break;

    case 'new':     
    case 'edit':      
            $department->edit(); 
            break;

    case 'save':        
            if ( $department->CheckFields()!=NULL ) {
               $department->edit();
               return false;
            }
            if ( $department->save() ){
                $department->UploadImages->SaveImages($department->id);
                echo "<script>window.location.href='$script';</script>";
            }
            break;

    case 'delete':
           $del = $department->del( $id_del );
           if( $del==0 ) $department->Msg->show_msg('_ERROR_DELETE');
           //else echo "<script>window.alert('Deleted OK! ($del records)');</script>";
           echo "<script>window.location.href='$script';</script>";
           break;
               
    case 'cancel':
           echo "<script>window.location.href='$script';</script>";
           break;
           
    case 'up':
           $department->up( $move );
           echo "<script>window.location.href='$script';</script>";
           break;

    case 'down':
           $department->down( $move );
           echo "<script>window.location.href='$script';</script>";
           break;

    case 'preview':
            $department->preview();
            break;
}
?>