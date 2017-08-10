<?php
/**
* mailer.defines.php
* script for all definitions
* @package Mailer Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 29.08.2013
* @copyright (c) 2010+ by SEOTM
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/defines.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/modules/mod_mailer/mailer.class.php' );

define("TblModMailerDispatch","mod_mailer_dispatch");

//------------ defines for Files ----------------
define("TblModMailerDispatchFiles","mod_mailer_dispatch_files");
define("MAILER_UPLOAD_FILES_PATH","/images/mod_mailer");
if (!defined("MAILER_MAX_FILE_SIZE")) define("MAILER_MAX_FILE_SIZE", 2048 * 1024);
if (!defined("MAILER_UPLOAD_FILES_COUNT")) define("MAILER_UPLOAD_FILES_COUNT", 5);
if (!defined("MAILER_MAX_UPLOAD_FILES_COUNT")) define("MAILER_MAX_UPLOAD_FILES_COUNT", 20);
?>