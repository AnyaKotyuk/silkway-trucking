<?php
include_once( $_SERVER['DOCUMENT_ROOT'].'/include/defines.php' );

$Page = check_init('PageUser', 'PageUser');
$Page->FrontendPages->page = PAGE_PUBLIC;
$Page->FrontendPages->page_txt = $Page->FrontendPages->GetPageTxt($Page->FrontendPages->page);
$Obj = check_init('PublicLayout', 'PublicLayout');
$Obj->PageUser = $Page;

  if( !isset($_REQUEST['categ']) ) $categ=NULL;
  if( !isset($_REQUEST['group']) ) $group=NULL;
if( !isset($_REQUEST['title']) ) $Obj->title=NULL;
else $Obj->title = $Obj->Form->GetRequestTxtData($_REQUEST['title'], 1);

if( !isset($_REQUEST['text']) ) $Obj->text=NULL;
else $Obj->text = $Obj->Form->GetRequestTxtData($_REQUEST['text'], 1);

if( !isset($_REQUEST['contact']) ) $Obj->contact=NULL;
else $Obj->contact = $Obj->Form->GetRequestTxtData($_REQUEST['contact'], 1);

if( !isset($_REQUEST['img']) ) $Obj->img=NULL;
else $Obj->img = $Obj->Form->GetRequestTxtData($_REQUEST['img'], 1);

if( !isset($_REQUEST['task']) ) $Obj->task=NULL;
else $Obj->task = $Obj->Form->GetRequestTxtData($_REQUEST['task'], 1);

if(!isset($_REQUEST['sort'])) $Obj->sort=NULL;
else $Obj->sort= $Obj->Form->GetRequestTxtData($_REQUEST['sort'], 1);

if(!isset($_REQUEST['start'])) $Obj->start=0;
else $Obj->start=$Obj->Form->GetRequestTxtData($_REQUEST['start'], 1);

if(!isset($_REQUEST['display']) ) $Obj->display=5;
else $Obj->display=$Obj->Form->GetRequestTxtData($_REQUEST['display'], 1);

if(!isset($_REQUEST['page'])) $Obj->page=1;
else $Obj->page = $Obj->Form->GetRequestTxtData($_REQUEST['page'], 1);
if($Obj->page>1) $Obj->start = ($Obj->page-1)*$Obj->display;
if($Obj->page=='all') {
    $Obj->start = 0;
    $Obj->display = 999999;
}

//echo '$Obj->page='.$Obj->page;
$Obj->categ = $categ;
$Obj->group= $group;
$Obj->status = 2;
$Obj->dt = date("Y-m-d H:i:s");
$Obj->time = time();

$title = $Page->FrontendPages->GetTitle();
if ($Obj->page>1) $title = $title.' | Page'.$Obj->page;

$Description = $Page->FrontendPages->GetDescription();
if ( empty($Description) ) $Description = $Page->FrontendPages->page_txt['pname'].' | '.META_DESCRIPTION;

$Keywords = $Page->FrontendPages->GetKeywords();
if ( empty($Keywords) ) $Keywords = $Page->FrontendPages->page_txt['pname'].' | '.META_KEYWORDS;

$Page->SetTitle( $title );
$Page->SetDescription( $Description );
$Page->SetKeywords( $Keywords );

$Obj->script=$_SERVER['PHP_SELF']."?display=$Obj->display&amp;start=$Obj->start&amp;sort=$Obj->sort&amp;srch=$Obj->srch&amp;categ=$Obj->categ&amp;group=$Obj->group";

$show_link = false;
if($Obj->task=='showfrom') $show_link = true;
$path_page = $Page->FrontendPages->ShowPath($Page->FrontendPages->page,NULL,$show_link);
$Page->breadcrumb = $path_page;
$Obj->PageUser = $Page;

ob_start();

  $Obj->AutoDeletingPublications();
  //echo '<br> $task='.$task;
  switch ($Obj->task){
      case 'showfrom':
        $Obj->ShowAddForm();
        break;
      case 'seveImg':
            $uploaddir = SITE_PATH.'/tmpImg/';
            $file = $uploaddir . basename($_FILES['uploadfile']['name']);

            if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) {
                echo $Obj->showImg('/tmpImg/'. basename($_FILES['uploadfile']['name']),NULL,50,50);
            } else {
                echo 1;
            }

          break;
      case 'addPublication':
        if ( $Obj->CheckFields()!=NULL ) {
            $Obj->ShowAddForm();
            break;
        }

        if ( $Obj->savePublic() ){
            $Obj->buildFileArray();
            $res = $Obj->SavePicture();
            if($res)  echo $res;

            $res = $Obj->sendPublic();
            if(!$res) echo $Obj->multi['MSG_PROFILE_NOT_SENT'];
            echo $Obj->multi['TXT_PUBLIC_GOOD'];
            ?><script type="text/javascript">
                setTimeout("location.href='<?=$Obj->script?>'", 2000);
            </script><?
        }else{
            echo $Obj->multi['TXT_NO_SAVE_TEXT'];
        }

        break;
      default:
        $res = $Obj->ShowPublications();
        if(!$res) $Page->Set_404();
        break;
  }

$Page->content = ob_get_clean();
$Page->out();

?>

