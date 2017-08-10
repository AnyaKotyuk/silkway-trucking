<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 01.06.13
 * Time: 12:19
 * To change this template use File | Settings | File Templates.
 */
/*<a class="btnAsk" href="<?=_LINK?>ask/show_form/"><?=$multi['TXT_ADD_QUESTION'];?>&nbsp;→</a><?*/
?><div class="err-fon"><?
    for($i = 0; $i < $rows; $i++) {
        $row = $arr[$i];
        $question = $row['question'];
        $fullAnswer = $row['fullAnswer'];
        $flag = $row['flag'];
        ?><div class="asked-one-item" id="oneItemFonBlock<?=$i?>">
            <div class="question-content" id="questionBlock<?=$i?>" <?if(ASKED_HIDE_ANSWER){?> onclick="showAnswer(<?=$i?>);"<?}?>><?
            if(ASKED_SHOW_NUMBER){?><span class="red-color"><?=($i+1)?>.</span> <?}
            if(ASKED_SHOW_AUTOR){?><span class="asked-author"><?=$row['author']?></span> <?}
            if(ASKED_SHOW_DATE){?><span class="asked-dttm"><?=$row['dttm']?></span> <?}
            if(ASKED_RATING){?><span class="asked-rating"><?
                for($j=0;$j<$row['rating+'];$j++){
                    ?><img src="/images/design/rating+.png" alt="" title="" /> <?
                }
                for($j=0;$j<$row['rating-'];$j++){
                    ?><img src="/images/design/rating-.png" alt="" title="" /> <?
                }
                ?></span> <?}
            ?><span class="asked-question"><?=$question?></span></div><?
            if($flag){?>
                <div class="answer-content" id="answerBlock<?=$i?>">
                    <?if(ASKED_SHOW_ANSWER_TITLE){?><div class="asked-author"><?=$multi['TXT_ADMINISTRATION']?></div><?}?>
                    <div class="answer-content-text"><?=$fullAnswer?></div>
                </div>
                <?if(ASKED_HIDE_ANSWER){?> <script type="text/javascript">$('#answerBlock<?=$i?>').hide();</script><?}?>
            <?}?>
        </div><?
    }

if(ASKED_HIDE_ANSWER){?>
    <div class="asked-one-item"></div>
    <script type="text/javascript">
        function showAnswer(elem){
            $('#questionBlock'+elem).toggleClass('red-color');
            $('#oneItemFonBlock'+elem).toggleClass('asked-one-item-sel');
            $('#answerBlock'+elem).toggle('fast');
        }
    </script>
<?}
?><div class="links"><?
echo $pageLinl;
?></div>
</div><?