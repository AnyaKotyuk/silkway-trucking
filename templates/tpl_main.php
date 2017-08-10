<?
/**
 * @uses: /include/classes/PageUser.class.php
 */

?>
<!DOCTYPE html>
    <html lang="<?=$PageUser->LangShortName;?>">
    <head>
        <?
        if (defined("MOD_CATALOG") AND MOD_CATALOG) {
            if (!isset($PageUser->Catalog->isContent))
                $PageUser->Catalog->isContent = $PageUser->Catalog->IsContent($PageUser->Catalog->id_cat);
            if ($PageUser->FrontendPages->page == PAGE_CATALOG && $PageUser->Catalog->isContent > 0) {
                $PageUser->Catalog->checHash();
            }
        }
        ?>
        <meta charset="<?=$PageUser->page_encode;?>" />
        <title><?=htmlspecialchars($PageUser->title);?></title>
        <meta name="Description" content="<? if( $PageUser->Description ) echo htmlspecialchars($PageUser->Description);else echo '';?>" />
        <meta name="Keywords" content="<? if( $PageUser->Keywords ) echo htmlspecialchars($PageUser->Keywords);else echo '';?>" />
        <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
        <?

        //Если это страница каталога с фмльтрами, то для гугла указывем дополнительные параметры
        //if( strstr($_SERVER["QUERY_STRING"], "parcod")){
        //более того, проверяем, есть ли любые дополнительные параметры в УРЛ,
        //и если есть, то будем закрыать от индексации и прописыать каноникал.
        if( strstr($_SERVER['REQUEST_URI'], '?')){
            //закрываем от индексации страницы результатов работы фильтров каталога товаров
            ?>
            <meta name="robots" content="noindex, nofollow, noarchive"/>
            <?

            if(!isset($_SERVER['REDIRECT_URL'])) {
                $link = substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '/')+1);
            }
            else{ $link = $_SERVER['REDIRECT_URL']; }
            $canonical = 'http://'.NAME_SERVER.$link;
            //echo '<br>$canonical='.$canonical;
            //Добавление этой ссылки и атрибута позволяет владельцам сайтов определять наборы идентичного содержания и сообщать Google:
            //"Из всех страниц с идентичным содержанием эта является наиболее полезной.
            //Установите для нее наивысший приоритет в результатах поиска."
            ?>
            <link rel="canonical" href="<?=$canonical;?>"/>
            <?
        }
        ?>

        <link rel="icon" type="image/vnd.microsoft.icon"  href="/images/design/favicon.ico" />
        <link rel="SHORTCUT ICON" href="/images/design/favicon.ico" />
        <link href="/include/css/main.css" type="text/css" rel="stylesheet" />
        <!--[if IE ]>
        <link href="/include/css/browsers/ie.css" rel="stylesheet" type="text/css" media="screen" />
        <![endif]-->
        <!--[if lt IE 8]>
        <link href="/include/css/browsers/ie7.css" rel="stylesheet" type="text/css" media="screen" />
        <![endif]-->
        <!--[if lt IE 7]>
        <script type="text/javascript" src="/include/js/iepngfix_tilebg.js"></script>
        <![endif]-->

        <!--Include jQuery scripts-->
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
        <?/*<script type="text/javascript" src='http://<?=NAME_SERVER."/sys/js/jQuery/jquery.js";?>'></script>*/?>
        <script type="text/javascript" src='http://<?=NAME_SERVER."/sys/js/jQuery/jquery.form.js";?>'></script>

        <script src="/include/js/cms_lib/lib.js" type="text/javascript"></script>

        <!-- optionally include helper plugins -->
        <script type="text/javascript" language="javascript" src="/include/js/carouFredSel/jquery.mousewheel.min.js"></script>
        <script type="text/javascript" language="javascript" src="/include/js/carouFredSel/jquery.touchSwipe.min.js"></script>
        <script type="text/javascript" language="javascript" src="/include/js/gallery.js"></script>
        <script type="text/javascript" language="javascript" src="/include/js/carouFredSel/jquery.carouFredSel-6.2.1-packed.js"></script>

        <!-- Enable HTML5 tags for old browsers -->
        <script type="text/javascript" src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>

        <!-- Старт валидации -->
        <script type="text/javascript" src="/include/js/validator/js/jquery.validationEngine.js"></script>
        <script type="text/javascript" src="/include/js/validator/js/languages/jquery.validationEngine-ru.js"></script>
        <script type="text/javascript" src="/include/js/validator/js/languages/jquery.validationEngine-en.js"></script>
        <link href="/include/js/validator/css/validationEngine.jquery.css" type="text/css" rel="stylesheet" media="screen"/>
        <!-- Конец валидации -->

        <?if (defined("MOD_GALLERY") AND MOD_GALLERY){?>
            <!--Photo Gallery-->
            <script type="text/javascript" src="/include/js/gallery.js"></script>
            <link rel="stylesheet" type="text/css" href="/include/css/jquery.jcarousel.css" media="screen" />
            <link rel="stylesheet" type="text/css" href="/include/css/jcarousel.gallery.css" media="screen" />
            <script type="text/javascript" src="/include/js/jquery.jcarousel.pack.js"></script>
            <!--End Photo Gallery-->
        <?}?>

        <?if (defined("MOD_VIDEO") AND MOD_VIDEO){?>
            <script type="text/javascript" src="/modules/mod_video/player/flowplayer-3.2.6.min.js"></script>
        <?}?>

        <!-- Комментарий вконтакте В тег <head> на странице Вашего сайта необходимо добавить следующий код: -->
        <!--<script src="http://userapi.com/js/api/openapi.js" type="text/javascript"></script>-->
        <script src="/include/js/cms_lib/popup.js" type="text/javascript"></script>
        <script src="/include/js/cms_lib/comments.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="/include/css/comments.css" media="screen" />

        <?if (defined("MOD_PUBLIC") AND MOD_PUBLIC){?>
            <!-- для публичних обявлений -->
            <script src="/include/js/public.js" type="text/javascript"></script>
        <?}?>

        <?if (defined("MOD_POLL") AND MOD_POLL){?>
            <!-- для публичних обявлений -->
            <script src="/include/js/poll.js" type="text/javascript"></script>
        <?}?>

        <?if (defined("MOD_ORDER") AND MOD_ORDER){?>
            <!-- для публичних обявлений -->
            <script src="/include/js/cms_lib/order.js" type="text/javascript"></script>
        <?}?>

        <!-- увеличалка -->
        <script src="/include/js/fancybox/jquery.fancybox.js" type="text/javascript"></script>
        <link href="/include/js/fancybox/jquery.fancybox.css" type="text/css" rel="stylesheet" media="screen"/>

        <script type="text/javascript">
            var _JS_LANG_ID = <?=_LANG_ID?>;
        </script>

        <script type="text/javascript" src="/include/js/jquery-ui-1.10.0/jquery-ui-1.10.0.custom.js"></script>
        <link href="/include/js/jquery-ui-1.10.0/jquery-ui-1.10.0.custom.css" type="text/css" rel="stylesheet" media="screen"/>
        <script src="/include/js/compare.js" type="text/javascript"></script>
        <script src="/include/js/filter.js" type="text/javascript"></script>
        <?
        //=== IMPORTANT!!! Do not delete this block! ===
        //Insert codes from Site Settings
        echo $PageUser->Settings['site_codes_head']."\n";
        //==============================================
        ?>
    </head>

    <body>
        <?
        //=== IMPORTANT!!! Do not delete this block! ===
        //Insert codes from Site Settings
        echo $PageUser->Settings['site_codes_body_start']."\n";
        //==============================================
        ?>

        <!--[if lt IE 8]>
        <div style=" margin:10px auto 0px auto; padding:20px; background:#DDDDDD; border:1px solid gray; width:980px; font-size:14px;">
            Уважаемый Пользователь!</br>
            Вы используете <span class="red">устаревший WEB-браузер</span>.</br>
            Предлагаем Вам установить и использовать последние версии WEB-браузеров, например:<br/>
            <ul>
                <li>Google Chrome <a href="https://www.google.com/chrome">https://www.google.com/chrome</a></li>
                <li>Mozilla Firefox <a href="http://www.mozilla.org/ru/firefox/new/">http://www.mozilla.org/ru/firefox/new/</a></li>
                <li>Opera <a href="http://www.opera.com/download/">http://www.opera.com/download/</a></li>
            </ul>
            Последние версии WEB-браузеров доступны для установки на сайтах разработчиков и содержат улучшенные свойства безопасности, повышенную скорость работы, меньшее количество ошибок. Эти простые действия помогут Вам максимально использовать функциональность сайта, избежать ошибок в работе, повысить уровень безопасности.
        </div>
        <![endif]-->

        <div class="wrapper">
            <div class="header-logo">
                <a href="<?=_LINK?>"><img src="/images/cmsLogo.png" alt="" title=""></a>
            </div>
            <div class="header-slogan">
                <strong><?=$PageUser->FrontendPages->getNameSpecContentByCod(2);?></strong>
                <?=$PageUser->FrontendPages->getSpecContentByCod(2, false);?>
            </div>
            <div class="header-tel">
                <strong><?=$PageUser->FrontendPages->getNameSpecContentByCod(1);?></strong>
                <?=$PageUser->FrontendPages->getSpecContentByCod(1, false);?>
            </div>

            <?=$PageUser->Lang->WriteLangPanelShort($PageUser->getMultiUrls());?>

            <div class="header-icons">
                <a class="icoHome" title="<?=$PageUser->multi['TXT_FRONT_HOME_PAGE'];?>" href="<?=_LINK;?>">&nbsp;</a>
                <a class="icoMail" title="<?=$PageUser->multi['TXT_FEEDBACK'];?>" href="<?=_LINK;?>contacts/">&nbsp;</a>
                <a class="icoMap"  title="<?=$PageUser->multi['_TXT_SITE_MAP'];?>" href="<?=_LINK;?>sitemap/">&nbsp;</a>
            </div>

            <div class="clear"></div>
            <? if (defined("MOD_CATALOG") AND MOD_CATALOG) { ?>
                <!-- SEO for Internet Magazine-->
                <? $upSEOMsg = $PageUser->Catalog->ShowHeaderSEO() ?>
                <div class="header-seo"><?= $upSEOMsg; ?></div>
                <!-- End SEO for Internet Magazine-->
            <? } ?>

            <div class="menu"><?$PageUser->FrontendPages->ShowHorisontalMenu();?></div>

            <?
            echo $contentHtml;
            ?>

            <div class="clear"></div>
            <? if (defined("MOD_CATALOG") AND MOD_CATALOG) { ?>
                <!-- SEO for Internet Magazine-->
                <? $dnSEOMsg = $PageUser->Catalog->ShowFooterSEO(); ?>
                <div class="footer-seo"><?= $dnSEOMsg; ?></div>
                <!-- End SEO for Internet Magazine-->

            <?
            }
            $PageUser->informerLayout->showSocial();
            $PageUser->FrontendPages->ShowFooterMenu();
            $yearStart = 2014;
            if(date("Y")>$yearStart){
                $year = $yearStart.' - '.date("Y");
            }else{
                $year = $yearStart;
            }
            ?>
            <div class="btm1">
                <div style="float:left">Copyright&nbsp;&copy;&nbsp;<?=$year;?>&nbsp;<a href="/rss/export<?=$PageUser->lang_id;?>.xml">RSS</a></div>
                <div style="float:right">
                    <?
                    if($PageUser->FrontendPages->page==$PageUser->FrontendPages->main_page) $txt = $PageUser->multi['_SITE_DEVELOPER_TEXT_2'];
                    else $txt = $PageUser->multi['_SITE_DEVELOPER_TEXT'];
                    echo $txt;
                    ?>
                </div>
            </div>
        </div>
        <?
        //=== IMPORTANT!!! Do not delete this block! ===
        //Insert codes from Site Settings
        echo $PageUser->Settings['site_codes_body_end']."\n";
        //==============================================
        ?>

    </body>
</html>
<?
if( defined("MAKE_DEBUG") AND MAKE_DEBUG==1 ){
    $PageUser->time_end = $PageUser->getmicrotime();
    ?><div style="font-size:9px; color:#797979;"><?
    printf ("<br/>TIME:%2.3f", $PageUser->time_end - $PageUser->time_start);
    if( isset($_SESSION['cnt_db_queries'])) echo '<br/>QUERIES: '.$_SESSION['cnt_db_queries'];
    ?></div><?
}
?>
