<?php
/**
 * Created by PhpStorm.
 * User: user1
 * Date: 22.10.15
 * Time: 11:12
 */
?>
<h3><?=$day?></h3>
<?for($i=0;$i<$rows;$i++) {
    $row = $arr[$i];
    /*var_dump($row);*/
?>
<div class="archive-news">
    <div class="head-archive-news">
        <a href="<?=$row['link_cat']?>"><?=$row['Name_cat']?></a>
    </div>
    <div class="body-archive-news">
        <div class="news-date"><?=$row['date']?></div>
        <h2><?=$row['name']?></h2>
        <?
        if(!empty($row['src'])){
        ?>
            <div class="news-img">
                <a href="<?=$row['link']?>" title="<?=$row['img_title']?>">
                    <img src="<?=$row['src']?>" alt="<?=$row['img_alt']?>" title="<?=$row['img_title']?>" />
                </a>
            </div>
        <?}?>
        <div class="news-short">
            <?=$row['short']?>
        </div>
    </div>
</div>
<?}