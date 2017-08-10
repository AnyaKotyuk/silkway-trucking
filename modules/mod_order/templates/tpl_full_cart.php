<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 10.07.13
 * Time: 17:42
 * To change this template use File | Settings | File Templates.
 */
?>
<table cellspacing="0" cellpadding="6" border="0" width="100%" class="full-cart-tbl">
<tr class="main-tr">
    <th colspan="2"><span><?=$multi['TXT_NAME_PROP'];?></span></th>
    <th><span><?=$multi['FLD_ART_NUM'];?></span></th>
    <th><span><?=$multi['FLD_QUANTITY'];?></span></th>
    <th><span><?=$multi['TXT_PRICE'];?></span></th>
    <th><span><?=$multi['FLD_SUMA']?></span></th>
    <th><span><?=$multi['TXT_DELL'];?></span></th>
</tr>
<?
for($i=0;$i<$rows;$i++){
    $row = $arr[$i];
    $name = $row['name'];
    ?><tr id="propText<?=$row["id"];?>">
        <td>
            <a href="<?=$row['link']?>" title="<?=$name?>">
                <img src="<?=$row['src']?>" alt="<?=$row['img_alt']?>" title="<?=$row['img_title']?>">
            </a>
        </td>
        <td class="order-prop-text">
            <div class="order-prop-name"><a href="<?=$row['link']?>" title="<?=$name?>"><?=$name?></a></div>
            <?
            if(!empty($row['name_brend'])){?><div class="order-prop-short"><?=$multi['TXT_BREND'].': '.$row['name_brend']?></div><?}
            if(!empty($row['number_name'])){?><div class="order-prop-short"><?=$row['number_name']?></div><?}?>
        </td>
        <td class="order-prop-art"><?=$row['art_num']?></td>
        <td>
            <input type="text" value="<?=$row['quantity']?>" id="quantity<?=$row['id']?>" name="quantity[<?=$row['id']?>]"
                   class="order-prop-quantity validate[required]"
                   maxlength="2" size="2" onkeypress="return me(event)" onkeyup="recalculation(<?=$row['id']?>)">
        </td>
        <td class="order-price" id="price<?=$row['id']?>"><?=$row['price']?></td>
        <td class="order-price" id="summ<?=$row['id']?>"><?=$row['summa']?></td>
        <td>
            <b onclick="ajaxRemoveProductInCart(<?=$row["id"];?>)"
               title="<?=$multi['TXT_KILL_PROP'].' '.$name?>">
                <img src="/images/design/order-kill.png" alt="">
            </b>
        </td>
    </tr><?
}?>
</table>
<div class="bonus-in-cart-price-all">
    <span>Общая стоимость:</span> <label id="orderSummAll"><?=$summ_all?></label>
</div>
<div class="order-form-one-kill"><span onclick="orderclear();"><?=$multi['TXT_CART_KILL']?></span></div>
<a href="<?=_LINK?>order/step2/"><?=$multi['TXT_NEXT_STEP']?></a><?