<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 19.06.13
 * Time: 9:31
 * To change this template use File | Settings | File Templates.
 */
?>
<div class="news padd">
    <div class="news-detail news container text-center">
    <!--    <div class="social_head">-->
    <!--        <div id="fb-root" class="classmates"></div>-->
    <!--        <script>(function(d, s, id) {-->
    <!--                var js, fjs = d.getElementsByTagName(s)[0];-->
    <!--                if (d.getElementById(id)) return;-->
    <!--                js = d.createElement(s); js.id = id;-->
    <!--                js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1";-->
    <!--                fjs.parentNode.insertBefore(js, fjs);-->
    <!--            }(document, 'script', 'facebook-jssdk'));</script>-->
    <!--        --><?php
    //        if(isset($_SERVER['REQUEST_URI']))
    //            $uri = 'http://'.NAME_SERVER.$_SERVER['REQUEST_URI'];
    //        else $uri = 'http://'.NAME_SERVER;
    //        ?>
    <!--        <div class="fb-share-button classmates" data-href="--><?php //echo $uri?><!--" data-type="button_count"></div>-->
    <!---->
    <!---->
    <!--        <div id="likeVk" class="classmates"></div>-->
    <!--        <script type="text/javascript">-->
    <!--            var countOfIn = 0,-->
    <!--                countOfOut = 0,-->
    <!--                allLoadIn = [-->
    <!--                    '/include/js/openapi.js',-->
    <!--                    '/sys/js/cal_frontend/cldr.js',-->
    <!--                    '/sys/js/ajax/poller.js',-->
    <!--                    '/sys/js/ajax/ajax.js',-->
    <!--                    '/include/js/swfobject.js',-->
    <!--                    '/include/js/CurrentTime.js',-->
    <!--                    '/include/js/lazyload.js'-->
    <!--                ],-->
    <!--                allLoadOut = [-->
    <!--//                'http://vk.com/js/api/openapi.js?47',-->
    <!--                    'http://vk.com/js/api/share.js?11',-->
    <!---->
    <!--                    'https://w.uptolike.com/widgets/v1/zp.js?pid=1261512',-->
    <!--                    'http://www.google.com/coop/cse/brand?form=cse-search-box&lang=ru',-->
    <!--                    'https://www.gstatic.com/swiffy/v5.0/runtime.js'-->
    <!--                ],-->
    <!--                allWithoutLoad = allLoadIn.length;-->
    <!--            function loadIn(){-->
    <!--                countOfIn++;-->
    <!--                if(countOfIn == allWithoutLoad){-->
    <!--                    VK.Widgets.Group("vk_groups", {mode: 0, width: "380", height: "275"}, 23678993);-->
    <!--                    $("img.lazy").lazyload({-->
    <!--                    });-->
    <!--                    ShowCurrentTime();-->
    <!--                    write_calendar(0, 0);-->
    <!--                }-->
    <!--            }-->
    <!--            function loadOut(){-->
    <!--                countOfOut++;-->
    <!--                if(countOfOut == 3){-->
    <!--//                VK.init({apiId: 2649413, onlyWidgets: true});-->
    <!--//                VK.init({apiId: 2340231, onlyWidgets: true}); //old-->
    <!--                    --><?php //if(isset($News->dateNewsCurrent) && $News->dateNewsCurrent > 0){?>
    <!--//                    VK.init({apiId: 2340231, onlyWidgets: true});//old-->
    <!--//                    --><?php ////}else{?>
    <!--//                    VK.init({apiId: 5037477, onlyWidgets: true});//new-->
    <!--//                    --><?php ////}?>
    <!--//                    $('#likeVk').html(VK.Share.button(false, {type: "round", text: "Нравится"}));-->
    <!--//-->
    <!--//                    VK.Widgets.Comments("vk_comments", {-->
    <!--//                        limit: 30,-->
    <!--//                        width: "785",-->
    <!--//                        attach: "*",-->
    <!--//                        onChange: addComment});-->
    <!--//                    VK.Observer.subscribe('widgets.comments.new_comment', function(num, last_comment, date)-->
    <!--//                    {-->
    <!--//                        addComment(num, last_comment, date);-->
    <!--//                        //$.post("/comment.php", { n: num, c: last_comment, id: ".$id_element.", t: 1 } );-->
    <!--//                    });-->
    <!--//                }-->
    <!--//            }-->
    <!--//            function loadScript(url, callback){-->
    <!--//                var script = document.createElement("script");-->
    <!--//                script.type = "text/javascript";-->
    <!--//                if (script.readyState){  //IE-->
    <!--//                    script.onreadystatechange = function(){-->
    <!--//                        if (script.readyState == "loaded" ||-->
    <!--//                            script.readyState == "complete"){-->
    <!--//                            script.onreadystatechange = null;-->
    <!--//                            callback();-->
    <!--//-->
    <!--//                        }-->
    <!--//                    };-->
    <!--//                } else {  //Others-->
    <!--//                    script.onload = function(){-->
    <!--//                        callback();-->
    <!--//                    };-->
    <!--//                }-->
    <!--//                script.src = url;-->
    <!--//                document.body.appendChild(script);-->
    <!--//            }-->
    <!--//-->
    <!--//            for(var i = 0; i < allLoadIn.length; i++) {-->
    <!--//                loadScript(allLoadIn[i], loadIn);-->
    <!--//            }-->
    <!--//            for(var i = 0; i < allLoadIn.length; i++) {-->
    <!--//                loadScript(allLoadOut[i], loadOut);-->
    <!--//            }-->
    <!--//        </script>-->
    <!--//-->
    <!--//-->
    <!--//        <div id="ok_shareWidget" class="classmates"></div>-->
            <script>
    //            !function (d, id, did, st) {
    //                var js = d.createElement("script");
    //                js.src = "https://connect.ok.ru/connect.js";
    //                js.onload = js.onreadystatechange = function () {
    //                    if (!this.readyState || this.readyState == "loaded" || this.readyState == "complete") {
    //                        if (!this.executed) {
    //                            this.executed = true;
    //                            setTimeout(function () {
    //                                OK.CONNECT.insertShareWidget(id,did,st);
    //                            }, 0);
    //                        }
    //                    }};
    //                d.documentElement.appendChild(js);
    //            }(document,"ok_shareWidget",document.URL,"{width:190,height:30,st:'rounded',sz:20,ck:2}");
    //        </script>
    <!--    </div>-->

    <!--    --><?//if(!empty($date)){?>
    <!--        <div class="news-date head_data_line">--><?//=$date?><!--</div>--><?//
    //    }?>

    <!--    --><?//if(!empty($images)) echo $images;
    /*if(!empty($tags)){
        */?><!--<div class="news-full-img"><?/*=$tags*/?></div>--><?/*
    }*/

    ?>

    <div class="row">
        <h1 class="news-full-name"><?=stripslashes($value['name']);?></h1>
    <!--    --><?// var_dump($value); ?>
        <?if(!empty($value['start_date'])){?>
            <div class="date">
                <span><?=$date['day']?></span>
                <span><?=$date['month']?></span>
                <?if(!empty($link)){?>
                    <a href="<?=$link?>" title="<?=$name?>">< Read</a>
                <?}?>
            </div>
            <div class="info">
    <!--           --><?// if(!empty($full) || 1){
                ?><div class="news-full-text"><?=$value['full'];?></div><?
    //            }?>
            </div>
        <?}?>
    </div>

    <?

    ?>
            <!--<div class="social_head2">
                <div id="fb-root2" class="classmates"></div>
                <script>(function(d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (d.getElementById(id)) return;
                        js = d.createElement(s); js.id = id;
                        js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1";
                        fjs.parentNode.insertBefore(js, fjs);
                    }(document, 'script', 'facebook-jssdk2'));</script>
                <?php
    /*            if(isset($_SERVER['REQUEST_URI']))
                    $uri = 'http://'.NAME_SERVER.$_SERVER['REQUEST_URI'];
                else $uri = 'http://'.NAME_SERVER;
                */?>
                <div class="fb-share-button classmates" data-href="<?php /*echo $uri*/?>" data-type="button_count"></div>


                <div id="likeVk2" class="classmates"></div>
                <script type="text/javascript">
                    var countOfIn1 = 0,
                        countOfOut1 = 0,
                        allLoadIn1 = [
                            '/include/js/openapi.js',
                            '/sys/js/cal_frontend/cldr.js',
                            '/sys/js/ajax/poller.js',
                            '/sys/js/ajax/ajax.js',
                            '/include/js/swfobject.js',
                            '/include/js/CurrentTime.js',
                            '/include/js/lazyload.js'
                        ],
                        allLoadOut1 = [
    //                'http://vk.com/js/api/openapi.js?47',
                            'http://vk.com/js/api/share.js?11',

                            'https://w.uptolike.com/widgets/v1/zp.js?pid=1261512',
                            'http://www.google.com/coop/cse/brand?form=cse-search-box&lang=ru',
                            'https://www.gstatic.com/swiffy/v5.0/runtime.js'
                        ],
                        allWithoutLoad1 = allLoadIn1.length;
                    function loadIn1(){
                        countOfIn1++;
                        if(countOfIn1 == allWithoutLoad1){
                            VK.Widgets.Group("vk_groups", {mode: 0, width: "380", height: "275"}, 23678993);
                            $("img.lazy").lazyload({
                            });
                            ShowCurrentTime();
                            write_calendar(0, 0);
                        }
                    }
                    function loadOut1(){
                        countOfOut1++;
                        if(countOfOut1 == 3){
    //                VK.init({apiId: 2649413, onlyWidgets: true});
    //                VK.init({apiId: 2340231, onlyWidgets: true}); //old
                            <?php /*if(isset($News->dateNewsCurrent) && $News->dateNewsCurrent > 0){*/?>
                            VK.init({apiId: 2340231, onlyWidgets: true});//old
                            <?php /*}else{*/?>
                            VK.init({apiId: 5037477, onlyWidgets: true});//new
                            <?php /*}*/?>
                            $('#likeVk2').html(VK.Share.button(false, {type: "round", text: "Нравится"}));

                            VK.Widgets.Comments("vk_comments", {
                                limit: 30,
                                width: "785",
                                attach: "*",
                                onChange: addComment});
                            VK.Observer.subscribe('widgets.comments.new_comment', function(num, last_comment, date)
                            {
                                addComment(num, last_comment, date);
                                //$.post("/comment.php", { n: num, c: last_comment, id: ".$id_element.", t: 1 } );
                            });
                        }
                    }
                    function loadScript1(url, callback){
                        var script = document.createElement("script");
                        script.type = "text/javascript";
                        if (script.readyState){  //IE
                            script.onreadystatechange = function(){
                                if (script.readyState == "loaded" ||
                                    script.readyState == "complete"){
                                    script.onreadystatechange = null;
                                    callback();

                                }
                            };
                        } else {  //Others
                            script.onload = function(){
                                callback();
                            };
                        }
                        script.src = url;
                        document.body.appendChild(script);
                    }

                    for(var i = 0; i < allLoadIn1.length; i++) {
                        loadScript1(allLoadIn1[i], loadIn1);
                    }
                    for(var i = 0; i < allLoadIn1.length; i++) {
                        loadScript1(allLoadOut1[i], loadOut1);
                    }
                </script>


                <div id="ok_shareWidget2" class="classmates"></div>
                <script>
                    !function (d, id, did, st) {
                        var js = d.createElement("script");
                        js.src = "https://connect.ok.ru/connect.js";
                        js.onload = js.onreadystatechange = function () {
                            if (!this.readyState || this.readyState == "loaded" || this.readyState == "complete") {
                                if (!this.executed) {
                                    this.executed = true;
                                    setTimeout(function () {
                                        OK.CONNECT.insertShareWidget(id,did,st);
                                    }, 0);
                                }
                            }};
                        d.documentElement.appendChild(js);
                    }(document,"ok_shareWidget2",document.URL,"{width:190,height:30,st:'rounded',sz:20,ck:2}");
                </script>
            </div>-->
            <div class="tegs">
                <?if($tags){?>
                    <span><?=$text_tegs?></span><?=$tags?>
                    <h2><?=$such_news?></h2>
                <?}?>
            </div>
        <?echo $news_by_tags;?>
        <!-- Put this div tag to the place, where the Comments block will be -->
        <div id="vk_comments" class="vk_comments"></div>
        <script type="text/javascript">
            VK.Widgets.Comments("vk_comments", {limit: 10, width: "665", attach: "*"});
        </script>
    </div>
</div>