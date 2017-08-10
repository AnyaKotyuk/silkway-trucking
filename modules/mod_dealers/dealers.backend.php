<?php
/**
* dealers.backend.php  
* script for all actions with Dealers
* @package Dealers Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 24.02.2011
* @copyright (c) 2010+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/modules/mod_dealers/dealers.defines.php' );

if(!defined("_LANG_ID")) {$pg = new PageAdmin();} 

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part) 
if ( !$pg->logon->isAccessToScript($module)) exit;

if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $task='show';
else $task=$_REQUEST['task'];

if( isset($_REQUEST['delimg']) and ( !empty($_REQUEST['delimg'])) ) {
    $task='delimg';
    $delimg = $_REQUEST['delimg'];
}
else $item_img = NULL;

if( !isset( $_REQUEST['fln'] ) ) $fln= _LANG_ID;
else $fln = $_REQUEST['fln']; 

if( !isset( $_REQUEST['fltr'] ) ) $fltr= NULL;
else $fltr = $_REQUEST['fltr'];
 
if( !isset( $_REQUEST['fltr2'] ) ) $fltr2 = NULL; 
else $fltr2 = $_REQUEST['fltr2'];

if( !isset( $_REQUEST['srch'] ) ) $srch = NULL;
else $srch = $_REQUEST['srch'];

if( !isset( $_REQUEST['srch2'] ) ) $srch2 = NULL; 
else $srch2 = $_REQUEST['srch2'];
//echo '<br> srch='.$srch;

if( !isset( $_REQUEST['sort'] ) ) $sort = NULL;  
else $sort = $_REQUEST['sort'];

if( !isset( $_REQUEST['start'] ) ) $start = 0;  
else $start = $_REQUEST['start'];

if( !isset($_REQUEST['display']) || ( isset($srch) and empty($srch)) ) $display=20;
else $display=$_REQUEST['display'];


if( !isset( $_REQUEST['id'] ) ) $id = NULL;
else $id = $_REQUEST['id']; 

if( !isset( $_REQUEST['name'] ) ) $name = NULL;
else $name = $_REQUEST['name'];

if( !isset( $_REQUEST['name2'] ) ) $name2 = NULL;
else $name2 = $_REQUEST['name2'];


if( !isset( $_REQUEST['tel'] ) ) $tel = NULL;
else $tel = $_REQUEST['tel']; 

if( !isset( $_REQUEST['email'] ) ) $email = NULL;
else $email = $_REQUEST['email']; 


if( !isset( $_REQUEST['cenntral_ofis'] ) ) $cenntral_ofis = 0;
else $cenntral_ofis= 1; 
//echo '$tel='.$tel.' $email='.$email;die;

if( !isset( $_REQUEST['group_d'] ) ) $group_d = 0;
else $group_d = $_REQUEST['group_d']; 

if( !isset( $_REQUEST['city_d'] ) ) $city_d = 0;
else $city_d = $_REQUEST['city_d']; 

if( !isset( $_REQUEST['content'] ) ) $content = NULL;
else $content = $_REQUEST['content'];

if( !isset( $_REQUEST['full'] ) ) $full = NULL;
else $full = $_REQUEST['full'];

if( !isset( $_FILES['filename'] ) ) $filename = NULL;
else $filename = $_FILES['filename']; 

if( !isset( $_REQUEST['visible'] ) ) $visible = NULL;
else $visible = $_REQUEST['visible']; 

if( !isset( $_REQUEST['move'] ) ) $move = NULL;
else $move = $_REQUEST['move']; 

if( !isset( $_REQUEST['img'] ) ) $img = NULL;
else $img = $_REQUEST['img'];

if( !isset( $_REQUEST['ko_x'] ) ) $ko_x = NULL;
else $ko_x = $_REQUEST['ko_x'];

if( !isset( $_REQUEST['ko_y'] ) ) $ko_y = NULL;
else $ko_y = $_REQUEST['ko_y'];

if( !isset( $_REQUEST['main_x'] ) ) $main_x = 0;
else $main_x = $_REQUEST['main_x'];

if( !isset( $_REQUEST['main_y'] ) ) $main_y = 0;
else $main_y = $_REQUEST['main_y'];

 $Dealer = new Dealer($pg->logon->user_id, $module, 10, $sort, $start, '100%');
 $Dealer->display = $display;
 $Dealer->sort = $sort;
 $Dealer->start = $start;
 $Dealer->fln = $fln;
 $Dealer->srch = $srch;
 $Dealer->srch2 = $srch2;
 $Dealer->fltr = $fltr;
 $Dealer->fltr2 = $fltr2;
 
 $Dealer->id=$id; 
 $Dealer->name=$name;
$Dealer->name2=$name2;
 $Dealer->tel=$tel;
 $Dealer->email=$email;
 $Dealer->cenntral_ofis=$cenntral_ofis;
 $Dealer->group_d=$group_d;
 $Dealer->city_d=$city_d;
 $Dealer->content=$content;
 $Dealer->full=$full;
 $Dealer->visible=$visible;
 $Dealer->img=$img;
 $Dealer->move=$move;  
 $Dealer->ko_x=$ko_x;
 $Dealer->ko_y=$ko_y;
 $Dealer->main_x=$main_x;
 $Dealer->main_y=$main_y;

 $Dealer->script=$_SERVER['PHP_SELF']."?module=$Dealer->module";
 if($Dealer->display!=NULL)$Dealer->script.="&display=$Dealer->display";
 if($Dealer->start!=NULL)$Dealer->script.="&start=$Dealer->start";
 if($Dealer->sort!=NULL)$Dealer->script.="&sort=$Dealer->sort";
 if($Dealer->fltr!=NULL)$Dealer->script.="&fltr=$Dealer->fltr";
 if($Dealer->fltr2!=NULL)$Dealer->script.="&fltr2=$Dealer->fltr2";
 if($Dealer->srch!=NULL)$Dealer->script.="&srch=$Dealer->srch";
 if($Dealer->srch2!=NULL)$Dealer->script.="&srch2=$Dealer->srch2";
    //echo '$task='.$task;
    if ($task=='savereturn') {
        $task='save';
        $return=1;
    }
    else{
        $return=0;
    }
 switch( $task ) {
    case 'show':      $Dealer->show();
                      break;
    case 'edit':
                      if (!$Dealer->edit()) echo "<script>window.location.href='$Dealer->script';</script>";
                      break;
    case 'new':       $Dealer->edit();
                      break;
    case 'save':
//                      if ( $Dealer->CheckFields()!=NULL ) {
//                          $Dealer->edit();
//                          return false;
//                      }
                      //phpinfo();
                      if ( !empty($filename['name']) ) {
                          $filename_name = $filename['name'];
//                          echo '<br> $filename='.$filename.' $filename_name='.$filename_name.' $_FILES["filename"]["name"]='.$_FILES["filename"]["name"];
                          $ext = substr($filename_name,1 + strrpos($filename_name, "."));
                          $Dealer->img = time().'.'.$ext;
                          $Dealer->img=$filename_name;
                          if ( !file_exists (Dealer_Img_Full_Path) ) mkdir(Dealer_Img_Full_Path,0777); 
                          else chmod(Dealer_Img_Full_Path,0777);
                          $uploaddir = Dealer_Img_Full_Path.$Dealer->img; 
                          if ( !copy($filename['tmp_name'],$uploaddir) ) {
                              chmod(Dealer_Img_Full_Path,0755);
                              $Dealer->Err = $Dealer->Err.$Dealer->Msg->show_text('MSG_ERR_FILE_MOVE', TblSysTxt).'<br>';
                              $Dealer->edit();
                              return false;
                          }
                          chmod(Dealer_Img_Full_Path,0755);
                      }  
                      if ( $Dealer->save() ){
                            if($return==1){
                               $Dealer->script.='&task=edit&id='.$id; 
                            }
                            echo "<script>window.location.href='$Dealer->script';</script>";
                      }
                      break;
    case 'delete':
                      if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
                      else $id_del = $_REQUEST['id_del'];
                      if ( !empty($id_del) ) {
                         $del=$Dealer->del( $id_del );
                         if ( $del > 0 ) echo "<script>window.alert('".$pg->Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
                         else $pg->Msg->show_msg('_ERROR_DELETE');
                      }
                      else $pg->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
                      echo "<script>window.location.href='$Dealer->script';</script>";
                      break;
    case 'cancel':
                      echo "<script>window.location.href='$Dealer->script';</script>";
                      break;
    case 'up':
                    $Dealer->down(TblModDealers);
                    //echo '$Dealer->script='.$Dealer->script;die;
                    echo "<script>window.location.href='$Dealer->script';</script>";
                    break;

    case 'down':
                    $Dealer->up(TblModDealers);
                    //echo '$Dealer->script='.$Dealer->script;die;
                    echo "<script>window.location.href='$Dealer->script';</script>";
                    break;                      
    case 'delimg':
                    if ( !$Dealer->DelItemImage($delimg)){
                        $Dealer->Err = $Dealer->multi['MSG_IMAGE_NOT_DELETED']."<br>";
                    }
                    $Dealer->edit();                      
 }
?> 