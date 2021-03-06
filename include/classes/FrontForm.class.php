<?php
/**
 * FrontForm.class.php
 * Class definition for describe HTML on front-end
 * @package Package of SEOCMS
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 2.0, 07.01.2013
 * @copyright (c) 2010+ by SEOTM
 */

/**
 * Class FrontForm
 * Class definition for describe HTML on front-end
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 2.0, 07.01.2013
 * @property name - name of form
 * @property multi - array with multilanguages texts
 */
class FrontForm extends Form {

    public $name = NULL;
    public $multi = NULL;

    /**
     * Class Constructor
     * Set the variabels
     * @return true/false
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 2.0, 07.01.2013
     */
    function __construct($nameform = 'f1'){
        $this->name = $nameform;
        $this->multi = check_init_txt('TblFrontMulti', TblFrontMulti);
    }

    /**
     * Class method WriteContentHeader
     * Write content header
     * @return true/false or arrays:
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 07.01.2013
     */
    function WriteContentHeader($h1 = null, $title = null, $path = null){
        ?>
        <div class="mceContentBody">
        <?
        if($h1){
            ?><h1><?= $h1; ?></h1><?
        }
        if($title){
            ?><div class="title"><?= $title; ?></div><?
        }
        if($path){
            ?><div class="path"><?= $path; ?></div><?
        }
    }

    /**
     * Class method WriteContentFooter
     * Write content footer
     * @return true/false or arrays:
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 07.01.2013
     */
    function WriteContentFooter(){
        ?>
        </div>
    <?
    }

    /**
     * Class method WriteFrontHeader
     * Write Form Header
     * @return true/false or arrays:
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 07.01.2013
     */
    function WriteFrontHeader($name = '', $script = '', $task = '', $params = ''){
        if (empty($name))
            $name = $this->name;
        ?>
        <form id="<?= $name; ?>" name="<?= $name; ?>" action="<?= $script; ?>" method="post" enctype="multipart/form-data" <?= $params; ?>>
        <input type="hidden" name="task" value="<?= $task; ?>"/>
    <?
    }

    /**
     * Class method WriteFrontFooter
     * Write Form Footer
     * @return true/false or arrays:
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 07.01.2013
     */
    function WriteFrontFooter(){
        ?>
        </form>
    <?
    }

    // ================================================================================================
    // Function : Radio()
    // Date : 15.04.2005
    // Parms :       $arr, $value, $property, $javascript
    // Description : Write Radio
    // Programmer : Andriy Lykhodid
    // ================================================================================================

    function Radio($name = NULL, $value = NULL, $valuech = NULL, $txt = NULL, $params = NULL) {
        ?><input type="radio" class="radio" name="<?= $name; ?>" value="<?= $value; ?>" <? if ($valuech == $value) echo 'checked'; ?> <? if (!empty($params)) echo $params; ?> ><?= $txt; ?><?
    }

    // ================================================================================================
    // Function : TextBox()
    // Date : 26.01.2005
    // Description : Write Text Box
    // Programmer : Andriy Lykhodid
    // ================================================================================================
    function TextBox($name = '', $value = '', $params = '',$id = '',$name_placeholder = ''){
        ?><input type="text" name="<?=$name?>"<?
        if($name_placeholder){
            ?> placeholder="<?=htmlspecialchars($name_placeholder)?>"<?
        }?> value="<?=htmlspecialchars($value)?>"<?
        if(!empty($id)){
            ?> id="<?=$id?>"<?
        }
        if (!empty($params))
            echo $params;
        ?>/><?
    }

    // ================================================================================================
    // Function : CheckBox()
    // Date : 26.01.2005
    // Description : Write CheckBox
    // Programmer : Andriy Lykhodid
    // ================================================================================================
    function CheckBox($name = 'id_del[]', $value = '', $sel = NULL, $id='', $params = '', $label=NULL) {
        ?>
        <INPUT TYPE="checkbox" name="<?= $name; ?>" value="<?= $value; ?>" <? if ($sel) echo ' checked'; ?> <? if (!empty($params)) echo $params; ?> />
    <?
    }

