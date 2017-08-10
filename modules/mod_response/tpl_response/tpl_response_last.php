<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 23.05.13
 * Time: 10:28
 * To change this template use File | Settings | File Templates.
 */
//var_dump($arr);
?><div class="response-main">
    <h2><?=$h2?></h2>
    <div class="response-main-fon-for-item">
        <?for($i=0;$i<$rows;$i++){
            $row = $arr[$i];
            if($i>0){?><div class="response-main-one-item-probel"></div><?}
            ?><div class="response-main-one-item">
                <div class="response-main-one-item-fon">
                    <div class="response-main-one-item-name">
                        <a href="<?=$row['path']?>" title="<?=$row['autor']?>"><?=$row['autor']?></a>
                    </div>
                    <div class="response-main-one-item-short"><?=$row['short']?></div>
                </div>
                <div class="response-main-clen"></div>
            </div><?
        }?>
    </div>
</div><?