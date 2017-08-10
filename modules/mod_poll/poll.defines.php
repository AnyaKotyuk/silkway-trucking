<?
// ================================================================================================
//    System      : CMS
//    Module     : Poll
//    Date         : 11.02.2011
//    Licensed To:  Yaroslav Gyryn
//    Purpose    : back-end block for POLLs
// ================================================================================================

include_once( SITE_PATH.'/include/defines.php' ); 
include_once( SITE_PATH.'/modules/mod_poll/poll.class.php' );
include_once( SITE_PATH.'/modules/mod_poll/poll_ctrl.class.php' );
include_once( SITE_PATH.'/modules/mod_poll/poll_use.class.php' );

define("MOD_POLL", true);

define("TblModPoll",       "mod_poll");
define("TblModPollAlt",    "mod_poll_alternatives");
define("TblModPollIP",     "mod_poll_ip");
define("TblModPollAnswers","mod_poll_answers");
define("TblModPollSprQ",   "mod_poll_spr_question");
define("TblModPollSprA",   "mod_poll_spr_altern");
define("TblModPollSprTxt", "mod_poll_spr_txt");
?>
