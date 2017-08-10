<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 13.09.13
 * Time: 18:16
 * To change this template use File | Settings | File Templates.
 */
?><div class="asked-form">
<?if(ASKED_SHOW_FORM_HIDE){?>
    <div class="asked-key-show-form-fon" id="formAskedFonBig">
        <div class="asked-key-show-form" onclick="showFormAsked();">
            <span class="asked-key-show-form-text"><?=$Ask->multi['TXT_ADD_ASKED']?></span>
        </div>
    </div>
<?}?>
<div class="<?if(ASKED_SHOW_FORM_HIDE){?>asked-form-fon-hide<?}else{?>asked-form-fon<?}?> feedback-left-form"
     id="formAskedFon">
    <div class="krest" onclick="closeAskedForm();"></div>
    <h3><?=$Ask->multi['TXT_YOR_ASKED']?></h3>
    <div class="container" id="container_asked"><?
    echo View::factory('/modules/mod_asked/tpl_asked_page/tpl_asked_form.php')
    ->bind('Ask',$Ask);
    ?>
    </div>
</div>
</div>
<?