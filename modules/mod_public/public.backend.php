<?php
/**
* public.backend.php  
* script for all actions with Publications
* @package Publications Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 24.02.2011
* @copyright (c) 2010+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/modules/mod_public/public.defines.php' );

if(!defined("_LANG_ID")) {$pg = new PageAdmin();} 

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part) 
if ( !$pg->logon->isAccessToScript($module)) exit;

if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $task='show';
else $task=$_REQUEST['task'];

 //echo '<br> $task='.$task;

if( !isset( $_REQUEST['fln'] ) ) $fln = _LANG_ID;
else $fln = $_REQUEST['fln'];

if( !isset( $_REQUEST['fltr'] ) ) $fltr = NULL; 
else $fltr = $_REQUEST['fltr'];

if( !isset( $_REQUEST['fltr2'] ) ) $fltr2 = NULL;
else $fltr2 = $_REQUEST['fltr2'];

if( !isset( $_REQUEST['fltr3'] ) ) $fltr3 = NULL;
else $fltr3 = $_REQUEST['fltr3'];

if( !isset( $_REQUEST['srch'] ) ) $srch = NULL;
else $srch = $_REQUEST['srch'];

if( !isset( $_REQUEST['srch2'] ) ) $srch2 = NULL; 
else $srch2 = $_REQUEST['srch2'];

if(!isset($_REQUEST['sort'])) $sort=NULL;
else $sort=$_REQUEST['sort'];

if(!isset($_REQUEST['start'])) $start=0;
else $start=$_REQUEST['start'];

if(!isset($_REQUEST['display']) || empty($srch) ) $display=20;
else $display=$_REQUEST['display'];

if( !isset( $_REQUEST['sel'] ) ) $sel = NULL;
else $sel = $_REQUEST['sel'];

if( !isset($_REQUEST['id']) ) $id=NULL;
else $id = $_REQUEST['id'];

if( !isset($_REQUEST['group']) ) $group=NULL;
else $group = $_REQUEST['group'];

if( !isset($_REQUEST['categ']) ) $categ=NULL;
else $categ = $_REQUEST['categ'];

if( !isset($_REQUEST['title']) ) $title=NULL;
else $title = $_REQUEST['title'];

if( !isset($_REQUEST['text']) ) $text=NULL;
else $text = $_REQUEST['text'];

if( !isset($_REQUEST['contact']) ) $contact=NULL;
else $contact = $_REQUEST['contact'];

if( !isset($_REQUEST['dt']) ) $dt=NULL;
else $dt = $_REQUEST['dt'];

if( !isset($_REQUEST['status']) ) $status=NULL;
else $status = $_REQUEST['status'];

if( !isset( $_REQUEST['img'] ) ) $img=NULL;
else $img = $_REQUEST['img'];

if( $task=='savereturn') {$task='save'; $action='return';}
else $action=NULL;

 $Public = new Public_ctrl($pg->logon->user_id, $module, 10, $sort, $start, '100%');
 $Public->display = $display;
 $Public->sort = $sort;
 $Public->start = $start;
 $Public->fln = $fln;
 $Public->srch = $srch;
 $Public->srch2 = $srch2;
 $Public->fltr = $fltr;
 $Public->fltr2 = $fltr2;
 $Public->fltr3 = $fltr3;
 $Public->sel = $sel; 
                   
 $Public->id=$id;
 $Public->categ=$categ;
 $Public->group=$group; 
 $Public->title = addslashes(strip_tags(trim($title)));
 $Public->text = addslashes(strip_tags(trim($text)));
 $Public->contact = addslashes(strip_tags(trim($contact)));
 $Public->dt=$dt;
 $Public->status=$status;
 $Public->img=$img;
 $Public->time = time(); 


 $Public->script=$_SERVER['PHP_SELF']."?module=$Public->module&display=$Public->display&start=$Public->start&sort=$Public->sort&fltr=$Public->fltr&fltr2=$Public->fltr2&fltr3=$Public->fltr3&srch=$Public->srch&srch2=$Public->srch2";
 
 //$Public->AutoDeletingPublications(); 
 //echo '$task='.$task;
 switch( $task ) {
    case 'show':      $Public->show();
                      break;
    case 'edit':
                      if (!$Public->edit( $id )) echo "<script>window.location.href='$Public->script';</script>";
                      break;
    case 'new':       $Public->edit( NULL );
                      break;
    case 'save':
                      if ( $Public->CheckFields( $id )!=NULL ) {
                          $Public->edit( $id );
                          return false;
                      }
                      if ( $Public->save( $id ) ){
                        if( $action=='return' ) echo "<script>window.location.href='".$Public->script."&task=edit&id=".$Public->id."';</script>";
                        else echo "<script>window.location.href='$Public->script';</script>";
                      }
                      break;
    case 'delete':
                      if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
                      else $id_del = $_REQUEST['id_del'];
                      if ( !empty($id_del) ) {
                         $del=$Public->del( $id_del );
                         if ( $del > 0 ) echo "<script>window.alert('".$pg->Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
                         else $pg->Msg->show_msg('_ERROR_DELETE');
                      }
                      else $pg->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
                      echo "<script>window.location.href='$Public->script';</script>";
                      break;
    case 'cancel':
                      echo "<script>window.location.href='$Public->script';</script>";
                      break;
    case 'setShow':
        $Public->showSet();
        break;
    case 'setSave':
        $res = $Public->saveSet();
        if(!$res) echo '<div class="red">'.$Public->multi['_NOT_SAVE'].'</div>';
        else echo '<div style="color: green;">'.$Public->multi['_OK_SAVE'].'</div>';
        $Public->showSet();
        break;
 }

?>
