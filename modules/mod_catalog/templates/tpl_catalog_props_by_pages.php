<?if(!empty($Catalog->id_cat)){
?><div class="compare-panel" id="comparePanelFon"<?
if(empty($compare)){?> style="display: none" <?}
?>><div id="comparePanel"><?=$compare;?></div>
    <div class="compare-key">
        <a href="<?=$Catalog->catLink;?>compare/" class="href-compare"><?=$Catalog->multi['TXT_COMPARE_TRUE']?></a>
        <div class="clear-compare" onclick="clearCookie(<?=$Catalog->id_cat?>);"><?=$Catalog->multi['TXT_CLEAR_COMPARE']?></div>
    </div>
</div><?
}

echo $sore;?>
<div class="categoryContent">
    <?
    foreach ($props as $prop):
            //echo View::factory('/modules/mod_catalog/templates/tpl_prop_by_pages_single.php')
            //    ->bind('prop',$prop);
            $id = $prop['id'];
            ?>
            <div class="listProdItem">
                <? if(!empty($prop['image'])): ?>
                    <div class="itemImg">
                        <a href="<?=$prop['link']?>" title="<?=$prop['name']?>">
                            <img src="<?=$prop['image']?>" alt="" title=""/>
                        </a>
                    </div>
                <?endif;?>
                <div><a href="<?=$prop['link']?>" title="<?=$prop['name']?>"><?=$prop['name']?></a></div>
                <div class="price">
                    <?=$Catalog->multi['FLD_PRICE'];?> <?=$prop['price']?>
                </div>
                <form action="#" method="post" name="catalog" id="catalog<?=$id;?>">
                    <div class="quantityField">
                        <input type="text" size="2" value="1" class="quantity" onkeypress="return me()" id="productId[<?=$id;?>]" name="productId[<?=$id;?>]" maxlength="2"/>
                    </div>
                    <a href="#" id="multiAdds<?=$id;?>" onclick="addToCart('catalog<?=$id;?>', 'cart', '<?=$id;?>');return false;">
                        <img src="/images/design/btnBuy.png" alt="<?=$Catalog->multi['TXT_BUY'];?>" title="<?=$Catalog->multi['TXT_BUY'];?>"/>
                    </a>
                </form>
                <div id="al<?=$id;?>" class="al"></div>
                <?=View::factory('/modules/mod_catalog/templates/param/tpl_compare.php')
                    ->bind('Catalog', $Catalog)
                    ->bind('id',$id)
                    ->bind('id_cat',$prop['id_cat'])
                    ->bind('linkCat',$prop['linkCat'])
                    ->bind('prop',$prop);?>
            </div>
            <?
    endforeach; ?>

</div>



<?if(!empty($pagination)):?>
<div class="links">
    <?=$pagination?>
</div>
<?endif;?>