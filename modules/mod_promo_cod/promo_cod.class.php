<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 03.01.14
 * Time: 15:17
 */
class PromoCode
{
    public $task ='show';
    public $Err = '';

    public $maxGenerateItem = 50;
    public $generateItem = 0;
    public $str_id = '';

    public $start = 0;
    public $display = 50;

    public $search_status = '';
    public $search_sum_from = '';
    public $search_sum_to = '';
    public $search_currency = '';
    public $search_cod = '';

    /**
     * @param null $user_id
     * @param null $module
     * @author Bogdan Iglinsky
     */
    function __construct($user_id=NULL, $module=NULL) {
        //Check if Constants are overrulled
        ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
        ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );

        if(defined("_LANG_ID")) $this->lang_id = _LANG_ID;

        if(empty($this->Rights)) $this->Rights = check_init('RightsSitemap', 'Rights', "'$this->user_id', '$this->module'");
        if(empty($this->Form)) $this->Form = check_init('FormSitemap', 'Form', "'frm_sitemap'");
        if(empty($this->multi)) $this->multi = check_init_txt('TblBackMulti',TblBackMulti);

        $this->Currencies = check_init('SystemCurrencies', 'SystemCurrencies');
        $this->Currencies->defCurrencyData = $this->Currencies->GetDefaultData();
        $this->Currencies->GetShortNamesInArray('back');

