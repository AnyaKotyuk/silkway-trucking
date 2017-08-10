<?
/**
* dispatch.php
* script for all actions with with sending created letters by CRON
* @package Mailer Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 29.08.2013
* @copyright (c) 2010+ by SEOTM
*/
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_mailer/mailer.defines.php' );

$Page = new PageAdmin();

$Mailer = new Mailer();
if( !isset( $_REQUEST['task'] ) ) $Mailer->task = NULL;
else $Mailer->task = $_REQUEST['task'];

if(isset($_REQUEST['subscr_start'])) $Mailer->subscr_start = $_REQUEST['subscr_start'];

if(isset($_REQUEST['subscr_cnt'])) $Mailer->subscr_cnt = $_REQUEST['subscr_cnt'];

$Mailer->showResult=true;
//echo '<br>$Mailer->task='.$Mailer->task;
switch( $Mailer->task ) {
    case 'send':
        $Mailer->MakeDispatch();
        break;
    default:
        break;
}
 ?>