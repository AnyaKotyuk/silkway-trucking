<?php

/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 05.09.14
 * Time: 16:42
 */
class SystemSprSetting extends SystemSpr
{
    public $arrExistSettings = array();

    function __construct()
    {
        $this->arrExistSettings = array(
            'usesliderbox' =>
                array(
                    'type' => 'box',
                    'fields' => array('useimg'=>1,'usevisible'=>1,'usemove'=>1,'usehref' =>1,'usetarget='=>1),
                    'label' => 'TXT_SLIDER'
                ),
            'usecodpli' =>
                array(
                    'type' => 'text',
                    'name' => 'cod_pli',
                    'label' => 'FLD_COD_PLI',
                    'showInList' => false,
                    'lang' => 'oneLang',
                    'defVal' => NULL,
                    'addIndex' => true
                ),
            'usename' =>
                array(
                    'type' => 'textarea',
                    'name' => 'name',
                    'label' => '_FLD_NAME',
                    'showInList' => true,
                    'lang' => 'manyLang',
                    'defVal' => NULL,
                    'addIndex' => false
                ),
            'usetranslitaftername' =>
                array(
                    'type' => 'footnote',
                    'name' => 'translit',
                    'label' => 'FLD_PAGE_URL',
                    'useFields'=>'usetranslit',
                    'showInList' => true,
                    'lang' => 'manyLang',
                    'defVal' => NULL,
                    'addIndex' => false
                ),
            'usegruoup' =>
                array(
                    'type' => 'spr',
                    'name' => 'group',
                    'nameSpr' => 'mod_catalog_spr_group',
                    'filterName' => 'group_filtr',
                    'typeLink' => 'many',
                    'label' => '_FLD_GROUP',
                    'showInList' => true,
                    'lang' => 'oneLang',
                    'defVal' => NULL,
                    'addIndex' => true
                ),
            'usecategory' =>
                array(
                    'type' => 'spr',
                    'name' => 'category',
                    'nameSpr' => 'mod_catalog_spr_name',
                    'filterName' => 'category_filtr',
                    'typeLink' => 'one',
                    'label' => 'FLD_CATEGORY',
                    'showInList' => true,
                    'lang' => 'oneLang',
                    'defVal' => NULL,
                    'addIndex' => true
                ),
            'usedatestatus' =>
                array(
                    'type' => 'datestatus',
                    'name' => 'date',
                    'label' => '_FLD_STATUS',
                    'showInList' => true,
                    'lang' => 'oneLang',
                    'defVal' => strftime('%Y-%m-%d %H:%M', strtotime('now')),
                    'defVal_end' => strftime('%Y-%m-%d %H:%M', strtotime('now +30day')),
                    'addIndex' => true
                ),

            'useh1' =>
                array(
                    'type' => 'textarea',
                    'name' => 'h1',
                    'label' => '_FLD_H1',
                    'showInList' => false,
                    'lang' => 'manyLang',
                    'defVal' => NULL,
                    'addIndex' => false
                ),
            'usemetaafterh1' =>
                array(
                    'type' => 'footnote',
                    'name' => 'translit',
                    'label' => '_TXT_META_DATA',
                    'useFields'=>'usemeta',
                    'showInList' => true,
                    'lang' => 'manyLang',
                    'defVal' => NULL,
                    'addIndex' => false
                ),
            'useshort' =>
                array(
                    'type' => 'text',
                    'name' => 'short',
                    'label' => '_FLD_SHORT_NAME',
                    'showInList' => true,
                    'lang' => 'manyLang',
                    'defVal' => NULL,
                    'addIndex' => false
                ),
            'useshorthtml' =>
                array(
                    'type' => 'htmlarea',
                    'name' => 'shorthtml',
                    'label' => '_FLD_SHORT',
                    'showInList' => true,
                    'lang' => 'manyLang',
                    'defVal' => NULL,
                    'addIndex' => false
                ),
            'usedescr' =>
                array(
                    'type' => 'htmlarea',
                    'name' => 'descr',
                    'label' => 'FLD_DESCRIPTION',
                    'showInList' => true,
                    'lang' => 'manyLang',
                    'defVal' => NULL,
                    'addIndex' => false
                ),
            'usemanydescr' =>
                array(
                    'type' => 'values',
                    'name' => 'descrpMan',
                    'visible' => 'values',
                    'label' => 'FLD_DESCRIP2',
                    'showInList' => true,
                    'lang' => 'oneLang',
                    'defVal' => '',
                    'addIndex' => false,
                    'tableValues' => 'mod_tours_spr_description',
                    'moduleValues' => 124,
                    'filterName' => 'tours_filtr',
                    'parentName' => 'tours'
                ),
            'usehref' =>
                array(
                    'type' => 'text',
                    'name' => 'href',
                    'label' => '_FLD_LINK_NAME',
                    'showInList' => true,
                    'lang' => 'manyLang',
                    'addIndex' => false,
                    'defVal' => NULL,
                    'helpField' => 'FLD_REFERANSE'
                ),
            'usetarget' =>
                array(
                    'type' => 'set',
                    'name' => 'target',
                    'label' => '',
                    'showInList' => false,
                    'defVal' => '1',
                    'lang' => 'manyLang',
                    'addIndex' => false,
                    'nameLabel' => 'TXT_SEPARATE_WINDOW'
                ),
            'usevisible' =>
                array(
                    'type' => 'set',
                    'name' => 'visible',
                    'label' => 'FLD_VISIBLE',
                    'showInList' => true,
                    'defVal' => '1',
                    'lang' => 'oneLang',
                    'addIndex' => true
                ),
            'useimg' =>
                array(
                    'type' => 'icon',
                    'name' => 'img',
                    'label' => '_FLD_IMAGE',
                    'showInList' => true,
                    'lang' => 'manyLang',
                    'defVal' => NULL,
                    'addIndex' => true,
                    'helpField' => 'image'
                ),
            'useimg2' =>
                array(
                    'type' => 'icon',
                    'name' => 'img2',
                    'label' => '_FLD_IMAGE',
                    'showInList' => true,
                    'lang' => 'oneLang',
                    'defVal' => NULL,
                    'addIndex' => true,
                    'helpField' => 'image2'
                ),
            'userating' =>
                array(
                    'type' => 'values',
                    'name' => 'rating',
                    'visible' => 'rating',
                    'label' => 'FLD_RATING',
                    'showInList' => true,
                    'lang' => 'oneLang',
                    'defVal' => 0,
                    'addIndex' => false,
                    'tableValues' => 'mod_sight_spr_rating',
                    'moduleValues' => 119,
                    'filterName' => 'sight_filtr',
                    'parentName' => 'sight'
                ),
            'usedate' =>
                array(
                    'type' => 'date',
                    'name' => 'date',
                    'label' => 'FLD_DT',
                    'showInList' => true,
                    'lang' => 'oneLang',
                    'defVal' => strftime('%Y-%m-%d %H:%M', strtotime('now')),
                    'addIndex' => false,
                ),
            'usedescsortbymove' =>
                array(
                    'type' => 'sort',
                    'name' => 'move',
                    'typeLink' => 'desc',
                    'label' => 'TXT_REVERSE_SORTING_BY_MOVE',
                ),
            'usecatandval' =>
                array(
                    'type' => 'spr',
                    'name' => 'catalogandval',
                    'nameSpr' => 'mod_catalog_spr_name',
                    'nameMany' => 'id_cat',
                    'nameSprMany' => 'mod_catalog_param_val',
                    'filterName' => 'paramval_filtr',
                    'id_bond' => 'id_cat',
                    'typeLink' => 'onemany',
                    'label' => 'FLD_COMPARE_PARAMS',
                    'showInList' => false,
                    'lang' => 'oneLang',
                    'defVal' => NULL,
                    'addIndex' => true
                ),
            'usetabledescr' =>
                array(
                    'type' => 'htmlarea',
                    'name' => 'descr',
                    'label' => $this->multi['FLD_DESCRIPTION'],
                    'showInList' => true,
                    'lang' => 'manyLang',
                    'defVal' => '<ul class="vip-services">
                                <li class="icon-vip-1" style="width: 95%;">&nbsp;</li>
                                <li class="icon-vip-2" style="width: 95%;">&nbsp;</li>
                                <li class="icon-vip-3" style="width: 95%;">&nbsp;</li>
                                <li class="icon-vip-4" style="width: 95%;">&nbsp;</li>
                                <li class="icon-vip-5" style="width: 95%;">&nbsp;</li>
                                <li class="icon-vip-6" style="width: 95%;">&nbsp;</li>
                                <li class="icon-vip-7" style="width: 95%;">&nbsp;</li>
                                </ul>',
                    'addIndex' => false
                ),
            'useimg2' =>
                array(
                    'type' => 'icon',
                    'name' => 'img2',
                    'label' => $this->multi['_FLD_IMAGE'],
                    'showInList' => true,
                    'lang' => 'oneLang',
                    'defVal' => NULL,
                    'addIndex' => true,
                    'helpField' => 'image2'
                ),
            'usecountofpeopleVipMany' =>
                array(
                    'type' => 'spr',
                    'name' => 'countofpeopl',
                    'nameSpr' => 'mod_vip_people_spr',
                    'filterName' => 'peopl_filtr',
                    'typeLink' => 'many',
                    'label' => $this->multi['TXT_NUMBER_PEOPLE'],
                    'showInList' => false,
                    'lang' => 'oneLang',
                    'defVal' => NULL,
                    'addIndex' => true
                ),
            'usemetaafterh1' =>
                array(
                    'type' => 'footnote',
                    'name' => 'translit',
                    'label' => $this->multi['_TXT_META_DATA'],
                    'useFields'=>'usemeta',
                    'showInList' => true,
                    'lang' => 'manyLang',
                    'defVal' => NULL,
                    'addIndex' => false
                ),
            'usetranslitaftername' =>
                array(
                    'type' => 'footnote',
                    'name' => 'translit',
                    'label' => $this->multi['FLD_PAGE_URL'],
                    'useFields'=>'usetranslit',
                    'showInList' => true,
                    'lang' => 'manyLang',
                    'defVal' => NULL,
                    'addIndex' => false
                ),
        );
        $this->convertLabel();
    }
    function convertLabel(){
        if(isset($this->arrExistSettings) && !empty($this->arrExistSettings)){
            foreach($this->arrExistSettings as $key=>$rowTxt){
//                echo '<br>$key='.$key;
                if(isset($rowTxt['label']) && isset($this->multi[$rowTxt['label']])){
                    $this->arrExistSettings[$key]['label'] = $this->multi[$rowTxt['label']];
                }
                if(isset($rowTxt['helpField']) && isset($this->multi[$rowTxt['helpField']])){
                    $this->arrExistSettings[$key]['helpField'] = $this->multi[$rowTxt['helpField']];
                }
                if(isset($rowTxt['nameLabel']) && isset($this->multi[$rowTxt['nameLabel']])){
                    $this->arrExistSettings[$key]['nameLabel'] = $this->multi[$rowTxt['nameLabel']];
                }
            }
        }
    }
}