    // ================================================================================================
    // Function : TextArea()
    // Date : 26.01.2005
    // Description : Write Text Area
    // Programmer : Andriy Lykhodid
    // ================================================================================================
    function TextArea($name = '', $value = '', $rows = 4, $cols = 70, $params = NULL,$id = '',$name_placeholder = '') {
        ?>
        <textarea name=<?= $name; ?> rows="<?= $rows; ?>" cols="<?= $cols; ?>" <?
        if($name_placeholder){
            ?> placeholder="<?=htmlspecialchars($name_placeholder)?>"<?
        }
        if(!empty($id)){
            ?> id="<?=$id?>"<?
        }
        if (!empty($params)) echo $params;
        ?> class="textarea"><?= htmlspecialchars($value); ?></textarea>
    <?
    }

    // ================================================================================================
    // Function : Password()
    // Date : 29.01.2005
    // Description : Write input type=hidden
    // Programmer : Andriy Lykhodid
    // ================================================================================================
    function Password($name = '', $value = '', $size = '', $param = NULL) {
        if (!$size) {
            $size = 10;
        }
        ?>
        <input type="password" <?= $param ?> class="CatinputFromForm" name="<?= $name; ?>" value="<?= $value; ?>" size="<?= $size; ?>"/>
    <?
    }

    // ================================================================================================
    // Function : Hidden()
    // Date : 27.01.2005
    // Description : Write input type=hidden
    // Programmer : Andriy Lykhodid
    // Change Request Nbr:
    // ================================================================================================
    function Hidden($name = '', $value = '', $params = NULL) {
        ?>
        <input type="hidden" name="<?= $name; ?>" value="<?= htmlspecialchars($value); ?>"/>
    <?
    }

    // ================================================================================================
    // Function : Button()
    // Date : 27.01.2005
    // Description : Write input type=hidden
    // Programmer : Andriy Lykhodid
    // ================================================================================================
    function Button($name = '', $value = '', $size=50, $params=NULL) {
        ?>
        <input type="submit" name="<?= $name ?>" value="<?= $value ?>" class="button" <? if ($params) echo $params; ?>/>
    <?
    }

    // ================================================================================================
    // Function : Select()
    // Date : 26.01.2005
    // Description : Write Select Box
    // Programmer : Andriy Lykhodid
    // ================================================================================================
    function Select($arr, $name = '', $value = NULL, $width = NULL, $params=NULL, $disabled_value = NULL) {
        ?>
        <select <?= $params; ?> name="<?= $name; ?>" width="<?= $width ?>">
            <?
            while ($el = each($arr)) {
                if($disabled_value!=NULL AND $el['key']==$disabled_value){
                    $disabled = 'disabled';
                }else{
                    $disabled = '';
                }
                if ($el['key'] == $value)
                    echo '<option value="' . $el['key'] . '" selected '.$disabled.'>' . $el['value'] . '</option>';
                else
                    echo '<option value="' . $el['key'] . '" '.$disabled.'>' . $el['value'] . '</option>';
            }
            ?>
        </select>
    <?
    }

    // ================================================================================================
    // Function : Link()
    // Date : 25.04.2005
    // Description : Write Link
    // Programmer : Andriy Lykhodid
    // ================================================================================================
    function Link($script = '', $name = 'link', $hint = NULL) {
        ?>
        <a href="<?= $script; ?>" <? if ($hint) echo "onmouseover=\"return overlib('$hint',WRAP);\" onmouseout=\"nd();\""; ?> class="arch_polls"><?= $name ?></a>
    <?
    }

