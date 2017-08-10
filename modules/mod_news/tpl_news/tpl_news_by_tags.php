<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 18.06.13
 * Time: 14:49
 * To change this template use File | Settings | File Templates.
 */
?>
<div class="news-fon">
<?for ($i = 0; $i < $rows; $i++) {
    $row = $arr[$i];
    $name = $row['name'];
    $link = $row['link'];
    $img = '<img src="' . $row['src'] . '" alt="' . $row['img_alt'] . '" title="' . $row['img_title'] . '" />';?>
    <div class="news-one-item">
        <?if (!empty($row['img'])) { ?>
            <div class="news-img-fon">
                <div class="news-img">
                    <? if (!empty($link)) {?>
                        <a href="<?= $link ?>" title="<?= $name ?>"><?= $img ?></a>
                    <?} else {
                        echo $img;
                    }?>
                </div>
            </div>
        <?}?>
        <div class="news-text">
            <div class="news-name">
                <? if (!empty($link)) { ?>
                    <a href="<?= $link ?>" title="<?= $name ?>"><?= $name ?></a>
                    <?
                } else {
                    echo $name;
                }?>
            </div>
        </div>
    </div>
    <?}?>
</div>
