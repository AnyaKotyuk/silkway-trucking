<?php
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] );
include_once( SITE_PATH.'/modules/mod_poll/poll.defines.php' ); 
$Page = new PageUser();
$m = new PollUse();
 
$Page->WriteHeader();
$m->ShowPoll();
$Page->WriteFooter();
?>