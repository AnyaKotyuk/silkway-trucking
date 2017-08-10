<?=$content;
if(empty($content)){
?>

<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 30.07.14
 * Time: 12:00
 */
echo $form;
?><ul class="list-of-tours">
    <?foreach($arrvip as $key=>$row):?>
    <li class="tour-tiser">
        <h3 class="tour-title"><a href="<?=$row['link']?>"><?=$row['name']?></a></h3>

        <div class="left-side">
            <?if(isset($row['img2']) && !empty($row['img2'])):?>
            <a href="<?=$row['link']?>" title="<?=$row['name_html']?>">
                <img src="<?=$row['src']?>" alt="<?=$row['name_html']?>"
                     title="<?=$row['name_html']?>"  />
            </a>
            <?endif;?>
        </div>
        <div class="right-side">
            <?=$row['shorthtml']?>
            <a href="<?=$row['link']?>"><?=$more;?></a>

        </div>
    </li>
    <?endforeach;?>
</ul>
<?
echo $pages;
}