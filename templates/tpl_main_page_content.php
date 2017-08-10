<?php
/**
 * @uses: /include/classes/PageUser.class.php
 */
?>
<!--<div class="row">-->
<!--    <div class="col-md-12">-->
<!--        --><?php
//        $PageUser->informerLayout->showMainSlider();
//        ?>
<!--    </div>-->
<!--</div>-->



            <? $PageUser->informerLayout->showMainSlider();?>
<!--            <div class="item active">-->
<!--                <a href="#">-->
<!--                    <img src="images/design/slider1.jpg" alt="...">-->
<!--                </a>-->
<!---->
<!--            </div>-->


</div>
<div class="company padd">
    <div class="container text-center">
        <div class="col-md-12">
            <h1><?=$main_content['company']['pname'];?></h1>
            <p><?=$main_content['company']['content'];?></p>

        </div>
    </div>
</div>
<!--<div class="services">-->
<!--    <div class="jumbotron text-center">-->
<!--        <h1>--><?//=$main_content['service']['name'];?><!--</h1>-->
<!--        <p>Distribution of dangerous goods under the ADR with a fleet of trucks certified. We verify the conformity of the goods and documents entrusted to us... .and we... guarantee that all</p>-->
<!--    </div>-->
<!--</div>-->
<div class="services padd">
    <div class="container">
        <h1><?=$main_content['service']['pname'];?></h1>
        <lr>
    <!--            --><?// print_r($main_content['service_list']);
                $lang_id = $PageUser->lang_id;
                foreach($main_content['service_list'] as $key=>$item){ //print_r($item); ?>
                <ld>
                    <a href="/service/">
                    <?
    //                echo $item['img2'].'<br>';
    //                var_dump($PageUser->Spr);
                    if($item['img2'])
                        $src = ImageK::getResizedImg($PageUser->Spr->GetImgPath($item['img2']), 'size_height=52', 85, NULL);
    //                    echo $src;
                        //$PageUser->Spr->ShowImage($PageUser->Spr->spr, NULL, $item['img2'], 'size_height=10', 85);
                    echo '<img src="'.$src.'" height="50">';
                    ?>
                    <h5><?=$item['name'];?></h5>
                        <?
                        $desc = iconv("windows-1251", "UTF-8", $item['shorthtml']);
                        $desc = substr($desc, 0, 120);
                        $desc = iconv("UTF-8", "windows-1251", $desc);
                        if($desc != $item['shorthtml']) $desc .= ' ...';
                        ?>
                    <p><?=$desc;?></p>
                    </a>
                </ld>
                <?}?>
        </lr>
    </div>
</div>


<? $PageUser->News->showNewsLastWidget(5); ?>
<div class="map jumbotron">
    <div class="map-cover"></div>
    <iframe src="<?=$PageUser->FrontendPages->getSpecContentByCod(3, true);?>" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
</div>

<!--<div class="news">-->
<!---->
<!--    <h1>LAST NEWS</h1>-->
<!--    <div id="carousel-news" class="container text-center"  data-ride="carousel">-->
<!--        <ol class="carousel-indicators">-->
<!--            <li data-target="#carousel-news" data-slide-to="0" class="active"></li>-->
<!--            <li data-target="#carousel-news" data-slide-to="1"></li>-->
<!--            <li data-target="#carousel-news" data-slide-to="2"></li>-->
<!--        </ol>-->
<!--        <div class="row ">-->
<!--            <div class="col-md-4 item active">-->
<!--                <div class="date">-->
<!--                    <span>05</span>-->
<!--                    <span>January</span>-->
<!--                </div>-->
<!--                <div class="info">-->
<!--                    <h5>Truck transport services on Italian terr and internetional for</h5>-->
<!--                    <div>A strong international experience and a great operational flexibility. This is the DNA of Euroasian Transport Solution Srl, which translates into an extremely reactive and proactive approach to the needs of the market. Euroasian Transport Solution Srl is able to provide Customs services and...</div>-->
<!--                </div>-->
<!--            </div>-->
<!--        -->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<!--<div class="row">-->
<?//if (defined("MOD_NEWS") AND MOD_NEWS){?>
<!--    <div class="col-xs-12 col-sm-6 col-md-6">-->
<!--        --><?php //$PageUser->News->showNewsLastWidget(5);?>
<!--    </div>-->
<?//}?>
<?//if (defined("MOD_ARTICLE") AND MOD_ARTICLE){?>
<!--    <div class="col-xs-12 col-sm-6 col-md-6">-->
<!--        --><?php //$PageUser->Article->showLastArticlesWidget(3);?>
<!--    </div>-->
<?//}?>
<!--</div>-->
<!--<div class="row">-->
<!--    <div class="col-md-12 body-center">-->
<!--        --><?php
//        if(!empty($h1)):?>
<!--            <h1>--><?php //echo $h1; ?><!--</h1>-->
<!--        --><?php //endif;?>
<!---->
<!--        --><?php
//        if(!empty($breadcrumb)):?>
<!--            <div class="path">--><?//= $breadcrumb; ?><!--</div>-->
<!--        --><?php //endif; ?>
<!---->
<!--        --><?php
//        echo $content;
//        ?>
<!--    </div>-->
<!--</div>-->