<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 20.06.13
 * Time: 13:19
 * To change this template use File | Settings | File Templates.
 */
?>
<div class="top-news top-news2">
    <?
    //echo $rows;
    //var_dump($arr);
    for( $i=0; $i<$rows; $i++ )
    {
        $row = $arr[$i];
        ?>
        <div class="news-last-top">
            <?if(!empty($row['img'])){?>
                <div class="news-last-img">
                    <a href="<?=$row['link'];?>" title="<?=$row['name']?>">
                        <img src="<?=$row['src'];?>" alt="<?=$row['img_alt']?>"  title="<?=$row['img_title']?>"/>
                    </a>
                </div>
            <?}
            if(!empty($row['date'])){
                ?><div class="news-date"><?=$data?> <?=$row['date'];?></div><?
            }
            if(!empty($row['Name_cat'])){
                ?><div class="news-name-cat"><a href="<?=$row['link_cat'];?>"><?=$row['Name_cat']?></a></div><?
            }?>
            <div class="news-last-name">
                <a href="<?=$row['link'];?>" title="<?=$row['name']?>"><?=$row['name']?></a>
            </div>
            <div class="news-last-short">
                <?=$row['short']?>
            </div>
        </div>

<?php
    }
    if(!empty($pages)) echo $pages;
    ?>
</div>
<?
