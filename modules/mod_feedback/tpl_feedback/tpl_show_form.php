<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 14.06.13
 * Time: 15:42
 * To change this template use File | Settings | File Templates.
 */
    if(!is_ajax){?>
        <div id="feedback" class="fb container"><?
    }?>
<div class="subBody">
    <h2><?=$Feedback->multi['TXT_FEEDBACK'];?></h2>
    <form method="post" action="<?=_LINK;?>contacts/send/" name="form_mod_feedback" id="form_mod_feedback" enctype="multipart/form-data">
        <?$Feedback->showErr();?>
        <input type="hidden" name="task" value="send" />
        <div class="row">
            <div class="col-md-3">
                <?
                $Feedback->showFormOneField('name',$Feedback->multi['_TXT_NAME'],$Feedback->name,'validate[required]',true);
                if($Feedback->is_surname==1){
                $Feedback->showFormOneField('surname',$Feedback->multi['TXT_SURNAME'],$Feedback->surname,'validate[required]',true);
                }
                if($Feedback->is_phone==1){
                $Feedback->showFormOneField('tel',$Feedback->multi['_TXT_TEL'],$Feedback->tel,'validate[required,custom[phone]]',true);
                }
                if($Feedback->is_email==1){
                $Feedback->showFormOneField('e_mail',$Feedback->multi['_TXT_E_MAIL'],$Feedback->e_mail,'validate[required,custom[email]]',true);
                }
                if($Feedback->is_fax==1){
                $Feedback->showFormOneField('fax',$Feedback->multi['_TXT_FAX'],$Feedback->fax);
                }
                ?>
            </div>
            <div class="col-md-8">
                <? $Feedback->showFormOneField('question',$Feedback->multi['_TXT_MESSAGE'],$Feedback->question,'validate[required]',true,'textarea');?>
            </div>

        <? if($Feedback->is_files==1){
            ?>
            <div class="floatContainer">
                <div class="width25 floatToLeft"><?=$Feedback->multi['ATTACH_FILE'];?>:</div>
                <div class="width75 floatToRight">
                    <input type="file" name="filename" />
                    <br /><span style="font-size: 10px;"><?=$Feedback->multi['ATTACH_FILE_DESCR'];?></span>
                </div>
            </div>
        <?
        }
        if($Feedback->is_captcha==1){
            include_once(SITE_PATH.'/include/kcaptcha/kcaptcha.php');
            ?><div><img src="/include/kcaptcha/index.php?<?=session_name()?>=<?=session_id()?>" alt="" /></div><?
            $Feedback->showFormOneField('captchacodestr',$Feedback->multi['_TXT_CAPTCHA'],NULL,'validate[required]',true);
        }?>
        <div class="col-md-1 submit">
                <?
                $Feedback->Form->Button('submit',$Feedback->multi['_TXT_SEND'], 50,' onclick="return verify('.$Feedback->is_send_ajax.');" ');?>

        </div>
        </div>
    </form>
</div>
<?
if(!is_ajax){
?></div>


<?}?>
</div>