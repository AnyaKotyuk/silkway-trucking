<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 30.07.13
 * Time: 15:54
 * To change this template use File | Settings | File Templates.
 */
?><div class="body-left-name"><?=$name_block?></div><?
?><div class="filter-block">
    <form name="find_by_param" method="post" action="<?=$catLink?>">
        <div class="filter-select-block"><?=$strSel;?></div>
        <div class="filter-block-all-param"><?=$str;?></div>
        <div class="filter-block-brand"><?=$strBrand;?></div>
        <div class="filter-block-price"><?=$strPrice;?></div>
    </form>
</div><?