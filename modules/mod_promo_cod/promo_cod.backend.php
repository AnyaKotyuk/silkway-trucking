<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 03.01.14
 * Time: 15:16
 */
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/modules/mod_promo_cod/promo_cod.defines.php' );

if(!defined("_LANG_ID")) {$pg = check_init('PageAdmin', 'PageAdmin');}

if( !isset( $_REQUEST['module'] ) ) $module = NULL;
else $module = $_REQUEST['module'];

//Blocking to execute a script from outside (not Admin-part)
if ( !$pg->logon->isAccessToScript($module)) exit;

$PromoCode = new PromoCode($pg->logon->user_id, $module);

if( !isset($_REQUEST['task']) || empty($_REQUEST['task']) ) $Sitemap->task='show';
else $PromoCode->task=$_REQUEST['task'];

if( !isset($_REQUEST['id']) ) $PromoCode->id=NULL;
else $PromoCode->id=$_REQUEST['id'];

if( !isset( $_REQUEST['sort'] ) ) $PromoCode->sort = 'id';
else $PromoCode->sort = $_REQUEST['sort'];

if( !isset( $_REQUEST['asc_desc'] ) ) $PromoCode->asc_desc = 'desc';
else $PromoCode->asc_desc = $_REQUEST['asc_desc'];

if( !isset( $_REQUEST['start'] ) ) $PromoCode->start = 0;
else $PromoCode->start = $_REQUEST['start'];

if( !isset($_REQUEST['display']) ) $PromoCode->display=50;
else $PromoCode->display=$_REQUEST['display'];

if( !isset($_REQUEST['sum']) ) $PromoCode->sum=NULL;
else $PromoCode->sum=$_REQUEST['sum'];

if( !isset($_REQUEST['currency']) ) $PromoCode->currency=NULL;
else $PromoCode->currency=$_REQUEST['currency'];

if( !isset($_REQUEST['expires_date']) ) $PromoCode->expires_date=NULL;
else $PromoCode->expires_date=$_REQUEST['expires_date'];

if( !isset($_REQUEST['str_id']) ) $PromoCode->str_id=NULL;
else $PromoCode->str_id=$_REQUEST['str_id'];

if( !isset($_REQUEST['search_status']) ) $PromoCode->search_status=NULL;
else $PromoCode->search_status=$_REQUEST['search_status'];

if( !isset($_REQUEST['search_cod']) ) $PromoCode->search_cod=NULL;
else $PromoCode->search_cod=$_REQUEST['search_cod'];

if( !isset($_REQUEST['search_sum_from']) ) $PromoCode->search_sum_from=NULL;
else $PromoCode->search_sum_from=$_REQUEST['search_sum_from'];

if( !isset($_REQUEST['search_sum_to']) ) $PromoCode->search_sum_to=NULL;
else $PromoCode->search_sum_to=$_REQUEST['search_sum_to'];

if( !isset($_REQUEST['search_currency']) ) $PromoCode->search_currency=NULL;
else $PromoCode->search_currency=$_REQUEST['search_currency'];

if( $task=='savereturn') {$task='save'; $action='return';}
else $action=NULL;

if( !isset($_REQUEST['cnt']) ) $PromoCode->cnt=NULL;
else $PromoCode->cnt=$_REQUEST['cnt'];

$PromoCode->scriptInit();
//var_dump($_REQUEST);
switch( $PromoCode->task ) {
    case 'show':
            $PromoCode->show();
        break;
    case 'edit':
            $PromoCode->edit();
        break;
    case 'save':
            if ($PromoCode->save())
            {
                if( $action=='return' ) echo "<script>window.location.href='".$PromoCode->script."&task=edit&id=".$PromoCode->id."';</script>";
                else echo "<script>window.location.href='".$PromoCode->script."';</script>";
            }
            else{
                $PromoCode->edit( $PromoCode->id, NULL);
                return false;
            }
        break;
    case 'delete':
            if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
            else $id_del = $_REQUEST['id_del'];
            if ( !empty($id_del) ) {
                $del=$PromoCode->del( $id_del );
                if ( !$del > 0 ) $pg->Msg->show_msg('_ERROR_DELETE');
                else echo "<script>window.alert('".$pg->Msg->get_msg('_SYS_DELETED_OK')." $del');</script>";
            }
            else $pg->Msg->show_msg('_ERROR_SELECT_FOR_DEL');
            echo "<script>window.location.href='$PromoCode->script';</script>";
        break;
    case 'form':
        $PromoCode->form();
        break;
    case 'generate':
//        var_dump($_REQUEST);
        $res = $PromoCode->generate();
        if(empty($res)){
            echo '<div style="color: red;padding: 10px;">'.$PromoCode->multi['TXT_ERROR_GENERATE'].'</div>';
            $PromoCode->form();
        }else{
            echo '<div style="color: green;padding: 10px;">'.$res.' '.$PromoCode->multi['TXT_OK_GENERATE'].'</div>';
            $PromoCode->scriptInit(false);
            $PromoCode->show();
        }
        break;
    case 'export':
        if( !isset($_REQUEST['id_del']) ) $id_del=NULL;
        else $id_del = $_REQUEST['id_del'];
        $PromoCode->export($id_del);
        $PromoCode->show();
        break;
}