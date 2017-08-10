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

//============================================================================================
// START
// Blocking to execute a script from outside (not Admin-part) 
//============================================================================================
$Msg = new ShowMsg();
$goto = "http://".NAME_SERVER."/admin/index.php?logout=1";
//echo '<br>$goto='.$goto;
if ( !isset($_SESSION[ 'session_id']) OR empty($_SESSION[ 'session_id']) OR empty( $module ) ) {
     //$Msg->show_msg( '_NOT_AUTH' );
     //return false;
     ?><script>window.location.href="<?=$goto?>";</script><?;
}
$logon = new  Authorization();
if (!$logon->LoginCheck()) {
    ?><script>window.location.href="<?=$goto?>";</script><?; 
}
//=============================================================================================
// END
//=============================================================================================

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

 if( !isset( $_REQUEST['email'] ) ) $email = NULL;
 else $email = $_REQUEST['email'];

 if( !isset( $_REQUEST['status'] ) ) $status = NULL;
 else $status = $_REQUEST['status'];

 if( !isset( $_REQUEST['position'] ) ) $position = NULL;
 else $position = $_REQUEST['position'];

 if( !isset( $_REQUEST['name'] ) ) $name = NULL;
 else $name = $_REQUEST['name'];

 if( !isset( $_REQUEST['post'] ) ) $post = NULL;
 else $post = $_REQUEST['post'];
 
 if( !isset( $_REQUEST['work_time'] ) ) $work_time = NULL;
 else $work_time = $_REQUEST['work_time'];

if( !isset( $_REQUEST['sel'] ) ) $sel = NULL;
else $sel = $_REQUEST['sel'];

if( !isset( $_REQUEST['id_del'] ) ) $id_del = NULL;
else $id_del = $_REQUEST['id_del'];

if( !isset( $_REQUEST['move'] ) ) $move = NULL;
else $move = $_REQUEST['move'];

if( isset($_REQUEST['saveimg']) ) $task='saveimg';
if( isset($_REQUEST['updimg']) ) $task='updimg'; 
if( isset($_REQUEST['delimg']) ) $task='delimg'; 
if( isset($_REQUEST['cancel']) ) $task='cancel';

 $departmentDoctor = new DepartmentDoctorCtrl($logon->user_id, $module);
 $departmentDoctor->id = $id;
 $departmentDoctor->category = $category;
 $departmentDoctor->status = $status;
 $departmentDoctor->email = $email;
 $departmentDoctor->name = $name;
 $departmentDoctor->post = $post;
 $departmentDoctor->work_time = $work_time;
 $departmentDoctor->position = $position;

 $departmentDoctor->task = $task;
 $departmentDoctor->display = $display;
 $departmentDoctor->start = $start;
 $departmentDoctor->sort = $sort;
 $departmentDoctor->fltr = $fltr;
 $departmentDoctor->sel = $sel;
 $departmentDoctor->move=$move;
 
 if( !isset( $_REQUEST['fln'] ) ) $departmentDoctor->fln = _LANG_ID;
 else $departmentDoctor->fln = $_REQUEST['fln'];

 $script = 'module='.$departmentDoctor->module.'&display='.$departmentDoctor->display.'&start='.$departmentDoctor->start.'&sort='.$departmentDoctor->sort.'&fltr='.$departmentDoctor->fltr;
 $script = $_SERVER['PHP_SELF']."?$script";

//echo "<br>task=".$task;
switch( $task ) {

    case 'show':      $departmentDoctor->show(); break;

    case 'new':     
    case 'edit':      $departmentDoctor->edit(); break;

    case 'save':        
        if ( $departmentDoctor->CheckFields()!=NULL ) {
           $departmentDoctor->edit();
           return false;
        }
        if ( $departmentDoctor->save() ){
            //$DepartmentDoctor->UploadImages->SaveImages($departmentDoctor->id);
            echo "<script>window.location.href='$script';</script>";
        }
        break;

    case 'delete':
                       $del = $departmentDoctor->del( $id_del );
                       if( $del==0 ) $departmentDoctor->Msg->show_msg('_ERROR_DELETE');
                       //else echo "<script>window.alert('Deleted OK! ($del records)');</script>";
                       echo "<script>window.location.href='$script';</script>";
                       break;
    case 'cancel':
                       echo "<script>window.location.href='$script';</script>";
                       break;
    case 'up':
                    $departmentDoctor->up( $move );
                    echo "<script>window.location.href='$script';</script>";
                    break;

    case 'down':
                    $departmentDoctor->down( $move );
                    echo "<script>window.location.href='$script';</script>";
                    break;
}
?>