<?php
/**
 * Created by PhpStorm.
 * User: roma
 * Date: 14.04.14
 * Time: 9:56
 */
?>
<? $slider_n = count($arr); ?>
<div class="jumbotron slider">
    <div id="carousel-header" class="carousel slide" data-ride="carousel">
        <!-- Маркеры слайдов -->
        <ol class="carousel-indicators">
            <li data-target="#carousel-header" data-slide-to="0" class="active"></li>
            <? for($n = 1; $n < $slider_n; $n++){ ?>
                <li data-target="#carousel-header" data-slide-to="<?=$n;?>"></li>
            <?}?>
        </ol>

        <!-- Содержимое слайдов -->
        <div class="carousel-inner">
<!--            --><?// print_r($arr); ?>
            <? $n = 0; for($n = 0; $n < $slider_n; $n++){?>
                <div class="item <? if($n == 0) echo 'active'; ?>">
                    <?
                    if(!empty($arr[$n]['href'])):?><a href="<?=$arr[$n]['href']?>"<?if($arr[$n]['target']){?> target="_blank"<?}?>><?
                        endif;?>
                        <img src="<?=$arr[$n]['path']?>" alt="<?=$arr[$n]['img']?>" height="345"/>
                        <?
                        if(!empty($arr[$n]['href'])):?></a><?
                endif;?>
                </div>
                <? }?>

        </div>



    </div>
</div>