<?
/**
* mailer.backend.php
* script for all actions with with sending created letters
* @package Mailer Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 29.08.2013
* @copyright (c) 2010+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/modules/mod_mailer/mailer.defines.php' );

if(!defined("_LANG_ID")) {
    $pg = check_init('PageAdmin', 'PageAdmin');
}

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//echo '<br>$module='.$module;
//Blocking to execute a script from outside (not Admin-part)
if(!$pg->logon->isAccessToScript($module)) exit;

//echo '<br>111111111111';
if( !isset( $_REQUEST['id'] ) ) $id = NULL;
else $id = $_REQUEST['id'];

if( !isset( $_REQUEST['task'] ) ) $task = 'show';
else $task = $_REQUEST['task'];

if( !isset( $_REQUEST['start'] ) ) $start = 0;
else $start = $_REQUEST['start'];

if( !isset( $_REQUEST['display'] ) ) $display = 50;
else $display = $_REQUEST['display'];

if( !isset( $_REQUEST['sort'] ) ) $sort = NULL;
else $sort = $_REQUEST['sort'];

if( !isset( $_REQUEST['fltr'] ) ) $fltr=NULL;
else $fltr = $_REQUEST['fltr'];

if( !isset( $_REQUEST['fln'] ) ) $fln = _LANG_ID;
else $fln = $_REQUEST['fln'];

if( !isset( $_REQUEST['id_del'] ) ) $id_del=NULL;
else $id_del = $_REQUEST['id_del'];

if( !isset( $_REQUEST['status'] ) ) $status=NULL;
else $status = $_REQUEST['status'];

if( !isset( $_REQUEST['dt'] ) ) $dt=NULL;
else $dt = $_REQUEST['dt'];

if( !isset( $_REQUEST['sbj'] ) ) $sbj=NULL;
else $sbj = $_REQUEST['sbj'];

if( !isset( $_REQUEST['body'] ) ) $body=NULL;
else $body = $_REQUEST['body'];

$Mailer = new Mailer($pg->logon->user_id, $module);
$Mailer->id = $id;
$Mailer->module = $module;
$Mailer->user_id = $pg->logon->user_id;
$Mailer->task = $task;
$Mailer->start = $start;
$Mailer->display = $display;
$Mailer->sort = $sort;
$Mailer->fltr = $fltr;
$Mailer->fln = $fln;

$Mailer->status = $status;
$Mailer->dt = $dt;
$Mailer->sbj = $Mailer->GetRequestData($sbj);
$Mailer->body = $Mailer->GetRequestData($body);


$script_ajax = 'module='.$Mailer->module.'&display='.$Mailer->display.'&start='.$Mailer->start.'&sort='.$Mailer->sort.'&fltr='.$Mailer->fltr;
$script = "index.php?".$script_ajax;

$Mailer->script_ajax = $script_ajax;
$Mailer->script = $script;
//phpinfo();
//print_r($_REQUEST);
//echo '<br>$task='.$task.' $Mailer->id ='.$Mailer->id;
switch( $task ) {
    case 'show':
        $Mailer->ShowDispatch();
        break;
    case 'new':
        $Mailer->EditDispatch();
        break;
    case 'edit':
        $Mailer->EditDispatch();
        break;
    case 'save':
        //phpinfo();
        if( $Mailer->CheckDispatch()!=NULL ) {
            $Mailer->EditDispatch();
            return false;
        }
        //phpinfo();
        $Mailer->SaveDispatch();
        //$Mailer->ShowDispatch();
        echo "<script>window.location.href='$script';</script>";
        break;
    case 'delete':
        if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
        else $id_del = $_REQUEST['id_del'];
        if ( !empty($id_del) ) {
           $del=$Mailer->DelDispatch($id_del);
           if ( $del > 0 ) echo "<script>window.alert('".$Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
           else $Msg->show_msg('_ERROR_DELETE');
        }
        else $Msg->show_msg('_ERROR_SELECT_FOR_DEL');
        echo "<script>window.location.href='$script';</script>";
        break;
    case 'del_file':
        if( !isset($_REQUEST['id_file']) ) $id_file=NULL;
        else $id_file = $_REQUEST['id_file'];
        if ( !empty($id_file) ) {
           $res=$Mailer->DelFile($id_file);
           if ( !$res ) $Msg->show_msg('_ERROR_DELETE');
        }
        $Mailer->EditDispatch();
        break;
    case 'cancel':
        echo "<script>window.location.href='$script';</script>";
        break;
    case 'start':
        if( !$Mailer->ChangeDispatchStatus($Mailer->id, 1) ) {echo 'Ощибка<br/>'; $Mailer->ControlDispatchStatus($Mailer->id, 0);}
        else {
            $Mailer->MakeDispatch();
            $Mailer->ControlDispatchStatus($Mailer->id);
        }
        break;
    case 'pause':
        if( !$Mailer->ChangeDispatchStatus($Mailer->id, 4) ) {echo 'Ощибка<br/>'; $Mailer->ControlDispatchStatus($Mailer->id, 0);}
        else $Mailer->ControlDispatchStatus($Mailer->id, 4);
        break;
    case 'stop':
        if( !$Mailer->ChangeDispatchStatus($Mailer->id, 3) ) {echo 'Ощибка<br/>'; $Mailer->ControlDispatchStatus($Mailer->id, 0);}
        else $Mailer->ControlDispatchStatus($Mailer->id, 3);
        break;
    case 'continue':
        if( !$Mailer->ChangeDispatchStatus($Mailer->id, 1) ) {echo 'Ощибка<br/>'; $Mailer->ControlDispatchStatus($Mailer->id, 0);}
        else {
            $Mailer->MakeDispatch();
            $Mailer->ControlDispatchStatus($Mailer->id);
        }
        break;
    case 'test_send':
        $Mailer->showResult=true;
        $Mailer->MakeDispatch();
        $Mailer->ShowDispatch();
        break;
    case 'is_can_start':
        echo $Mailer->isCanStart();
        break;
    default:
        ?><h1 class="err">Page Not Exist</h1><?
        //phpinfo();
        break;
}