    /**
     * FrontForm::WriteLinkPagesStatic()
     * @author Yaroslav
     * @param mixed $scriptact
     * @param mixed $rows
     * @param mixed $display
     * @param mixed $start
     * @param mixed $sort
     * @param mixed $page
     * @param mixed $param_url
     * @return
     */
    function WriteLinkPagesStatic($scriptact, $rows, $display, $start, $sort, $page, $param_url = null,$showAjax = false) {
        $sh = 1;
        $na = 2;
        $flag1 = 1;
        $flag2 = 1;
        $p0 = 0;
        //echo "<br> page = ".$page;
        //echo '<br/>rows ='.$rows;
        //echo '<br/>$display ='.$display;
        $end = round($rows / $display, 2);
        if ($end <= 1)
            return;
        $goToPage = '';
        ob_start();
        ?>
        <table border="0" cellpadding="2" cellspacing="0" align="center" class="pages-table">
            <tr>
                <td align="left">
                    <?
                    $curr = round($start / $display, 0) + 1;
                    if (!$start || $start == '') {
                        ?><?
                    } else {
                        if ($page - 1 == 1){
                            $link_prevpage = $scriptact;
                            $linkAjaxParam = '';
                        }else{
                            $link_prevpage = $scriptact . 'page' . ($page - 1) . '/';
                            $linkAjaxParam = 'page' . ($page - 1) . '/';
                        }

                        if (!empty($param_url)){
                            $link_prevpage .= $param_url;
                            $linkAjaxParam .= $param_url;
                        }
                        ?><a href="<?= $link_prevpage; ?>" class="link-page prev"<?if($showAjax){
                            ?> onclick="return gelPropConetnt('<?=$scriptact?>','<?=$linkAjaxParam?>');"<?
                        }?>>< Prev</a>&nbsp;<?
                    }
                    $start_ = 0;
                    $end_ = 0;
                    $t_end = round($rows / $display, 0);
                    for ($i = 0; $i < ($rows / $display); $i++) {
                        $p = $i + 1;
                        if ($p0 == $p) {
                            continue;
                        }
                        $start_ = $end_;
                        if (( $end_ + $display) > $rows)
                            $end_ = $rows;
                        else
                            $end_ = $end_ + $display;

                        if ($p == 1){
                            $script = $scriptact;
                            $linkAjaxParam = '';
                        }else{
                            $script = $scriptact . "page" . $p . '/';
                            $linkAjaxParam = "page" . $p . '/';
                        }
                        if (!empty($param_url)){
                            $script = $script . $param_url;
                            $linkAjaxParam .= $param_url;
                        }
                        if ($p <= $na + $sh) {
                            if ($p == $curr) {
                                ?><b class="link-pages-sel"><?= $p; ?></b>&nbsp;<?
                                if ($end <= $sh)
                                    continue;
                            }
                            else {
                                ?><a href="<?= $script; ?>" title="<?= $goToPage; ?> <?= $p; ?>" class="link-page"<?if($showAjax){
                                    ?> onclick="return gelPropConetnt('<?=$scriptact?>','<?=$linkAjaxParam?>');"<?
                                }?>><?= $p; ?></a>&nbsp;<?
                                if ($end <= $sh)
                                    continue;
                            }
                        }
                        else {
                            if ($flag1 == 1 and $na + $sh <= $curr - $sh - $sh - 1 and $p > $sh + 2 and $p < $end - $sh) {
                                echo '<div class="pagesPoints"> ... </div>';
                                $flag1 = 0;
                            }
                        }
                        if ($p >= $curr - $sh and $p <= $curr + $sh and $p > $na + $sh and $p < $end - $sh and $p > ($sh + 1)) {
                            if ($p == $curr) {
                                ?><b class="link-pages-sel"><?= $p; ?></b><? } else {
                                ?><a href="<?= $script; ?>" title="<?= $goToPage; ?> <?= $p; ?>" class="link-page"<?if($showAjax){
                                    ?> onclick="return gelPropConetnt('<?=$scriptact?>','<?=$linkAjaxParam?>');"<?
                                }?>><?= $p; ?></a>&nbsp;<?
                            }
                            if ($p >= $curr + $sh and $flag2 == 1 and $end - $sh >= $curr + $sh + $sh + 2) {
                                echo '<div class="pagesPoints"> ... </div>';
                                $flag2 = 0;
                            }
                        } else {
                            if ($curr < $sh + $na and $flag2 == 1 and $p >= $curr + $sh and $p > $sh + 2) {
                                echo '<div class="pagesPoints"> ... </div>';
                                $flag2 = 0;
                            }
                        }

                        if ($p >= $end - $sh and $p > $sh + 2) {
                            if ($p == $curr) {
                                ?><b class="link-pages-sel"><?= $p; ?></b>&nbsp;<? } else {
                                ?><a href="<?= $script; ?>" title="<?= $goToPage; ?> <?= $p; ?>" class="link-page"<?if($showAjax){
                                    ?> onclick="return gelPropConetnt('<?=$scriptact?>','<?=$linkAjaxParam?>');"<?
                                }?>><?= $p; ?></a>&nbsp;<?
                            }
                        }
                        $p0 = $p;
                    }

                    if (!empty($param_url)){
                        $script = $scriptact . 'page' . ($page + 1) . '/' . $param_url;
                        $linkAjaxParam = 'page' . ($page + 1) . '/' . $param_url;
                    }else{
                        $script = $scriptact . 'page' . ($page + 1) . '/';
                        $linkAjaxParam = 'page' . ($page + 1) . '/';
                    }

                    if (($display + $start) >= $rows) {
                        ?><? } else {
                        ?><a href="<?= $script; ?>" class="link-page next"<?if($showAjax){
                            ?> onclick="return gelPropConetnt('<?=$scriptact?>','<?=$linkAjaxParam?>');"<?
                        }?>>Next ></a><? }

                    if (!empty($param_url)){
                        $script = $scriptact.'alltovar/'.$param_url;
                        $linkAjaxParam = 'alltovar/' . $param_url;
                    }else{
                        $script = $scriptact.'alltovar/';
                        $linkAjaxParam = 'alltovar/';
                    }

                    ?>
                </td>

            </tr>
        </table>
        <div class="clear"></div>
        <?
        return ob_get_clean();
    }


