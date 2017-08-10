<?
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( SITE_PATH.'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_department/department.defines.php' );

//========= FIRST DEFINE PAGE LANGUAGE  BEGIN ===========
$Page = new PageUser();
/*$Msg = new ShowMsg();
$Msg->SetShowTable(TblModDepartmentSprTxt);*/
//========= FIRST DEFINE PAGE LANGUAGE BEGIN  ===========

$ModulesPlug = new ModulesPlug();
$id_module = $ModulesPlug->GetModuleIdByPath( 'mod_department/department.backend.php' );
$Page->module = $id_module;
$Department = new DepartmentLayout(NULL, $id_module, NULL, NULL); 

if( !isset( $_REQUEST['task'] ) ) $task = 'cat';
else $task = $_REQUEST['task'];

if( !isset( $_REQUEST['start'] ) ) $start = 0;
else $start = intval($_REQUEST['start']);

if( !isset( $_REQUEST['sort'] ) ) $sort = NULL;
else $sort = $_REQUEST['sort'];

if( !isset( $_REQUEST['display'] ) ) $display = 8;
else $display = intval($_REQUEST['display']);

if(!isset($_REQUEST['page'])) $page=1;
else $page=$_REQUEST['page'];
if($page>1) $start = ($page-1)*$display;
if($page=='all') {
   $start = 0;
   $display = 999999;
}

$idCat=1;
if( !isset( $_REQUEST['cat'] ) ) $cat = NULL; // category cod
else { 
    $cat = $_REQUEST['cat'];
    $Department->catTranslit = $cat;
    $idCat= $Department->GetCategoryIdByTranslit($cat);
}

if(!$idCat) 
    $Page->Set_404(); 
$Department->fltr = ' AND `category`='.$idCat;  
$Department->cat  = $idCat;
$Department->catName = $Department->Spr->GetNameByCod(TblModDepartmentCat, $Department->cat, $Department->lang_id, 1);


if( !isset( $_REQUEST['position'] ) ) $Department->position = NULL;
else {
    $Department->position = $_REQUEST['position'];
    $Department->positionTranslit = $Department->position;
    $idPosition= $Department->GetPositionIdByTranslit($Department->position, $idCat);
    
    if(!$idPosition) 
        $Page->Set_404(); 
    $Department->id = $idPosition;  
    $Department->positionName =  $Department->Spr->GetNameByCod(TblModDepartmentTxt, $Department->id, $Department->lang_id, 1);
}

$Department->task = $task; 
$Department->display = $display;
$Department->page = $page;
$Department->start = $start;
$Department->sort = $sort;
$Department->SetMetaData();  
 
$Page->FrontendPages = new FrontendPages();    
$Page->FrontendPages->lang_id = _LANG_ID; 
$Page->FrontendPages->page = PAGE_DEPARTMENT;


if ( empty($Department->title) ) $Title = $Department->multi['TXT_DEPARTMENT_TITLE'];
else $Title = $Department->title;
if ( empty($Department->description) ) $Description = $Department->multi['TXT_DEPARTMENT_TITLE'];
else $Description = $Department->description;
if ( empty($Department->keywords) ) $Keywords = $Department->multi['TXT_DEPARTMENT_TITLE'];
else $Keywords = $Department->keywords; 

$Page->SetTitle( $Title );
$Page->SetDescription( $Description );
$Page->SetKeywords( $Keywords );


ob_start();

/*if($Department->task=='position'){
    $title_content = $Department->catName.' / '.$Department->multi['TXT_DEPARTMENT_TITLE'].' / '.$Department->positionName;
}
elseif($Department->task=='cat'){
    if(!empty($Department->cat))
        $title_content = $Department->catName.' / '.$Department->multi['TXT_DEPARTMENT_TITLE'];
    else
        $title_content = $Department->multi['TXT_DEPARTMENT_TITLE'];
}
elseif($Department->task=='last'){
    $title_content = $Department->multi['TXT_FRONT_TITLE_LATEST'];    
}
elseif($Department->task=='showall'){
    if( !empty($Department->category)) 
        $title_content = $Department->catName;
    else 
        $title_content = $Department->multi['TXT_ALL_DEPARTMENT'];
}
else {
    if(!empty($Department->cat))
        $title_content = $Department->multi['TXT_DEPARTMENT_TITLE'].' / '.$Department->catName;
    else*/
        $title_content = $Department->multi['TXT_DEPARTMENT_TITLE'];    
//}

$Page->Form->WriteContentHeader($title_content, false,false);
?><div id="department"><?
/*?>
    <div class="path">                                                         
        <?if($Department->task == 'position' or  $Department->task == 'list'){ ?>
            <div class="back"><a href="javascript:window.history.go(-1);">← <?=$Department->multi['TXT_BACK']?></a></div>
        <?}?>
        <div class="hleb" style="margin-right: 65px;">
            <?=$Department->ShowPath($Page->FrontendPages);?>
        </div>
    </div>
    
    <? 
    if($Department->task !='list') {
         if($Department->task=='cat') {
            $Page->FrontendPages->pageData = $Page->FrontendPages->GetPageData($Page->FrontendPages->page);  
            $Page->FrontendPages->content = stripslashes($Page->FrontendPages->pageData['content']);
            if(!empty($Page->FrontendPages->content)) {
                ?><div style="padding: 5px 15px;"><?=$Page->FrontendPages->content;?></div><?
            }
         }
        $Department->DepartmentsTabCat();
    }*/
//echo '<br/>$task='.$Department->task.'<br/> $Department->cat='.$Department->cat.'<br/>';
switch( $Department->task ){
/*    case 'last':
    case 'showall':
        $Department->fltr = $Department->fltr." AND `status`='a'";
        $Department->ShowDepartmentsByPages();
        break;*/

    case 'cat':
        $Department->fltr = $Department->fltr." AND `status`='a'";
        $Department->ShowDepartmentsByPages();
        break;
                
    case 'position':
        $Department->ShowDepartmentFull();
        break;

    case 'list':
        $Department->DepartmentsCatList();
        $Department->ShowDoctorsList();
        break;
                
    default: 
        $Department->ShowDepartmentCat(); 
}
//$Department->ShowDepartmentTask(); 
//$Department->ShowDepartmentNavigation();
?></div><?
$Page->Form->WriteContentFooter();
$Page->content = ob_get_clean();
$Page->out();