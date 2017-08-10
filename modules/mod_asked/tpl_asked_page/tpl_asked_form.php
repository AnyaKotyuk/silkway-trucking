<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 13.09.13
 * Time: 15:54
 * To change this template use File | Settings | File Templates.
 */

?><form method="post" action="<?=_LINK;?>ask/add/" name="form_mod_asked" id="form_mod_asked" enctype="multipart/form-data">
    <?//$Ask->ShowErr();?>
    <div class="feedback-one-item">
        <div class="feedback-label">
            <label for="name"><?=$Ask->multi['_TXT_NAME'];?>: <span class="red">*</span></label>
        </div>
        <div class="feedback-input">
            <?$Ask->Form->TextBox('asked_author', stripslashes($Ask->author),' class="validate[required]"','asked_author');?>
        </div>
    </div>
    <?if(ASKED_EMAIL){?>
    <div class="feedback-one-item">
        <div class="feedback-label">
            <label for="name"><?=$Ask->multi['_TXT_E_MAIL'];?>: <span class="red">*</span></label>
        </div>
        <div class="feedback-input">
            <?$Ask->Form->TextBox('asked_email', stripslashes($Ask->email),' class="validate[required,custom[email]]"','asked_email');?>
        </div>
    </div>
    <?}
    if(ASKED_CATEGORY){?>
        <div class="float-container">
            <div class="width25 float-to-left"><?=$Ask->multi['_FLD_CATEGORY'];?>:</div>
            <div class="width75 float-to-right">
                <?/*$Ask->Form->TextBox('asked_email', stripslashes($Ask->email));?>
                    <span class="form-hint red"><?$Ask->Form->ShowMessage('asked_category', $Ask->Err);?></span>
                     <?*/
                $Ask->Spr->ShowInComboBox( TblModAskedCat, 'asked_category', $Ask->asked_category, 40, $Ask->multi['FLD_SELECT_CHAPTER'] );
                ?>
            </div>
        </div>
    <?}
    if(ASKED_RATING){
        ?><div class="feedback-one-item">
            <div class="feedback-label">
                <label for="question"><?=$Ask->multi['TXT_PUT_RATING'];?>: <span class="red">*</span></label>
            </div>
            <div class="feedback-input" style="position: relative">
                <div id="resResize" class="asked-key-rating-img"><?
                for($i=1;$i<6;$i++){
                    ?><img src="/images/design/rating+.png" alt="" title=""><?
                }
                ?></div>
                <div class="asked-key-rating" onmouseout="killRatingSel()">
                    <div class="asked-key-rating-first-item" onmouseover="chengMoveRating(0)"
                         onclick="selRating(0)"></div><?
                    for($i=1;$i<6;$i++){
                        ?><div class="asked-key-rating-one-item" onmouseover="chengMoveRating(<?=$i?>)"
                               onclick="selRating(<?=$i?>)"></div><?
                    }
                ?></div>
                <div class="asked-rating-minus" id="imgPlus"><img src="/images/design/rating+.png" alt="" title=""></div>
                <div class="asked-rating-minus" id="imgMinus"><img src="/images/design/rating-.png" alt="" title=""></div>
                <div class="asked-rating-field">
                    <input type="tel" id="rating" name="rating" value="" class="validate[required]" />
                </div>
            </div>
        </div><?
    }?>
    <div class="feedback-one-item">
        <div class="feedback-label">
            <label for="question"><?=$Ask->multi['TXT_RESPONSE'];?>: <span class="red">*</span></label>
        </div>
        <div class="feedback-input">
            <?$Ask->Form->TextArea('question', stripslashes($Ask->question), 6, 38,' class="validate[required]" id="question"');?>
        </div>
    </div>

    <?/*include_once(SITE_PATH.'/include/kcaptcha/kcaptcha.php');?>
           <div class="floatContainer">
                <div class="width25 floatToLeft"><img src="/include/kcaptcha/index.php?<?=session_name()?>=<?=session_id()?>" alt="" /></div>
                <div class="width75 floatToRight">
                    <div style="font-size:10px;"><?=$Ask->multi['_TXT_CAPTCHA'];?></div>
                    <input type="text" name="captchacodestr" class="captchacode"/>
                </div>
           </div>*/?>

    <div class="feedback-one-item" style="overflow: hidden">
        <div class="feedback-input">
            <div id="submitOld">
                <input type="submit" name="submit" value="<?=$Ask->multi['_TXT_SEND']?>" class="button" onclick="return verify();">
            </div>
            <div class="feedback-submit" id="submitReal" onclick="verify();">
                <div class="feedback-submit-img"></div>
                <div class="feedback-submit-text"><?=$Ask->multi['_TXT_SEND']?></div>
            </div>
            <script type="text/javascript">
                $('#submitOld').hide();
                $('#submitReal').show();
            </script>
        </div>
    </div>
</form><?