    function WriteLinkPagesStaticHomePage($scriptact, $rows, $display, $start, $sort, $page, $param_url = null, $showAjax = false) {
        //var_dump($scriptact);
        //var_dump($rows);
        //var_dump($display);
        //var_dump($page);

        $sh = 1;
        $na = 4;
        $flag1 = 1;
        $flag2 = 1;
        $p0 = 0;
//        echo "<br> page = ".$page;
//        echo '<br/>rows ='.$rows;
//        echo '<br/>$display ='.$display;
        $end = round($rows / $display, 2);
        if ($end <= 1)
            return;
        $goToPage = '';
        ob_start();
        ?>
        <table border="0" cellpadding="2" cellspacing="0" align="center" class="pages-table">
            <tr>
                <td align="left">
                    <?
                    $curr = round($start / $display, 0) + 1;
                    if (!$start || $start == '') {
                        ?><?
                    } else {
                        if ($page - 1 == 1) {
                            $link_prevpage = $scriptact;
                            $linkAjaxParam = '';
                        } else {
                            $link_prevpage = $scriptact . 'page' . ($page - 1) . '/';
                            $linkAjaxParam = 'page' . ($page - 1) . '/';
                        }

                        if (!empty($param_url)) {
                            $link_prevpage .= $param_url;
                            $linkAjaxParam .= $param_url;
                        }
                        ?><a class="prev-last-news" href="<?= $link_prevpage; ?>" class="link-page"<?if ($showAjax) {
                        ?>onclick="return gelPropConetntByNews('<?= $scriptact ?>','<?= $linkAjaxParam ?>');"<?
                    }?>></a>&nbsp;<?
                    }
                    $start_ = 0;
                    $end_ = 0;
                    $t_end = round($rows / $display, 0);
                    for ($i = 0; $i < ($rows / $display); $i++) {
                        $p = $i + 1;
                        if ($p0 == $p) {
                            continue;
                        }
                        $start_ = $end_;
                        if (( $end_ + $display) > $rows)
                            $end_ = $rows;
                        else
                            $end_ = $end_ + $display;

                        if ($p == 1){
                            $script = $scriptact;
                            $linkAjaxParam = '';
                        }else{
                            $script = $scriptact . "page" . $p . '/';
                            $linkAjaxParam = "page" . $p . '/';
                        }
                        if (!empty($param_url)){
                            $script = $script . $param_url;
                            $linkAjaxParam .= $param_url;
                        }
                        if ($p <= $na + $sh) {
                            if ($p == $curr) {
                                ?><b class="link-pages-sel"><?= $p; ?>&nbsp;/</b>&nbsp;<?
                                if ($end <= $sh)
                                    continue;
                            }
                            else {
                                ?><a href="<?= $script; ?>" title="<?= $goToPage; ?> <?= $p; ?>" class="link-page"<?if($showAjax){
                                    ?> onclick="return gelPropConetntByNews('<?=$scriptact?>','<?=$linkAjaxParam?>');"<?
                                }?>><?= $p; ?>&nbsp;/</a>&nbsp;<?
                                if ($end <= $sh)
                                    continue;
                            }
                        }
                        else {
                            if ($flag1 == 1 and $na + $sh <= $curr - $sh - $sh - 1 and $p > $sh + 2 and $p < $end - $sh) {
                                echo '<div class="pagesPoints"> ... </div>';
                                $flag1 = 0;
                            }
                        }
                        if ($p >= $curr - $sh and $p <= $curr + $sh and $p > $na + $sh and $p < $end - $sh and $p > ($sh + 1)) {
                            if ($p == $curr) {
                                ?><b class="link-pages-sel"><?= $p; ?></b><? } else {
                                ?><a href="<?= $script; ?>" title="<?= $goToPage; ?> <?= $p; ?>" class="link-page"<?if($showAjax){
                                    ?> onclick="return gelPropConetntByNews('<?=$scriptact?>','<?=$linkAjaxParam?>');"<?
                                }?>><?= $p; ?>&nbsp;/</a>&nbsp;<?
                            }
                            if ($p >= $curr + $sh and $flag2 == 1 and $end - $sh >= $curr + $sh + $sh + 2) {
                                echo '<div class="pagesPoints"> ... </div>';
                                $flag2 = 0;
                            }
                        } else {
                            if ($curr < $sh + $na and $flag2 == 1 and $p >= $curr + $sh and $p > $sh + 2) {
//                                echo '<div class="pagesPoints"> ... </div>';
                                echo '<div class="pagesPoints"></div>';
                                $flag2 = 0;
                            }
                        }

                        if ($p >= $end - $sh and $p > $sh + 2) {
                            if ($p == $curr) {
                                ?><b class="link-pages-sel"><?= $p; ?>&nbsp;/</b>&nbsp;<? } else {
                                ?><a href="<?= $script; ?>" title="<?= $goToPage; ?> <?= $p; ?>" class="link-page"<?if($showAjax){
                                    ?> onclick="return gelPropConetntByNews('<?=$scriptact?>','<?=$linkAjaxParam?>');"<?
                                }?>><?= $p; ?>&nbsp;/</a>&nbsp;<?
                            }
                        }
                        $p0 = $p;
                    }

                    if (!empty($param_url)){
                        $script = $scriptact . 'page' . ($page + 1) . '/' . $param_url;
                        $linkAjaxParam = 'page' . ($page + 1) . '/' . $param_url;
                    }else{
                        $script = $scriptact . 'page' . ($page + 1) . '/';
                        $linkAjaxParam = 'page' . ($page + 1) . '/';
                    }

                    if (($display + $start) >= $rows) {
                        ?><? } else {
                        ?><a class="next-last-news" href="<?= $script; ?>" class="link-page"<?if($showAjax){
                            ?> onclick="return gelPropConetntByNews('<?=$scriptact?>','<?=$linkAjaxParam?>');"<?
                        }?>></a><? }

                    if (!empty($param_url)){
                        $script = $scriptact.'alltovar/'.$param_url;
                        $linkAjaxParam = 'alltovar/' . $param_url;
                    }else{
                        $script = $scriptact.'alltovar/';
                        $linkAjaxParam = 'alltovar/';
                    }

                    ?>
                </td>

            </tr>
        </table>
        <div class="clear"></div>
        <?
        return ob_get_clean();
    }

