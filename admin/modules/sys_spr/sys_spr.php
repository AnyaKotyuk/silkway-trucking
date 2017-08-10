<?php
/**
 * sys_spr.php
 * script for all actions with reference-books
 * @package System Package of SEOCMS
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 1.1, 02.04.2012
 * @copyright (c) 2005+ by SEOTM
 */
if (!defined("SITE_PATH")){
    define("SITE_PATH", $_SERVER['DOCUMENT_ROOT']);
}
include_once( SITE_PATH . '/admin/include/defines.inc.php' );
include_once( SITE_PATH . '/admin/modules/sys_spr/sys_spr.class.php' );
if (!defined("_LANG_ID")) {
    $pg = check_init("PageAdmin","PageAdmin");
}

//echo '<br>$_REQUEST='.print_r($_REQUEST);
$module = AntiHacker::AntiHackRequest('module');
//echo '<br>$module='.$module;
//============================================================================================
// START
// Blocking to execute a script from outside (not Admin-part)
//============================================================================================
$Msg = check_init("ShowMsg","ShowMsg");
$goto = "/admin/index.php?logout=1";
//echo '<br>$goto='.$goto;
//var_dump($_SESSION);echo '$module='.$module;die();
if (!isset($_SESSION['session_id']) OR empty($_SESSION['session_id']) OR empty($module)) {
//    echo '$module='.$module;
//    var_dump($_REQUEST);
//    var_dump($_SESSION);
    $Msg->show_msg( '_NOT_AUTH' );
//    die();
    //return false;
    ?><script>window.location.href="<?= $goto ?>";</script><?
;
    exit;
}

$logon = check_init('logon', 'Authorization');
//if ( ! defined('BASEPATH')) {
if (!$logon->LoginCheck()) {
//    var_dump($_REQUEST);
    //return false;
    ?><script>window.location.href="<?= $goto ?>";</script><?
;
}
//=============================================================================================
// END
//=============================================================================================

$spr = AntiHacker::AntiHackRequest('spr');
$mas_module = explode("?", $module);
$module = $mas_module[0];
if(empty($spr)) {
    $spr = $mas_module[1];
    $mas_spr = explode("=", $mas_module[1]);
    $spr = $mas_spr[1];
}
//var_dump($logon);die();
$sys_spr = new SysSpr($logon->user_id, $module, NULL, NULL, NULL, '100%', $spr);

$sys_spr->task = AntiHacker::AntiHackRequest('task', 'show');
$sys_spr->sort = AntiHacker::AntiHackRequest('sort');
$sys_spr->start = AntiHacker::AntiHackRequest('start', '0');
$sys_spr->display = AntiHacker::AntiHackRequest('display', '20');
$sys_spr->id = AntiHacker::AntiHackRequest('id');
$sys_spr->cod = AntiHacker::AntiHackRequest('cod');
$sys_spr->cod_old = AntiHacker::AntiHackRequest('cod_old');
$sys_spr->fln = AntiHacker::AntiHackRequest('fln', _LANG_ID);
$sys_spr->srch = AntiHacker::AntiHackRequest('srch');
$sys_spr->module_name = AntiHacker::AntiHackRequest('module_name');
$sys_spr->root_script = AntiHacker::AntiHackRequest('root_script');
$sys_spr->parent_script = AntiHacker::AntiHackRequest('parent_script');
$sys_spr->parent_id = AntiHacker::AntiHackRequest('parent_id');
$sys_spr->info_msg = AntiHacker::AntiHackRequest('info_msg');
$sys_spr->asc_desc = AntiHacker::AntiHackRequest('asc_desc','asc');
$sys_spr->edit_lang = AntiHacker::AntiHackRequest('edit_lang', _LANG_ID);

//------- settings fielsd start ---------
if(!isset($_REQUEST['usename']))$_REQUEST['usename'] = 1;
//var_dump($_REQUEST);
$sys_spr->initArraySettings();
$sys_spr->getValuesFromRequest();
$sys_spr->usemove = AntiHacker::AntiHackRequest('usemove');
$sys_spr->move = AntiHacker::AntiHackArrayRequest('move');
if (!isset($_REQUEST['replace_to'])){
    $sys_spr->replace_to = NULL;
}else{
    $sys_spr->replace_to = stripslashes(trim($_REQUEST['replace_to']));
}

