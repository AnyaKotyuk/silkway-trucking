<?
// ================================================================================================
//    System     : PrCSM05
//    Module     : News
//    Version    : 1.0.0
//    Date       : 04.02.2005
//    Licensed To:
//                 Igor  Trokhymchuk  ihoru@mail.ru
//                 Andriy Lykhodid    las_zt@mail.ru
//
//    Purpose    : Class definition for News - moule
//
// ================================================================================================
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_job/job.defines.php' );

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
    //return false;
    ?><script>window.location.href="<?=$goto?>";</script><?; 
}
//=============================================================================================
// END
//=============================================================================================


if( !isset( $_REQUEST['task'] ) ) $task = 'show';
else $task = $_REQUEST['task'];

if( !isset( $_REQUEST['display'] ) ) $display = 10;
else $display = $_REQUEST['display'];

if( !isset( $_REQUEST['start'] ) ) $start = 0;
else $start = $_REQUEST['start'];

if( !isset( $_REQUEST['sort'] ) ) $sort = NULL;
else $sort = $_REQUEST['sort'];

if( !isset( $_REQUEST['id'] ) ) $id = NULL;
else $id = $_REQUEST['id'];

if( !isset( $_REQUEST['fltr'] ) ) $fltr=NULL;
else $fltr = $_REQUEST['fltr'];

if( !isset( $_REQUEST['filter'] ) ) $filter=NULL;
else $filter = $_REQUEST['filter'];

if( !isset( $_REQUEST['move'] ) ) $move=NULL;
else $move = $_REQUEST['move'];

if( !isset( $_REQUEST['date'] ) ) $date=NULL;
else $date = $_REQUEST['date'];

if( !isset( $_REQUEST['cat'] ) ) $cat=NULL;
else $cat = $_REQUEST['cat'];

if( !isset( $_REQUEST['visible'] ) ) $visible=NULL;
else $visible = $_REQUEST['visible'];

if( !isset( $_REQUEST['age'] ) ) $age=NULL;
else $age = $_REQUEST['age'];

if( !isset( $_REQUEST['vac'] ) ) $vac=NULL;
else $vac = $_REQUEST['vac'];

if( !isset( $_REQUEST['status'] ) ) $status=NULL;
else $status = $_REQUEST['status'];

if( !isset( $_REQUEST['position'] ) ) $position=NULL;
else $position = $_REQUEST['position'];

if( !isset( $_REQUEST['education'] ) ) $education=NULL;
else $education = $_REQUEST['education'];

if( !isset( $_REQUEST['experience'] ) ) $experience=NULL;
else $experience = $_REQUEST['experience'];

if( !isset( $_REQUEST['contacts'] ) ) $contacts=NULL;
else $contacts = $_REQUEST['contacts'];

if( !isset( $_REQUEST['Err'] ) ) $Err=NULL;
else $Err = $_REQUEST['Err'];

/*
if( !isset( $_REQUEST['img'] ) ) $img=NULL;
else $img = $_REQUEST['img'];
*/

 $Job = new Job();
 $jobBackend = new JobBackend($logon->user_id, $module);
 $jobBackend->module = $module;
 $jobBackend->user_id = $logon->user_id;
 $jobBackend->task = $task;
 $jobBackend->display = $display;
 $jobBackend->start = $start;
 $jobBackend->sort = $sort;
 $jobBackend->fltr = $fltr;
 
 $jobBackend->Err = $Err;
 
 $jobBackend->move = $move;
 $jobBackend->age = $age;
 $jobBackend->date = $date;
 $jobBackend->cat = $cat;
 $jobBackend->visible = $visible;
 $jobBackend->vac = $vac;
 $jobBackend->status = $status;
 $jobBackend->position = $position;
 $jobBackend->education = $education;
 $jobBackend->experience = $experience;
 $jobBackend->contacts = $contacts;
// $Job->img = $img;

 if( !isset( $_REQUEST['fln'] ) ) $jobBackend->fln = _LANG_ID;
 else $jobBackend->fln = $_REQUEST['fln'];

 $script = 'module='.$jobBackend->module.'&display='.$jobBackend->display.'&start='.$jobBackend->start.'&sort='.$jobBackend->sort.'&fltr='.$jobBackend->fltr;
 //.'&fltr2='.$Job->fltr2.'&fltr3='.$Job->fltr3;
 $script = $_SERVER['PHP_SELF']."?$script";


switch( $task ) {

    case 'show':      $jobBackend->show(); break;
	
    case 'new':       $jobBackend->edit( NULL, NULL ); break;

    case 'edit':      $jobBackend->edit( $id, NULL ); break;

    case 'save':
                       if($jobBackend->CheckContentFields($id) != NULL)
					     {
						  $jobBackend->edit( $id, NULL );
						  return false;
						 }
						 else{
					   //$filename_name = substr($filename, strrpos('\\',$filename));
					   //echo '<br> $filename='.$filename.' $filename_name='.$filename_name.' $_FILES["filename"]["name"]='.$_FILES["filename"]["name"];
					   /*if ( !empty($filename) ) {
                          $m->img=$filename_name;
                          $uploaddir = $_SERVER['DOCUMENT_ROOT'].'/images/mod_news/';

                          if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777); 
                          else chmod($uploaddir,0777);
                          $uploaddir1 = $uploaddir.$m->img; 
                          if ( !copy($filename,$uploaddir1) ) {
                              chmod($uploaddir,0755);
                              echo "Ошибка копирования файла<br>";
                              $m->edit( $id );
                              return false;
                          }
                       }   
                       //echo '<br> $Catalog->img_cat='.$Catalog->img_cat;
                       chmod($uploaddir,0755);
					  */
					  
					  if ( $jobBackend->save($id) )
                      {
                        echo "<script>window.location.href='$script';</script>";
                      }
					  else echo '<br>'.$Msg->show_text('MSG_ERR_NOT_SAVE');
                       break;
					  }
                      break;

    case 'delete':
                       $del = $jobBackend->del( $id_del );
                       if ( $del > 0 ) {echo "<script>window.alert('Deleted OK! ($del records)');</script>";}
                       else {$Msg->show_msg('_ERROR_DELETE');}
                       echo "<script>window.location.href='$script';</script>";
                       break;

    case 'up':
                    $jobBackend->up($move);
                    echo "<script>window.location.href='$script';</script>";
                    break;

    case 'down':
                    $jobBackend->down($move);
                    echo "<script>window.location.href='$script';</script>";
                    break;

    case 'cancel':  echo "<script>window.location.href='$script';</script>";
                    break;

    case 'preview':
                    $jobBackend->preview();
                    break;

}

?>