    function WriteLinkPagesStaticTopHomePage($scriptact, $rows, $display, $start, $sort, $page, $param_url = null, $showAjax = false) {

        //var_dump($scriptact);
        //var_dump($rows);
        //var_dump($display);
        //var_dump($page);

        $sh = 1;
        $na = 10;
        $flag1 = 1;
        $flag2 = 1;
        $p0 = 0;
//        echo "<br> page = ".$page;
//        echo '<br/>rows ='.$rows;
//        echo '<br/>$display ='.$display;
        $end = round($rows / $display, 2);
        if ($end <= 1)
            return;
        $goToPage = '';
        ob_start();
        ?>
        <table border="0" cellpadding="2" cellspacing="0" align="center" class="pages-table">
            <tr>
                <td align="left">
                    <?
                    $curr = round($start / $display, 0) + 1;
                    if (!$start || $start == '') {
                        ?><?
                    } else {
                        if ($page - 1 == 1) {
                            $link_prevpage = $scriptact;
                            $linkAjaxParam = '';
                        } else {
                            $link_prevpage = $scriptact . 'page' . ($page - 1) . '/';
                            $linkAjaxParam = 'page' . ($page - 1) . '/';
                        }

                        if (!empty($param_url)) {
                            $link_prevpage .= $param_url;
                            $linkAjaxParam .= $param_url;
                        }
                        ?><a class="prev-last-news" href="<?= $link_prevpage; ?>" class="link-page"<?if ($showAjax) {
                        ?>onclick="return gelPropConetntByTopNews('<?= $scriptact ?>','<?= $linkAjaxParam ?>');"<?
                    }?>></a>&nbsp;<?
                    }
                    $start_ = 0;
                    $end_ = 0;
                    $t_end = round($rows / $display, 0);
                    for ($i = 0; $i < ($rows / $display); $i++) {
                        $p = $i + 1;
                        if ($p0 == $p) {
                            continue;
                        }
                        $start_ = $end_;
                        if (( $end_ + $display) > $rows)
                            $end_ = $rows;
                        else
                            $end_ = $end_ + $display;

                        if ($p == 1){
                            $script = $scriptact;
                            $linkAjaxParam = '';
                        }else{
                            $script = $scriptact . "page" . $p . '/';
                            $linkAjaxParam = "page" . $p . '/';
                        }
                        if (!empty($param_url)){
                            $script = $script . $param_url;
                            $linkAjaxParam .= $param_url;
                        }
                        if ($p <= $na + $sh) {
                            if ($p == $curr) {
                                ?><b class="link-pages-sel"><?= $p; ?>&nbsp;/</b>&nbsp;<?
                                if ($end <= $sh)
                                    continue;
                            }
                            else {
                                ?><a href="<?= $script; ?>" title="<?= $goToPage; ?> <?= $p; ?>" class="link-page"<?if($showAjax){
                                    ?> onclick="return gelPropConetntByTopNews('<?=$scriptact?>','<?=$linkAjaxParam?>');"<?
                                }?>><?= $p; ?>&nbsp;/</a>&nbsp;<?
                                if ($end <= $sh)
                                    continue;
                            }
                        }
                        else {
                            if ($flag1 == 1 and $na + $sh <= $curr - $sh - $sh - 1 and $p > $sh + 2 and $p < $end - $sh) {
                                echo '<div class="pagesPoints"> ... </div>';
                                $flag1 = 0;
                            }
                        }
                        if ($p >= $curr - $sh and $p <= $curr + $sh and $p > $na + $sh and $p < $end - $sh and $p > ($sh + 1)) {
                            if ($p == $curr) {
                                ?><b class="link-pages-sel"><?= $p; ?></b><? } else {
                                ?><a href="<?= $script; ?>" title="<?= $goToPage; ?> <?= $p; ?>" class="link-page"<?if($showAjax){
                                    ?> onclick="return gelPropConetntByTopNews('<?=$scriptact?>','<?=$linkAjaxParam?>');"<?
                                }?>><?= $p; ?>&nbsp;/</a>&nbsp;<?
                            }
                            if ($p >= $curr + $sh and $flag2 == 1 and $end - $sh >= $curr + $sh + $sh + 2) {
                                echo '<div class="pagesPoints"> ... </div>';
                                $flag2 = 0;
                            }
                        } else {
                            if ($curr < $sh + $na and $flag2 == 1 and $p >= $curr + $sh and $p > $sh + 2) {
//                                echo '<div class="pagesPoints"> ... </div>';
                                echo '<div class="pagesPoints"></div>';
                                $flag2 = 0;
                            }
                        }

                        if ($p >= $end - $sh and $p > $sh + 2) {
                            if ($p == $curr) {
                                ?><b class="link-pages-sel"><?= $p; ?>&nbsp;/</b>&nbsp;<? } else {
                                ?><a href="<?= $script; ?>" title="<?= $goToPage; ?> <?= $p; ?>" class="link-page"<?if($showAjax){
                                    ?> onclick="return gelPropConetntByTopNews('<?=$scriptact?>','<?=$linkAjaxParam?>');"<?
                                }?>><?= $p; ?>&nbsp;/</a>&nbsp;<?
                            }
                        }
                        $p0 = $p;
                    }

                    if (!empty($param_url)){
                        $script = $scriptact . 'page' . ($page + 1) . '/' . $param_url;
                        $linkAjaxParam = 'page' . ($page + 1) . '/' . $param_url;
                    }else{
                        $script = $scriptact . 'page' . ($page + 1) . '/';
                        $linkAjaxParam = 'page' . ($page + 1) . '/';
                    }
                    //echo '<br>$display='.$display.' $start='.$start.' $rows='.$rows;
                    if (($display + $start) >= $rows) {
                        ?><?
                    }else {
                        ?><a class="next-last-news" href="<?= $script; ?>" class="link-page"<?if($showAjax){
                            ?> onclick="return gelPropConetntByTopNews('<?=$scriptact?>','<?=$linkAjaxParam?>');"<?
                        }?>></a><? }

                    if (!empty($param_url)){
                        $script = $scriptact.'alltovar/'.$param_url;
                        $linkAjaxParam = 'alltovar/' . $param_url;
                    }else{
                        $script = $scriptact.'alltovar/';
                        $linkAjaxParam = 'alltovar/';
                    }

                    ?>
                </td>

            </tr>
        </table>
        <div class="clear"></div>
        <?
        return ob_get_clean();
    }

