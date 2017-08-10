<?php
// ================================================================================================
// System : PrCSM05
// Module : user.defines.php
// Version : 1.0.0
// Date : 06.01.2006
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose :
//
// ================================================================================================

include_once( $_SERVER['DOCUMENT_ROOT'].'/include/defines.php' );
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_search/search.class.php' );
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_search/searchCtrl.class.php' );

define("MOD_SEARCH", true);

define("TblModSearchResult","mod_search_result");
/*
?>
SELECT * FROM `mod_pages_txt` WHERE
(BINARY `content` = 'Житомир') OR
(BINARY `pname` = 'Житомир')
UNION
SELECT * FROM `mod_pages_txt` WHERE
(`content` = 'Житомир') OR
(`pname` = 'Житомир')
UNION
SELECT * FROM `mod_pages_txt` WHERE
(BINARY `content` LIKE '%Житомир%') OR
(BINARY `pname` LIKE '%Житомир%')
UNION
SELECT * FROM `mod_pages_txt` WHERE
(`content` LIKE '%Житомир%') OR
(`pname` LIKE '%Житомир%')*/?>
