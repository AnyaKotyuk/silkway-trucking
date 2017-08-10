<?
include_once( SITE_PATH.'/include/defines.php' );
include_once( SITE_PATH.'/modules/mod_asked/asked.class.php' );
include_once( SITE_PATH.'/modules/mod_asked/askedCtrl.class.php' );
include_once( SITE_PATH.'/modules/mod_asked/askedLayout.class.php' );
 
define("MOD_ASKED", true);

define("TblModAsked","mod_asked");
define("TblModAskedCat","mod_asked_category");

define("ASKED_CATEGORY", 1);
define("ASKED_AUTOR", 1);
define("ASKED_EMAIL", 1);
define("ASKED_DATE", 1);
define("ASKED_RATING", 1);

define("ASKED_HIDE_ANSWER", 0);
define("ASKED_SHOW_NUMBER", 1);
define("ASKED_SHOW_AUTOR", 1);
define("ASKED_SHOW_DATE", 1);
define("ASKED_SHOW_ANSWER_TITLE", 1);
define("ASKED_SHOW_FORM", 1);
define("ASKED_SHOW_FORM_HIDE", 1);
?>