    // ================================================================================================
    // Function : ShowTextMessages()
    // Date : 06.06.2007
    // Returns :      true,false / Void
    // Description :  Show text messages
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function ShowTextMessages($txt = NULL) {
        if ($txt) {
            ?>
            <div align="center">
                <div class="msg-box">
                    <table border="0" cellspacing="0" cellpadding="0" align="center">
                        <tr><td class="msg-text"><?= $txt; ?></td></tr>
                    </table>
                </div>
            </div>
        <?
        }
    }

    // ================================================================================================
    // Function : ShowTextWarnings()
    // Date : 16.05.2008
    // Returns :      true,false / Void
    // Description :  Show text warnings
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function ShowTextWarnings($txt = NULL) {
        if (!empty($txt)) {
            ?>
            <div align="center">
                <div class="wrn-box">
                    <table border="0" cellspacing="0" cellpadding="0" align="center">
                        <tr><td class="wrn-text"><?= $txt; ?></td></tr>
                    </table>
                </div>
            </div>
        <?
        }
    }

    // ================================================================================================
    // Function : ShowErr()
    // Date : 10.01.2006
    // Returns :      true,false / Void
    // Description :  Show errors
    // Programmer :  Igor Trokhymchuk
    // ================================================================================================
    function showErr($txt = NULL) {
        if ($txt) {
            ?>
            <div align="center">
                <div class="err-box">
                    <table border="0" cellspacing="0" cellpadding="0" align="center">
                        <tr><td class="err-title"><?= $this->multi['MSG_PAY_ATTENTION']; ?></td></tr>
                        <tr><td class="err-text"><?= $txt; ?></td></tr>
                    </table>
                </div>
            </div>
        <?
        }

    }
}
//end of claas FrontForm
?>