$sys_spr->uselevels = AntiHacker::AntiHackRequest('uselevels');
$sys_spr->level = AntiHacker::AntiHackRequest('level', 0);
$sys_spr->node = AntiHacker::AntiHackRequest('node', 0);
$sys_spr->level_new = AntiHacker::AntiHackRequest('level_new', '0');

$sys_spr->usecolors = AntiHacker::AntiHackRequest('usecolors');
$sys_spr->colorBit = AntiHacker::AntiHackArrayRequest('colorBit');

$sys_spr->usemeta = AntiHacker::AntiHackRequest('usemeta');
$sys_spr->mtitle = AntiHacker::AntiHackArrayRequest('mtitle');
$sys_spr->mdescr = AntiHacker::AntiHackArrayRequest('mdescr');
$sys_spr->mkeywords = AntiHacker::AntiHackArrayRequest('mkeywords');

$sys_spr->usetranslit = AntiHacker::AntiHackRequest('usetranslit');
$sys_spr->translit = AntiHacker::AntiHackArrayRequest('translit');
$sys_spr->translit_from = AntiHacker::AntiHackArrayRequest('translit_from');

$sys_spr->useuploadimages = AntiHacker::AntiHackRequest('useuploadimages', '0');
if($sys_spr->useuploadimages==1){
    $sys_spr->UploadImages = check_init('UploadImage', 'UploadImage', $sys_spr->module.", null, 'uploads/images/".$sys_spr->spr."', '".TblModUploadImg."'");
}
$sys_spr->useuploadfiles = AntiHacker::AntiHackRequest('useuploadfiles', '0');
if($sys_spr->useuploadfiles==1){
    $sys_spr->UploadFile = check_init('UploadClass', 'UploadClass', $sys_spr->module.", null, 'uploads/files/".$sys_spr->spr."','".TblModUploadFiles."'");
}
//------- settings fielsd end ---------

//--- For Catalog parameters ---
$sys_spr->id_cat = AntiHacker::AntiHackRequest('id_cat');
$sys_spr->id_param = AntiHacker::AntiHackRequest('id_param');

if( $sys_spr->task=='savereturn') {$sys_spr->task='save'; $sys_spr->action='return';}
else $sys_spr->action=NULL;

