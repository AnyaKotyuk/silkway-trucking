<?php
// ================================================================================================
// System : SEOCMS
// Module : job.php
// Version : 1.0.0
// Date : 24.10.2006
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Script for Control All actions for module of Job opportunity
//
// ================================================================================================
session_cache_limiter('private');
session_cache_expire(1);
session_start();
$_SESSION['start_in_login_file'] = 1;

if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( SITE_PATH.'/include/defines.php' );

//phpinfo();
 if(isset($_GET['razdel']))
 {
     
     $_SESSION['razdel'] = $_GET['razdel'];
   
 }
//echo $_SESSION['razdel'];
 if(!isset($_SESSION['razdel']))
 {
 $_SESSION['razdel'] = 1;
 }



$Job = new JobLayout();

$arr = $Job->GetSeo();

$title = $arr['title']." | "._TITLE;
$description = $arr['description'].". "._DESCRIPTION; 
$keywords = $arr['keywords'].", "._KEYWORDS; 


if( !isset( $_REQUEST['task'] ) ) $task = 'showall';
else $task = $_REQUEST['task'];

if( !isset( $_REQUEST['srch'] ) ) $srch = NULL;
else $srch = $_REQUEST['srch'];

if( !isset( $_REQUEST['cat'] ) ) $cat = NULL;
else $cat = $_REQUEST['cat'];

if( !isset( $_REQUEST['status'] ) ) $status = NULL;
else $status = $_REQUEST['status'];


if( !isset( $_REQUEST['fltr'] ) ) $fltr = NULL;
else $fltr = $_REQUEST['fltr'];

if( !isset( $_REQUEST['id'] ) ) $id = NULL;
else $id = $_REQUEST['id'];


$Spr = new SysSpr();




$Job->task = $task; 
$Job->status = $status; 
$Job->srch = $srch;
$Job->fltr = $fltr;
$Job->cat = $cat;
$Job->id = $id;

head($title, $description, $keywords);
?>
<table cellspacing="0" cellpadding="0" width="100%" border="0">
 <tr>
  <td width="20"></td>
  <td colspan="2" align="left" style="border-bottom:2px solid #888888;"><h1><a href="job.php" class="h1_index">Вакансії</a>
  <?
  if(!empty($Job->cat))
  {
  echo "&nbsp;>&nbsp;".$Spr->GetNameByCod(TblModJobCategory, $Job->cat);
  }
  
  if(!empty($Job->status))
  {
  echo "&nbsp;|&nbsp;".$Spr->GetNameByCod(TblModJobStatuses, $Job->status);
  }
  ?>
  </h1></td>
 </tr>
 <tr>
  <td height="5"></td>
 </tr>
 <tr>
  <td width="20"></td>
  <td valign="top">
  <?
  switch( $Job->task )
  {
   case 'showall': 
        $Job->Show_jobs();
   		break;
  }
  ?>
  </td>
  <td width="30%" valign="top">
  <?
  $Job->show_Navigation();
  ?>
  </td>
 </tr>
</table>

<?
footer();

?>
