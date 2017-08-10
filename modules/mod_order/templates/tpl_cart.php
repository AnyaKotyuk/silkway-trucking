<a class="icoCart" href="<?=_LINK?>order/" title="<?=$cart_text;?>">&nbsp;</a>

<div class="count">
    <span class="price">В корзине <?=$tow;?></span> товаров<br />
   на сумму
    <?
    if($tow==0){
        ?><span class="price">00.00</span> <?=$curr?><?
    }
    else{
        ?><span class="price"><?=$sum?></span> <?=$curr?><?
    }
    ?>
</div>