if ($sys_spr->module != NULL){
    $sys_spr->script_ajax = "module=$sys_spr->module&spr=$sys_spr->spr&display=$sys_spr->display&start=$sys_spr->start&sort=$sys_spr->sort&fln=$sys_spr->fln&uselevels=$sys_spr->uselevels&level=$sys_spr->level&node=$sys_spr->node&usemeta=$sys_spr->usemeta&root_script=$sys_spr->root_script&parent_script=$sys_spr->parent_script&parent_id=$sys_spr->parent_id&srch=$sys_spr->srch&module_name=$sys_spr->module_name";
    if(isset($sys_spr->arraySettings) && !empty($sys_spr->arraySettings))
        foreach($sys_spr->arraySettings as $key=>$val){
            $sys_spr->script_ajax .= '&'.$key.'=1';
        }
    if(isset($_REQUEST['usename']) && $_REQUEST['usename']==0){
        $sys_spr->script_ajax .= '&usename=0';
    }
    if(isset($sys_spr->arraySettingsType['spr']) && !empty($sys_spr->arraySettingsType['spr'])){
        foreach($sys_spr->arraySettingsType['spr'] as $key=>$rowTxt){
            if($rowTxt['typeLink']=='one')
            $sys_spr->script_ajax .= '&'.$rowTxt['filterName'].'='.$sys_spr->arrayValues[$rowTxt['filterName']];
        }
    }
    if (!empty($sys_spr->id_cat))
        $sys_spr->script_ajax .= "&id_cat=" . $sys_spr->id_cat;
    if (!empty($sys_spr->id_param))
        $sys_spr->script_ajax .= "&id_param=" . $sys_spr->id_param;
    if (!empty($sys_spr->asc_desc))
        $sys_spr->script_ajax .= "&asc_desc=" . $sys_spr->asc_desc;
    $sys_spr->script = "index.php?" . $sys_spr->script_ajax;
//    echo '<br> $sys_spr->script='.$sys_spr->script;
    //phpinfo();
//    echo '<br>$sys_spr->task='.$sys_spr->task;
    switch ($sys_spr->task) {
        case 'show':
            $sys_spr->show();
            break;
        case 'show_sublevel':
            $sys_spr->show();
            break;
        case 'edit':
        case 'new':
            $sys_spr->edit();
            break;
        case 'save':
            if ($sys_spr->SavePicture() != NULL) {
                $sys_spr->edit();
                return false;
            }
//            echo 'ddd';
            $sys_spr->CheckFields();
//            echo '<br>$sys_spr->Err='.$sys_spr->Err;
            if (empty($sys_spr->Err)) {
                if ($sys_spr->save()) {
                    $go_to_ = true;
                    if (!empty($sys_spr->Err)) {
                        echo $sys_spr->Err;
                        $go_to_ = false;
                    }
//                    echo '$go_to_='.$go_to_;
                    if($go_to_){
                        $sys_spr->info_msg = $sys_spr->Msg_text['_OK_SAVE'];
                        if( $sys_spr->action=='return' ){
                            echo "<script>window.location.href='".$sys_spr->script."&info_msg=".$sys_spr->info_msg."&task=edit&id=".$sys_spr->id."';</script>";
                        }else{
                            echo "<script>window.location.href='".$sys_spr->script."&info_msg=".$sys_spr->info_msg."';</script>";
                        }
                    }else{
                        $sys_spr->edit();
                    }
                }
            } else {
                $sys_spr->edit();
            }
            break;
        case 'delete':
            if (!isset($_REQUEST['id_del']))
                $id_del = NULL;
            else
                $id_del = $_REQUEST['id_del'];
            if (!empty($id_del)) {
                $del = $sys_spr->del($id_del);
                if ($del == 0)
                    $Msg->show_msg('_ERROR_DELETE');
            }
            else
                $Msg->show_msg('_ERROR_SELECT_FOR_DEL');
            echo '<script>window.location.href="', $sys_spr->script, '";</script>';
            break;
        case 'cancel':
            echo '<script>window.location.href="', $sys_spr->script, '";</script>';
            break;
        case 'up':
//            echo 'up $sys_spr->asc_desc='.$sys_spr->asc_desc.' $sys_spr->sort='.$sys_spr->sort;
            if($sys_spr->asc_desc=='asc')$sys_spr->down($sys_spr->spr, $sys_spr->level);
            elseif($sys_spr->asc_desc=='desc') $sys_spr->up($sys_spr->spr, $sys_spr->level);
            $sys_spr->ShowContentHTML();
            //echo "<script>window.location.href='$sys_spr->script';</script>";
            break;
        case 'down':
//            echo 'down $sys_spr->asc_desc='.$sys_spr->asc_desc.' $sys_spr->sort='.$sys_spr->sort;
//            echo ' start='.$sys_spr->start.' display='.$sys_spr->display;
            if($sys_spr->asc_desc=='desc')$sys_spr->down($sys_spr->spr, $sys_spr->level);
            elseif($sys_spr->asc_desc=='asc')$sys_spr->up($sys_spr->spr, $sys_spr->level);
            $sys_spr->ShowContentHTML();
            //echo "<script>window.location.href='$sys_spr->script';</script>";
            break;
        case 'replace':
            $sys_spr->Form->ReplaceByCod($sys_spr->spr, 'move', $sys_spr->id, $sys_spr->replace_to);
            $sys_spr->ShowContentHTML();
            break;
        case 'delIcon':
            $item_icon = AntiHacker::AntiHackRequest('item_icon');
            $edit_lang = AntiHacker::AntiHackRequest('edit_lang');
            if (!$sys_spr->DelItemImage( $item_icon, $sys_spr->cod, $edit_lang)) {
                $sys_spr->Err = $sys_spr->Msg->show_text('MSG_IMAGE_NOT_DELETED') . "<br>";
            }else{
                if(!empty($edit_lang))$sys_spr->arrayValues[$item_icon][$edit_lang] = '';
                else $sys_spr->arrayValues[$item_icon] = '';
            }
            $sys_spr->edit();
            break;
        case 'make_search':
            $sys_spr->showList();
            break;
        default:
            if(strstr($sys_spr->task,'change')){
                $name = substr($sys_spr->task,strpos($sys_spr->task,'_') + 1);
                $sys_spr->ChangeVisibleProp($sys_spr->cod, AntiHacker::AntiHackRequest($name,'1'),$name);
                $sys_spr->ShowVisibility($sys_spr->cod, AntiHacker::AntiHackRequest($name,'1'),$name);
            }
            break;
    } //end switch
} //end if
?>