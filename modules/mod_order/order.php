<?
  if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
  include_once( SITE_PATH.'/include/defines.php' );
  //include_once( SITE_PATH.'/modules/mod_user/user.defines.php' );
  //phpinfo();
  
  $Page = check_init("PageUser","PageUser");

  //$logon = new  UserAuthorize();

  $title = 'Пользовательская корзина | '.META_TITLE;
  $Description = 'Пользовательская корзина. '.META_DESCRIPTION;
  $Keywords = 'Пользовательская корзина, '.META_KEYWORDS; 


/*  $Page->SetTitle( $title );
  $Page->SetDescription( $Description );
  $Page->SetKeywords( $Keywords );    
     */
  //$Page->WriteHeader();
$Page->showContent2Box = '';
  $scriptact='/modules/mod_order/order.frontend.php';
ob_start();
  include_once(SITE_PATH.$scriptact);
$Page->content = ob_get_clean();
$Page->out();
 // $Page->WriteFooter();
?>