<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 30.07.13
 * Time: 15:57
 * To change this template use File | Settings | File Templates.
 */
?><div class="sore-panel">
    <div class="sore-panel-name"><?=$name_block?>:</div>
    <div class="sore-panel-one-item current">
        <?if($asc_desc=='desc'){?>
        <span><?=$name_elem1?></span>
        <?}else{
            ?><a href="<?=$catLink.$link_href?>" title="<?=$name_elem1?>"
                 onclick="return gelPropConetnt('<?=$catLink?>','<?=$link_href?>');"><?=$name_elem1?></a><?
        }?>
    </div>
    <div class="sore-panel-probel">|</div>
    <div class="sore-panel-one-item">
        <?if($asc_desc=='asc'){?>
            <span><?=$name_elem2?></span>
        <?}else{
            ?><a href="<?=$catLink.$link_href?>" title="<?=$name_elem2?>"
                 onclick="return gelPropConetnt('<?=$catLink?>','<?=$link_href?>');"><?=$name_elem2?></a><?
        }?>
    </div>
</div><?