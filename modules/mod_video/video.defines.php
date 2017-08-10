<?

// ================================================================================================
//    System     : SEOCMS
//    Module     : Video
//    Version    : 1.0.0
//    Date       : 01.07.2010
//    Purpose    : Defines Video Module
//    Licensed To: Yaroslav Gyryn
// ================================================================================================
include_once( SITE_PATH . '/include/defines.php' );
include_once( SITE_PATH . '/modules/mod_video/video.class.php' );
include_once( SITE_PATH . '/modules/mod_video/videoCtrl.class.php' );
include_once( SITE_PATH . '/modules/mod_video/videoLayout.class.php' );
include_once( SITE_PATH . '/modules/mod_video/video_settings.class.php' );

define("MOD_VIDEO", true);

define("TblModVideo", "mod_video");
define("TblModVideoCat", "mod_video_spr_category");
//define("TblModVideoCat","sys_spr_category");   // Спільний загальний довідник для всіх модулів
define("TblModVideoTxt", "mod_video_txt");
//define("TblModVideoSprTxt","mod_video_spr_txt");
// --------------- defines for news settings  ---------------
define("TblModVideoSet", "mod_video_set");
define("TblModVideoSetSprMeta", "mod_video_set_meta");
?>