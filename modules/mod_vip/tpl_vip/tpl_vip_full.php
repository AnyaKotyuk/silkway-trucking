<?=$content;
if(empty($content)){
?>
<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 30.07.14
 * Time: 17:58
 */

if(!empty($arrvip['src'])){?>
    <div class="left-side img_vip">
        <a href="<?=$arrvip['path']?>" class="fancybox" title="<?=$arrvip['name_html']?>" >
            <img src="<?=$arrvip['src']?>" alt="<?=$arrvip['name_html']?>" title="<?=$arrvip['name_html']?>"  />
        </a>
    </div>
<?}?>
<?if(!empty($arrvip['shorthtml'])){?>
<div class="right-bl">
    <?=$arrvip['shorthtml']?>
</div><?}?>
<?if(!empty($arrvip['descr']) && strlen($arrvip['descr'])>424){?>
<div class="description">
    <?=$arrvip['descr']?>
</div><?}?>

<?=$formFeedBack?>
<?}?>