        $this->arr_status['a'] = $this->multi['STATUS_ACTIVE'];
        $this->arr_status['u'] = $this->multi['STATUS_USED'];
        $this->arr_status['d'] = $this->multi['STATUS_EXPIRED'];
//        var_dump($this->Currencies->defCurrencyData );
    } // End of Sitemap Constructor

    /**
     *
     * @author Bogdan Iglinsky
     */
    function showFilter(){
        /* Write Table Part */
        AdminHTML::PanelSimpleH();
        ?>
        <tr valign="top">
            <td style="width: 400px">
                <div><h3 style="padding:0px; margin:0px;"><?=$this->multi['TXT_SEARCH_PANEL'];?></h3></div>
                <table border="0" cellpadding="2" cellspacing="1">
                    <tr class="tr2">
                        <td align="right" width="100"><?=$this->multi['FLD_STATUS'];?>:</td>
                        <td align="left">
                            <div align="left"><?
                                $arr_status[''] =$this->multi['TXT_NEWS_ALL_STATUSES'];
                                $arr_status = array_merge($arr_status,$this->arr_status);
                                $this->Form->SelectAct( $arr_status, 'search_status', $this->search_status, "" );
                                ?></div>
                        </td>
                    </tr>
                    <tr class="tr2">
                        <td align="right" nowrap="nowrap">
                            <?=$this->multi['FLD_SUMA'];?>:
                        </td>
                        <td>
                            <?=$this->multi['FLD_FROM']?>
                            <?$this->Form->TextBox('search_sum_from', $this->search_sum_from, 10, 'style="width:70px;"');?>
                            <?=$this->multi['FLD_QUANTITY_TO']?>
                            <?$this->Form->TextBox('search_sum_to', $this->search_sum_to, 40, 'style="width:70px;"');?>
                            <?
                            if( empty($this->currency) AND $this->currency!='0' ) $this->currency = $this->Currencies->defCurrencyData['id'];

                            $this->Form->Select($this->Currencies->listShortNames, 'search_currency', $this->currency);?>
                        </td>
                    </tr>
                    <tr class="tr2">
                        <td align="right" nowrap="nowrap">
                            <?=$this->multi['_FLD_CODE'];?>:
                        </td>
                        <td>
                            <?$this->Form->TextBox('search_cod', $this->search_cod, 40, 'style="width:90px;"');?>
                        </td>
                    </tr>
                    <tr class="tr2">
                        <td></td>
                        <td align="left">
                            <input type="submit" value="<?=$this->multi['TXT_BUTTON_SEARCH'];?>"/>

                        </td>
                    <tr>
                </table>
            </td>
            <?
            if(!empty($this->str_id)){?>
                <td><b><?=$this->multi['TXT_SELECTED_RECORDS']?>:</b><br/> <?=$this->str_id?><br/><br/>
                    <a class="r-button" href="<?=$_SERVER['PHP_SELF']."?module=$this->module";?>">
                    <span>
                        <span>
                            <img src="images/icons/delete.png" alt="Удалить" title="Удалить" align="center" name="delete">
                            <?=$this->multi['TXT_RESET_FILTER']?>
                        </span>
                    </span>
                    </a>
                </td>

            <?
            }?>
        </tr>
        <?
        AdminHTML::PanelSimpleF();

    }

    /**
     * @return bool
     * @author Bogdan Iglinsky
     */
    function show(){
        $this->checkStatus();
        $row_arr = $this->GetContent();
//        var_dump($row_arr);
        $rows = count( $row_arr );

        /* Write Form Header */
        $this->Form->WriteHeader( $this->script );

        /* Write Table Part */
        AdminHTML::TablePartH();

        echo '<TR><TD COLSPAN=6>';
        $this->showFilter();

        /* Write Links on Pages */
        echo '<TR><TD COLSPAN=9>';
        $this->Form->WriteLinkPages( $this->script, count($this->GetContent('nolimit')), $this->display, $this->start, $this->sort );


        echo '<TR><TD COLSPAN=6>';
        $this->Form->WriteTopPanel( $this->script, 2);
        ?><a class="r-button" href="<?=$this->script?>&task=form">
            <span><span><img src="images/icons/new.png" alt="Генерировать" title="Генерировать" align="center" name="new">Генерировать</span></span>
        </a>
        <a class="r-button" href="javascript:$('#task').val('export');$('#frm_sitemap').submit();"
           onclick="if($('input[type=\'checkbox\'].check0:checked').length){
               if( !window.confirm('Экспортировать позиции. Вы уверены?')) return false;
               }else{alert('<?=$this->multi['TXT_EXPORT_CATALOG_NO_POSITIONS']?>'); return false;}">
    <span><span>
     <img src="images/icons/save.png" alt="<?=$this->multi['TXT_EXPORT']?>" title="<?=$this->multi['TXT_EXPORT']?>"
          align="center" name="save"><?=$this->multi['TXT_EXPORT']?></span></span>
        </a><?

        //echo '<br>$this->asc_desc='.$this->asc_desc;
        $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&sort_old='.$this->sort.'&asc_desc='.$this->asc_desc;
        $script2 = $_SERVER['PHP_SELF']."?$script2";

        if($rows>$this->display) $ch = $this->display;
        else $ch = $rows;
        ?>
        <TR>
        <Th class="THead"><input value="0" id="cAll" onclick="if (this.value == '1') {unCheckAll(<?=$ch;?>); this.value = '0';} else {checkAll(<?=$ch;?>); this.value = '1';}" type="checkbox"></Th>
        <Th class="THead"><?=$this->Form->LinkSort( $this->sort, 'id', $script2, $this->asc_desc, $this->multi['FLD_ID']);?></Th>
        <Th class="THead"><?=$this->Form->LinkSort( $this->sort, 'cod', $script2, $this->asc_desc, $this->multi['_FLD_CODE']);?></Th>
        <Th class="THead"><?=$this->Form->LinkSort( $this->sort, 'sum', $script2, $this->asc_desc, $this->multi['FLD_SUMA']);?></Th>
        <Th class="THead"><?=$this->Form->LinkSort( $this->sort, 'status', $script2, $this->asc_desc, $this->multi['FLD_STATUS']);?></Th>
        <Th class="THead"><?=$this->Form->LinkSort( $this->sort, 'expires_date', $script2, $this->asc_desc, $this->multi['TXT_SKILL_INVITE_EXPIRES_DATE']);?></Th>
        <Th class="THead"><?=$this->multi['TXT_ISIDE_INFORMATION'];?></Th>
        <?

        $style1 = 'TR1';
        $style2 = 'TR2';
        for( $i = 0; $i < $rows; $i++ )
        {
            $row = $row_arr[$i];
            if ( (float)$i/2 == round( $i/2 ) ) $class_tr = $style1;
            else $class_tr = $style2;
            ?>
            <tr align="center" class="<?=$class_tr;?>">
                <td><?=$this->Form->CheckBox( "id_del[]", $row['id'], null, "check".$i );?></td>
                <td><?=$this->Form->Link( $this->script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->multi['_TXT_LISTEN_DATA'] );?></td>
                <td><?=stripslashes($row['cod']);?></td>
                <td><?=$this->Currencies->ShowPrice($this->Currencies->Converting($row['currency'],$this->Currencies->defCurrencyData['id'],$row['sum']))?></td>
                <td><?=$this->arr_status[$row['status']];?></td>
                <td><?=$row['expires_date'];?></td>
                <td><?
                    if(!empty($row['id_order'])):
                        $order_number = stripslashes($row['order_number']);
                        if(!empty($order_number)){
                            ?><div>
                            <b><?=$this->multi['FLD_ORDER_ID']?></b>:
                            <a href="/admin/index.php?module=106&task=edit&id_order=<?=$order_number?>"><?=$order_number?></a><?
                            if(!empty($row['sum_order'])):
                                echo ' ('.$this->Currencies->ShowPrice($this->Currencies->Converting($row['currency_order'],
                                    $this->Currencies->defCurrencyData['id'],$row['sum_order'])).')';
                            endif;
                            ?></div><?
                        }
                    endif;
                    if(!empty($row['id_user'])):
                        $name_user = stripslashes($row['name_user']);
                        if(empty($name_user)) $name_user = $this->mutli['_VALUE_NOT_SET'];
                        ?><div><b><?=$this->multi['FLD_USER_ID']?></b>: (<?=$row['id_user']?>) <?=$name_user?></div><?
                    endif;
                    if(!empty($row['IP'])):
                        ?><div><b>IP</b>: <?=$row['IP']?></div><?
                    endif;
                    if(!empty($row['activate_date']) && $row['activate_date']!='0000-00-00 00:00:00'):
                        ?><div><b><?=$this->multi['TXT_DATE_ACTIVATION']?></b>: <?=$row['activate_date']?></div><?
                    endif;
                ?></td>
            </tr>
        <?
        } //-- end for
        AdminHTML::TablePartF();
        $this->Form->WriteFooter();
        return true;
    }

    /**
     * @param string $limit
     * @return array|bool
     * @author Bogdan Iglinsky
     */
    function GetContent($limit='limit'){
//        echo '$this->str_id='.$this->str_id;
        if( !$this->sort ) $this->sort='id';
        $q = "SELECT `".TblModPromoCod."`.* ,
        `".TblModOrderComments."`.`id_order` as `order_number`,
        `".TblModOrderComments."`.`sum` as `sum_order`,
        `".TblModOrderComments."`.`currency` as `currency_order`,
        `".TblModUser."`.`name` as `name_user`
        FROM `".TblModPromoCod."`
        LEFT JOIN `".TblModOrderComments."`
        ON (`".TblModPromoCod."`.`id_order` = `".TblModOrderComments."`.`id`)
        LEFT JOIN `".TblModUser."`
        ON (`".TblModPromoCod."`.`id_user` = `".TblModUser."`.`sys_user_id`)
        WHERE 1 ";
        if(!empty($this->str_id)){
            $q .= " AND `".TblModPromoCod."`.`id` in (".$this->str_id.") ";
        }
        if(!empty($this->search_status)){
            $q .= " AND `".TblModPromoCod."`.`status` = '".$this->search_status."' ";
        }
        if(!empty($this->search_cod)){
            $q .= " AND `".TblModPromoCod."`.`cod` LIKE '%".$this->search_cod."%' ";
        }
        if(!empty($this->search_sum_from)){
            $q .= " AND `".TblModPromoCod."`.`sum` >= '".$this->search_sum_from."' ";
        }
        if(!empty($this->search_sum_to)){
            $q .= " AND `".TblModPromoCod."`.`sum` <= '".$this->search_sum_to."' ";
        }
        $q .= " ORDER BY `$this->sort` $this->asc_desc";
        if($limit=='limit') $q .= " LIMIT ".$this->start.", ".$this->display;
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
//        echo '<br />$q='.$q.' $this->Rights->result='.$this->Rights->result;
        if( !$res OR !$this->Rights->result) return false;
        $rows = $this->Rights->db_GetNumRows();
        $arr = array();
        for($i=0;$i<$rows;$i++){
            $arr[$i] = $this->Rights->db_FetchAssoc();
        }

        return $arr;
    }//end of function GetContent();

    /**
     * @return bool
     * @author Bogdan Iglinsky
     */
    function edit(){
        $mas=NULL;
        if( $this->id!=NULL){
            $q = "SELECT `".TblModPromoCod."`.* ,
                `".TblModOrderComments."`.`id_order` as `order_number`,
                `".TblModOrderComments."`.`sum` as `sum_order`,
                `".TblModOrderComments."`.`currency` as `currency_order`,
                `".TblModUser."`.`name` as `name_user`
                FROM `".TblModPromoCod."`
                LEFT JOIN `".TblModOrderComments."`
                ON (`".TblModPromoCod."`.`id_order` = `".TblModOrderComments."`.`id`)
                LEFT JOIN `".TblModUser."`
                ON (`".TblModPromoCod."`.`id_user` = `".TblModUser."`.`sys_user_id`)
                WHERE `".TblModPromoCod."`.`id`='$this->id' ";
//            echo '$q='.$q;
            $res = $this->Rights->Query( $q, $this->user_id, $this->module );
            if( !$res  OR !$this->Rights->result ) return false;
            $mas = $this->Rights->db_FetchAssoc();
        }
        else return false;

        $def_currency = $this->Currencies->defCurrencyData['id'];

        /* Write Form Header */
        $this->Form->WriteHeader( $this->script );

        $this->Form->Hidden( 'id', $this->id );
        $this->Form->Hidden( 'display', $this->display );
        $this->Form->Hidden( 'start', $this->start );
        $this->Form->Hidden( 'sort', $this->sort );

        if( $this->id!=NULL ) $txt = $this->multi['_TXT_LISTEN_DATA'];
        else $txt = $this->multi['_TXT_ADD_DATA'];

        AdminHTML::PanelSubH( $txt );
        AdminHTML::PanelSimpleH();
        ?>
        <table border="0" cellpadding="0" cellspacing="5" width="100%">
            <tr>
                <td valign="top">
                    <table border="0" cellpadding="5" cellspacing="1" width="600">
                        <tr CLASS="TR1">
                            <td align="left"><?=$this->multi['FLD_ID'];?>:</td>
                            <td align="left"><?=$mas['id'];?></td>
                        </tr>
                        <tr CLASS="TR2">
                            <td align="left"><?=$this->multi['_FLD_CODE'];?>:</td>
                            <td align="left"><?=$mas['cod'];?></td>
                        </tr>
                        <tr CLASS="TR1">
                            <td align="left"><?=$this->multi['TXT_COST_SKILL'];?>:</td>
                            <td align="left">
                                <?
                                if( $this->id!=NULL ) $this->Err!=NULL ? $currency=$this->currency : $currency=$mas['currency'];
                                else $currency=$this->currency;
                                if( empty($currency) AND $currency!='0' ) $currency = $def_currency;

                                if( $this->id!=NULL ) $this->Err!=NULL ? $sum=$this->sum : $sum=$mas['sum'];
                                else $sum=$this->sum;
                                $this->Form->TextBox( 'sum', stripslashes($sum), 10 );
                                $this->Form->Select($this->Currencies->listShortNames, 'currency', $currency);?>
                            </td>
                        </tr>
                        <tr CLASS="TR2">
                            <td align="left"><?=$this->multi['FLD_STATUS'];?>:</td>
                            <td align="left"><?=$this->arr_status[$mas['status']];?></td>
                        </tr>
                        <tr CLASS="TR1">
                            <td align="left"><?=$this->multi['TXT_SKILL_INVITE_EXPIRES_DATE'];?>:</td>
                            <td align="left"><?
                                $calendar = new DHTML_Calendar(false, 'en', 'calendar-win2k-2', false);
                                $calendar->load_files();
                                if( $this->id!=NULL ) {
                                    if($this->Err!=NULL) $expires_date = $this->expires_date;
                                    else $expires_date = $mas['expires_date'];
                                }
                                else $expires_date=$this->expires_date;
                                $a1 = array('firstDay'       => 1, // show Monday first
                                    'showsTime'      => true,
                                    'showOthers'     => true,
                                    'ifFormat'       => '%Y-%m-%d %H:%M',
                                    'timeFormat'     => '12');
                                $a2 = array('style'       => 'width: 15em; color: #840; background-color: #ff8; border: 1px solid #000; text-align: center',
                                    'name'        => 'expires_date',
                                    'value'       => $expires_date );
                                $calendar->make_input_field( $a1, $a2 );?></td>
                        </tr>
                        <tr CLASS="TR2"><td align="left"><?=$this->multi['TXT_ISIDE_INFORMATION'];?>:</td>
                            <td align="left"><?
                                if(!empty($mas['id_order'])):
                                    $order_number = stripslashes($mas['order_number']);
                                    if(!empty($order_number)){
                                        ?><div>
                                        <b><?=$this->multi['FLD_ORDER_ID']?></b>:
                                        <a href="/admin/index.php?module=106&task=edit&id_order=<?=$order_number?>"><?=$order_number?></a><?
                                        if(!empty($mas['sum_order'])):
                                            echo ' ('.$this->Currencies->ShowPrice($this->Currencies->Converting($mas['currency_order'],
                                                    $this->Currencies->defCurrencyData['id'],$mas['sum_order'])).')';
                                        endif;
                                        ?></div><?
                                    }
                                endif;
                                if(!empty($mas['id_user'])):
                                    $name_user = stripslashes($mas['name_user']);
                                    if(empty($name_user)) $name_user = $this->mutli['_VALUE_NOT_SET'];
                                    ?><div><b><?=$this->multi['FLD_USER_ID']?></b>: (<?=$mas['id_user']?>) <?=$name_user?></div><?
                                endif;
                                if(!empty($mas['IP'])):
                                    ?><div><b>IP</b>: <?=$mas['IP']?></div><?
                                endif;
                                if(!empty($mas['activate_date']) && $mas['activate_date']!='0000-00-00 00:00:00'):
                                    ?><div><b><?=$this->multi['TXT_DATE_ACTIVATION']?></b>: <?=$mas['activate_date']?></div><?
                                endif;
                                ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <hr style="color:#666666" size="1px"; />
        <?
        $this->Form->Hidden( "id_del[0]", $this->id );
        $this->Form->WriteSaveAndReturnPanel( $this->script );?>&nbsp;<?
        $this->Form->WriteSavePanel( $this->script );?>&nbsp;<?
        $this->Form->WriteTopPanel( $this->script, 2);
        ?>
        <a class="r-button" href="<?=$this->script?>" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage('restore','','images/icons/restore_f2.png',1);">
            <span><span><IMG src='images/icons/restore.png' width="23" height="23" alt="Go to:" align="middle" border="0" name="restore" />&nbsp;&nbsp;<?=$this->multi['BTN_BACK'];?></span></span></a>
        <?

        echo '<TR><TD COLSPAN=2 ALIGN=left>';
       // $this->Form->WriteSavePanel( $this->script );
       //$this->Form->WriteCancelPanel( $this->script );
        ?></table><?;
        AdminHTML::PanelSimpleF();
        AdminHTML::PanelSubF();

        $this->Form->WriteFooter();
        return true;
    }

    /**
     * @return bool
     * @author Bogdan Iglinsky
     */
    function save(){
        $q = "SELECT * FROM ".TblModPromoCod." WHERE `id`='".$this->id."'";
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
        //echo "<br / > q = ".$q." res = ".$res;
        if( !$res ) return false;
        $rows = $this->Rights->db_GetNumRows();
        //echo 'rows='.$rows;

        if( $rows>0 )   //--- update
        {
            $q = "UPDATE `".TblModPromoCod."` SET
              `sum` = '".$this->sum."',
              `currency` = '".$this->currency."',
              `expires_date` = '".$this->expires_date."'
               WHERE `id`='".$this->id."'";
            $res = $this->Rights->Query( $q, $this->user_id, $this->module );
//        echo "<br / > q = ".$q." res = ".$res;
            if( !$res ) return false;
        }
        else          //--- insert
        {
            $q = "INSERT INTO `".TblModNews."` SET
              `sum` = '".$this->sum."',
              `currency` = '".$this->currency."',
              `expires_date` = '".$this->expires_date."'
             ";

            $res = $this->Rights->Query( $q, $this->user_id, $this->module );
//        echo "<br / > q = ".$q." res = ".$res;
            if( !$res ) return false;

            $this->id = $this->Rights->db_GetInsertID();
        }
        $this->checkStatus($this->id);
        return true;
    }

    /**
     * @param null $id_only
     * @return bool
     * @author Bogdan Iglinsky
     */
    function checkStatus($id_only = NULL){
        $q = "SELECT *
        FROM ".TblModPromoCod."
        WHERE 1";
        if(!empty($id_only)) $q .= " AND `id`='".$id_only."'";
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
//        echo "<br / > q = ".$q." res = ".$res;
        if( !$res ) return false;
        $rows = $this->Rights->db_GetNumRows();
        if($rows==0) return false;
        $arr_cod = array();
        for($i=0;$i<$rows;$i++){
            $arr_cod[$i] = $this->Rights->db_FetchAssoc();
        }
        $now_date = strftime('%Y-%m-%d %H:%M', strtotime('now'));
        for($i=0;$i<$rows;$i++){
            $row = $arr_cod[$i];
            if($row['activate_date']!='0000-00-00 00:00:00'){
                $status = 'u';
            }elseif($row['expires_date']<$now_date){
                $status = 'd';
            }elseif($row['expires_date']>$now_date){
                $status = 'a';
            }
            if($status!=$row['status']){
                $q = "UPDATE `".TblModPromoCod."` SET
                `status` = '".$status."'
                WHERE `id`='".$row['id']."'";
                $res = $this->Rights->Query( $q, $this->user_id, $this->module );
//        echo "<br / > q = ".$q." res = ".$res;
                if( !$res ) return false;
            }
        }
        return true;
    }

    /**
     * @param $id_del
     * @return bool|int
     * @author Bogdan Iglinsky
     */
    function del( $id_del )
    {
        $kol = count( $id_del );
        $del = 0;
        for( $i=0; $i<$kol; $i++ ){
            $u = $id_del[$i];

            $q="SELECT * FROM `".TblModPromoCod."` WHERE `id`='".$u."'";
            $res = $this->Rights->Query( $q, $this->user_id, $this->module );
            if (!$res OR !$this->Rights->result) return false;

            $q="DELETE FROM `".TblModPromoCod."` WHERE `id`='".$u."'";
            $res = $this->Rights->Query( $q, $this->user_id, $this->module );
            if (!$res OR !$this->Rights->result) return false;
            $del=$del+1;
        }
        return $del;
    } //end of fuinction del()

    /**
     *
     * @author Bogdan Iglinsky
     */
    function form(){
        $this->scriptInit(true);
        /* Write Form Header */
        $this->Form->WriteHeader( $this->script );

        $this->Form->Hidden( 'id', $this->id );
        $this->Form->Hidden( 'display', $this->display );
        $this->Form->Hidden( 'start', $this->start );
        $this->Form->Hidden( 'sort', $this->sort );

        if( $this->id!=NULL ) $txt = $this->multi['_TXT_LISTEN_DATA'];
        else $txt = $this->multi['_TXT_ADD_DATA'];

        AdminHTML::PanelSubH( $txt );
        ?>
        <div>
            <fieldset title="<?=$this->multi['TXT_PATTERN_FORMATION_BONUSES']?>">
                <legend>
                    <span style='vetical-align:middle; font-size:15px;'>
                        <img src='images/icons/meta.png'
                             alt="<?=$this->multi['TXT_PATTERN_FORMATION_BONUSES']?>"
                             title="<?=$this->multi['TXT_PATTERN_FORMATION_BONUSES']?>" border='0' />
                        <?=$this->multi['TXT_PATTERN_FORMATION_BONUSES']?>
                    </span>
                </legend>
                <table border="0" cellpadding="5" cellspacing="5">
                    <tr>
                        <td><b><?=$this->multi['TXT_COST_SKILL']?></b></td>
                        <td><?
                            $this->Form->TextBox( 'sum', $this->sum, 16 );
                            if(!empty($this->currency)) $def_currency = $this->currency;
                            else $def_currency = $this->Currencies->defCurrencyData['id'];
                            $this->Form->Select($this->Currencies->listShortNames, 'currency', $def_currency);
                            ?></td>
                    </tr>
                    <tr>
                        <td><?=$this->multi['TXT_SKILL_INVITE_EXPIRES_DATE'];?>:</td>
                        <td><?
                            $calendar = new DHTML_Calendar(false, 'en', 'calendar-win2k-2', false);
                            $calendar->load_files();

                            if(!empty($this->expires_date)) $expires_date = $this->expires_date;
                            else $expires_date = strftime('%Y-%m-%d %H:%M', strtotime('now'));
                            $a1 = array('firstDay'       => 1, // show Monday first
                                'showsTime'      => true,
                                'showOthers'     => true,
                                'ifFormat'       => '%Y-%m-%d %H:%M',
                                'timeFormat'     => '12');
                            $a2 = array('style'       => 'width: 15em; color: #840; background-color: #ff8; border: 1px solid #000; text-align: center',
                                'name'        => 'expires_date',
                                'value'       =>  $expires_date);
                            $calendar->make_input_field( $a1, $a2 );?></td>
                    </tr>
                    <tr>
                        <td><b><?=$this->multi['FLD_QUANTITY']?></b></td>
                        <td><?
                            $this->Form->TextBox( 'cnt', $this->cnt, 27 );
                            ?></td>
                    </tr>
                </table>
            </fieldset>
        </div>
        <hr style="color:#666666" size="1px"; />
        <?
        $this->Form->Hidden( "id_del[0]", $this->id );
        ?><a class="r-button" href="javascript:$('#task').val('generate');$('#frm_sitemap').submit();">
            <span><span><img src="images/icons/new.png" alt="Генерировать" title="Генерировать" align="center" name="new">Генерировать</span></span>
        </a><?
        ?>
        <a class="r-button" href="javascript:window.history.go(-1);" onmouseout="MM_swapImgRestore();" onmouseover="MM_swapImage('restore','','images/icons/restore_f2.png',1);">
            <span><span><IMG src='images/icons/restore.png' width="23" height="23" alt="Go to:" align="middle" border="0" name="restore" />&nbsp;&nbsp;<?=$this->multi['BTN_BACK'];?></span></span></a>
        <?

        $this->Form->WriteFooter();
    }

    /**
     * @return bool|int
     * @author Bogdan Iglinsky
     */
    function generate(){
//        echo '$this->cnt='.$this->cnt;;
        if(isset($this->cnt) && !empty($this->cnt)){
            $this->str_id = '';
            $rows = 0;
            for($i=0;$i<$this->cnt;$i++){
                $this->generateItem = 0;
                $id = $this->getUniqueCod($this->sum,$this->currency,$this->expires_date);
//                echo '<br>$i='.$i.' $id='.$id;
                if(!empty($id)){
                    if(!empty($this->str_id)) $this->str_id .= ',';
                    $this->str_id .= $id;
                    $rows++;
                }
            }
            return $rows;
        }
        else return false;
    }

    /**
     * @param null $sum
     * @param null $currency
     * @param null $expires_date
     * @return bool
     * @author Bogdan Iglinsky
     */
    function getUniqueCod($sum=NULL,$currency = NULL,$expires_date = NULL){
        $cod = $this->getCod();
//        echo '<br>$cod='.$cod;
        $q = "SELECT * FROM ".TblModPromoCod." WHERE `cod`='".$cod."'";
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
//        echo "<br / > q = ".$q." res = ".$res;
        if( !$res ) return false;
        $rows = $this->Rights->db_GetNumRows();
        if($rows>0){
            if($this->generateItem>=$this->maxGenerateItem) return false;
            $this->generateItem++;
            return $this->getUniqueCod();
        }else{
            $q = "INSERT INTO `".TblModPromoCod."` SET
                `cod`='".$cod."',
                `sum` = '".$sum."',
                `currency` = '".$currency."',
                `expires_date` = '".$expires_date."'
                ";
            $res = $this->Rights->Query( $q, $this->user_id, $this->module );
//            echo "<br / > q = ".$q." res = ".$res;
            if( !$res ) return false;

            $id = $this->Rights->db_GetInsertID();
            return $id;
        }
    }

    /**
     * @return null|string
     * @author Bogdan Iglinsky
     */
    function getCod(){
        // Символы, которые будут использоваться в пароле.
        $chars="qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPASDFGHJKLZXCVBNM";
        // Количество символов.
        $max=32;
        // Определяем количество символов в $chars
        $size=strlen($chars)-1;
        // Определяем пустую переменную, в которую и будем записывать символы.
        $cod=null;
        // Создаём код.
        while($max--)
            $cod .= $chars[rand(0,$size)];

        return $cod;
    }

    /**
     * @param bool $showOnlyNewId
     * @author Bogdan Iglinsky
     */
    function scriptInit($showOnlyNewId = false){
        if($showOnlyNewId){
            $this->script=$_SERVER['PHP_SELF']."?module=$this->module&str_id=$this->str_id";
        }else{
            $this->script = $_SERVER['PHP_SELF']."?module=$this->module&display=$this->display&start=$this->start";
            $this->script .= "&sort=$this->sort&asc_des=$this->asc_desc&str_id=$this->str_id&search_status=$this->search_status";
            $this->script .= "&search_cod=$this->search_cod&search_sum_from=$this->search_sum_from&search_sum_to=$this->search_sum_to";
            $this->script .= "&search_currency=$this->search_currency";
        }
    }

    /**
     * @param $id_del
     * @return bool
     * @author Bogdan Iglinsky
     */
    function export($id_del){
        $csv_separator = ";";
        $txt_separator = "	";
        $csv_terminated = "\n";
        $filename = 'promo_cod.csv';
        $filenameTxt = 'promo_cod.txt';
        $outPutArray = '';
        $outPutArrayTxt = '';
        $str_id = implode(',',$id_del);
        // Выборка категорий и товаров
        $q = "SELECT
                    `".TblModPromoCod."`.*
              FROM
                    `".TblModPromoCod."`
              WHERE
                     `".TblModPromoCod."`.`id`in (".$str_id.")
              ORDER BY `".TblModPromoCod."`.`id` asc";
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
//        echo '<br>'.$q.'<br/> $res='.$res.' $this->Rights->result='.$this->Rights->result;
        if( !$res OR !$this->Rights->result) return false;
        $rows = $this->Rights->db_GetNumRows();
        //echo '<br>$rows='.$rows;

        // Формирование заголовка таблицы
        $outPutArray .= $this->multi['_FLD_CODE'].$csv_separator.
            $this->multi['FLD_SUMA'].$csv_separator.
            $this->multi['FLD_STATUS'].$csv_separator.
            $this->multi['TXT_SKILL_INVITE_EXPIRES_DATE'].$csv_terminated;

        $outPutArrayTxt .= $this->multi['_FLD_CODE'].$txt_separator.
            $this->multi['FLD_SUMA'].$txt_separator.
            $this->multi['FLD_STATUS'].$txt_separator.
            $this->multi['TXT_SKILL_INVITE_EXPIRES_DATE'].$csv_terminated;

        $def_curr = $this->Currencies->defCurrencyData['id'];
        for($i=0; $i<$rows; $i++){
            $row = $this->Rights->db_FetchAssoc();
            $sum = $this->Currencies->ShowPrice($this->Currencies->Converting($row['currency'],$def_curr,$row['sum']));
            $status = $this->arr_status[$row['status']];
            $outPutArray .= $row['cod'].$csv_separator.
                $sum.$csv_separator.
                $status.$csv_separator.
                $row['expires_date'].$csv_terminated;
            $outPutArrayTxt .= $row['cod'].$txt_separator.
                $sum.$txt_separator.
                $status.$txt_separator.
                $row['expires_date'].$csv_terminated;
        }

        //csv
        $uploaddir = SITE_PATH.'/export';
        $fullpath = $uploaddir.'/'.$filename;
        if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
        else @chmod($uploaddir,0777);

        if (!$handle = fopen($fullpath, 'w')) {
            echo "Не могу открыть файл ($fullpath)";
            return;
        }
        // Записываем $outPutArray в наш открытый файл.
        if (fwrite($handle, $outPutArray) === FALSE) {
            echo "Не могу произвести запись в файл ($fullpath)";
            return;
        }
        fclose($handle);
        $path = '/export/'.$filename;
        $path_show = 'http://'.NAME_SERVER.$path;

        if($path){
            echo 'Скачать файл <a href="http://'.NAME_SERVER.'/modules/mod_catalog/report_download.php?path='.$path.'&module='.$this->module.'&task=export_to_csv">'.$path_show.'</a>';
        }
        else{
            echo 'Ошибка. '.$filename.' не экспортировался';
        }
        echo '<br>';
        //txt
        $uploaddir = SITE_PATH.'/export';
        $fullpath = $uploaddir.'/'.$filenameTxt;
        if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
        else @chmod($uploaddir,0777);

        if (!$handle = fopen($fullpath, 'w')) {
            echo "Не могу открыть файл ($fullpath)";
            return;
        }
        // Записываем $outPutArray в наш открытый файл.
        if (fwrite($handle, $outPutArrayTxt) === FALSE) {
            echo "Не могу произвести запись в файл ($fullpath)";
            return;
        }
        fclose($handle);
        $path = '/export/'.$filenameTxt;
        $path_show = 'http://'.NAME_SERVER.$path;

        if($path){
            echo 'Скачать файл <a href="http://'.NAME_SERVER.'/modules/mod_catalog/report_download.php?path='.$path.'&module='.$this->module.'&task=export_to_csv">'.$path_show.'</a>';
        }
        else{
            echo 'Ошибка. '.$filenameTxt.' не экспортировался';
        }
    }

    /**
     * @param $str
     * @return string
     * @author Bogdan Iglinsky
     */
    function Conv($str)
    {
        //echo '<br/>$this->from_charset ='.$this->from_charset;
        //echo '<br/>$this->to_charset ='.$this->to_charset;
        if($this->to_charset!=$this->from_charset){
            $str = iconv($this->from_charset, $this->to_charset, $str);
        }
        return $str;
    }// end of fucntion function Conv()
}