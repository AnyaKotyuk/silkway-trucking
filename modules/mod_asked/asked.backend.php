<?php
// ================================================================================================
//    System     : PrCSM05
//    Module     : Asked
//    Version    : 1.0.0
//    Date       : 28.05.2009
//
//    Purpose    : Class definition for Asked
//
// ================================================================================================

if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/admin/include/defines.inc.php' );
include_once( SITE_PATH.'/modules/mod_asked/asked.defines.php' );

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//============================================================================================
// START
// Blocking to execute a script from outside (not Admin-part)
//============================================================================================
$goto = "http://".NAME_SERVER."/admin/index.php?logout=1";

if ( !isset($_SESSION[ 'session_id']) OR empty($_SESSION[ 'session_id']) OR empty( $module ) ) {
     ?><script>window.location.href="<?=$goto?>";</script><?;
}

$logon = check_init("Authorization","Authorization");
if (!$logon->LoginCheck()) {
    ?><script>window.location.href="<?=$goto?>";</script><?;
}
//=============================================================================================
// END
//=============================================================================================
if( !isset( $_REQUEST['task'] ) ) $task = 'show';
else $task = $_REQUEST['task'];

if( !isset( $_REQUEST['display'] ) ) $display = 50;
else $display = $_REQUEST['display'];

if( !isset( $_REQUEST['start'] ) ) $start = 0;
else $start = $_REQUEST['start'];

if( !isset( $_REQUEST['sort'] ) ) $sort = NULL;
else $sort = $_REQUEST['sort'];

if( !isset( $_REQUEST['fltr'] ) ) $fltr=NULL;
else $fltr = $_REQUEST['fltr'];

if( !isset( $_REQUEST['id'] ) ) $id = NULL;
else $id = $_REQUEST['id'];

if( !isset( $_REQUEST['date'] ) ) $date = NULL;
else $date = $_REQUEST['date'];

if ( !isset($_REQUEST['author']) ) $author = NULL;
else $author = $_REQUEST['author'];

if ( !isset($_REQUEST['email']) ) $email = NULL;
else $email = $_REQUEST['email'];

if ( !isset($_REQUEST['category']) ) $category = NULL;
else $category = $_REQUEST['category'];

if ( !isset($_REQUEST['question']) ) $question = NULL;
else $question = $_REQUEST['question'];

if ( !isset($_REQUEST['answer']) ) $answer = NULL;
else $answer = $_REQUEST['answer'];

if ( !isset($_REQUEST['rating']) ) $rating = 0;
else $rating = $_REQUEST['rating'];

if ( !isset($_REQUEST['visible']) ) $visible = '0';
else $visible = $_REQUEST['visible'];

if( !isset( $_REQUEST['move'] ) ) $move = NULL;
else $move = $_REQUEST['move'];

if( !isset( $_REQUEST['keywords'] ) ) $keywords = NULL;
else $keywords = $_REQUEST['keywords'];

if( !isset( $_REQUEST['description'] ) ) $description = NULL;
else $description = $_REQUEST['description'];

$Asked = new AskedCtrl($logon->user_id, $module);
$Asked->id = $id;
$Asked->date = $date;
$Asked->author = $author;
$Asked->category = $category;
$Asked->email = $email;
$Asked->question = $question;
$Asked->answer = $answer;
$Asked->visible = $visible;

$Asked->display = $display;
$Asked->task = $task;
$Asked->start = $start;
$Asked->sort = $sort;
$Asked->fltr = $fltr;
$Asked->keywords = $keywords;
$Asked->description = $description;
$Asked->rating = $rating;

$script = 'module='.$Asked->module.'&display='.$Asked->display.'&start='.$Asked->start.'&sort='.$Asked->sort.'&fltr='.$Asked->fltr;
$script = $_SERVER['PHP_SELF']."?$script";

switch ($task) {

    case 'cancel':
    case 'show':
        $Asked->ShowContent();
        break;

    case 'new':
        $Asked->edit( NULL, NULL );
        break;

    case 'edit':
        $Asked->edit( $Asked->id, NULL );
        break;

    case 'save':
        $Asked->CheckFields();
        if(empty($Asked->Err)){
            if ($Asked->save()) echo "<script>window.location.href='$script';</script>";
        }else{
            $Asked->edit( $Asked->id, NULL );
        }
        break;

    case 'delete':
         if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
         else $id_del = $_REQUEST['id_del'];

         if ( !empty($id_del) ) {
             $del = $Asked->del( $id_del );
             if ( $del > 0 ) echo "<script>window.alert('".$Asked->multi['_SYS_DELETED_OK']." $del');</script>";
             else $Asked->multi['_ERROR_DELETE'];
         }

         else $Asked->multi['_ERROR_SELECT_FOR_DEL'];
         echo "<script>window.location.href='$script';</script>";
         break;
}

?>