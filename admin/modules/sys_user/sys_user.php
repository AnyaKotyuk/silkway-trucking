<?php
/**
* sys_user.php
* Script for all action with control of system users
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/admin/include/defines.inc.php' );
include_once( SITE_PATH.'/admin/modules/sys_user/sys_user.class.php' );

if(!defined("_LANG_ID")) {$pg = new PageAdmin();}

$module = AntiHacker::AntiHackRequest('module');

//Blocking to execute a script from outside (not Admin-part)
if ( !$pg->logon->isAccessToScript($module)) exit;

$task=AntiHacker::AntiHackRequest('task','show');
if( isset($_REQUEST['change_pass']) AND $task!='cancel' ) $task = 'change_pass';

$check_up = AntiHacker::AntiHackRequest('check_up');

$sys_user = new UserBackend($pg->logon->user_id, $module, 100);
$sys_user->module = $module;
$sys_user->srch = AntiHacker::AntiHackRequest('srch');
$sys_user->fltr2 = AntiHacker::AntiHackRequest('fltr2');
$sys_user->srch_dtfrom = AntiHacker::AntiHackRequest('srch_dtfrom');
$sys_user->srch_dtto = AntiHacker::AntiHackRequest('srch_dtto');
$sys_user->start = AntiHacker::AntiHackRequest('start',0);
$sys_user->sort = AntiHacker::AntiHackRequest('sort');
$sys_user->display = AntiHacker::AntiHackRequest('display',50);
$sys_user->fltr = AntiHacker::AntiHackRequest('fltr');;
$sys_user->fltr_user = AntiHacker::AntiHackRequest('fltr_user');;
$sys_user->task = $task;
$sys_user->id =  AntiHacker::AntiHackRequest('id');;
$sys_user->group_id = AntiHacker::AntiHackRequest('group_id');;
$sys_user->login = addslashes(strip_tags(AntiHacker::AntiHackRequest('login')));
$sys_user->pass = AntiHacker::AntiHackRequestPass('pass');;
$sys_user->confirm_pass = AntiHacker::AntiHackRequestPass('confirm_pass');;
$sys_user->change_pass = AntiHacker::AntiHackRequest('change_pass');;
$sys_user->login_multi_use = AntiHacker::AntiHackRequest('login_multi_use');
$sys_user->enrol_date = addslashes(strip_tags(AntiHacker::AntiHackRequest('enrol_date')));
$sys_user->email = addslashes(strip_tags(AntiHacker::AntiHackRequest('email')));
$sys_user->old_login = addslashes(strip_tags(AntiHacker::AntiHackRequest('old_login')));
$sys_user->old_email = addslashes(strip_tags(AntiHacker::AntiHackRequest('old_email')));

$sys_user->script_ajax = "module=$sys_user->module&display=$sys_user->display&start=$sys_user->start&sort=$sys_user->sort&fltr=$sys_user->fltr&srch=$sys_user->srch&fltr2=$sys_user->fltr2&srch_dtfrom=$sys_user->srch_dtfrom&srch_dtto=$sys_user->srch_dtto";
$sys_user->script="index.php?".$sys_user->script_ajax;

//echo '<br> $sys_user->task='.$sys_user->task;
switch( $sys_user->task ){
	case 'show':
		$sys_user->show();
		break;
	case 'edit':
		if (!$sys_user->edit()) echo "<script>window.location.href='$sys_user->script';</script>";
		break;
	case 'new':
		$sys_user->edit();
		break;
	case 'newpass':
		if ( !$sys_user->change_pass_form() ){
                    echo "<script>window.location.href='$sys_user->script';</script>";
		}
		break;
	case 'change_pass':
		if( $sys_user->CheckPassFieldsSysUser($sys_user->login, $sys_user->pass, $sys_user->confirm_pass)!=NULL){
			$sys_user->change_pass_form();
			return false;
		}
		if( !$sys_user->change_pass( $sys_user->login, $sys_user->pass) ){
                    $pg->Msg->show_msg('_ERROR_SAVE');
                }
                $sys_user->sendNoticeEmail(0);
		echo "<script>window.location.href='$sys_user->script';</script>";
		break;
	case 'save':
		if ( $sys_user->CheckFields()!=NULL ){
		   $sys_user->edit();
		   return false;
                }
		if (!$sys_user->save()){
                    $pg->Msg->show_msg('_ERROR_SAVE');
                }else{
                    //Если создаем новго пользователя ,то у него $this->id еще нет.
                    //Значит на указанныйы в форме емейл отправляем письмо с логином и паролем.
                    if(empty($sys_user->id)){
                        $sys_user->sendNoticeEmail(1);
                    }
                    echo "<script>window.location.href='$sys_user->script';</script>";
                }
		break;
	case 'delete':
		if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
		else $id_del = $_REQUEST['id_del'];
                if ( !empty($id_del) ) {
                    $del=$sys_user->del( $id_del );
                    if($del==0){
                        $pg->Msg->show_msg('_ERROR_DELETE');
                    }
		}
		else $pg->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
		echo "<script>window.location.href='$sys_user->script';</script>";
		break;
	case 'cancel':
		echo "<script>window.location.href='$sys_user->script';</script>";
		break;
	case 'show_stat':
		$sys_user->ShowStatByUserId($logon->user_id);
		break;
	case 'login_checkup':
		//echo '<br>$make_check='.$make_check;
		if( empty($sys_user->login) ){
			?><span style="font-size:10px; color:red;"><?=$sys_user->Msg->show_text('_EMPTY_LOGIN_FIELD', TblBackMulti);?></span><?
			return false;
		}
		if( $sys_user->old_login!=$sys_user->login OR $check_up ){
			if( !$sys_user->unique_login($sys_user->login) ) {
				?><span style="font-size:10px; color:red;"><?=$sys_user->Msg->show_text('MSG_LOGIN_EXIST', TblBackMulti);?></span><?
			}
			else {?><span style="font-size:10px; color:green;"><?=$sys_user->Msg->show_text('MSG_LOGIN_FREE',TblBackMulti);?></span><?}
		}
		break;
	case 'alias_checkup':
		//echo '<br>$make_check='.$make_check;
		if( empty($sys_user->login) ){
			?><span style="font-size:10px; color:red;"><?=$sys_user->Msg->show_text('_EMPTY_ALIAS_FIELD', TblBackMulti);?></span><?
			return false;
		}
		if( $sys_user->old_alias!=$sys_user->alias OR $check_up ){
			if( !$sys_user->unique_email($sys_user->alias) ) {
				?><span style="font-size:10px; color:red;"><?=$sys_user->Msg->show_text('MSG_LOGIN_EXIST', TblBackMulti);?></span><?
			}
			else {?><span style="font-size:10px; color:green;"><?=$sys_user->Msg->show_text('MSG_LOGIN_FREE', TblBackMulti);?></span><?}
		}
		break;
	default:
		$sys_user->show( $logon->user_id, $module, $display, $sort, $start );
		break;
}


?>