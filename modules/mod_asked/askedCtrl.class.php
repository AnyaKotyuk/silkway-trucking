<?php
include_once( SITE_PATH.'/modules/mod_asked/asked.defines.php' );

/**
 * AskedCtrl
 *
 * @package SEOCMS
 * @author Yaroslav
 * @copyright 01.03.2012
 * @version 1.0
 * @access public
 */
class AskedCtrl extends Asked {


    /**
     * AskedCtrl::AskedCtrl()
     * Constructor
     * @author Yaroslav
     * @return void
     */
    function __construct($user_id=NULL, $module=NULL)
    {
        $this->user_id = $user_id;
        $this->module = $module;

        $this->db = DBs::getInstance();
        $this->Right =  check_init("RightsQA", "Rights", "'".$this->user_id."','".$this->module."'");
        $this->Form = check_init("Form", "Form", "'form_asked'" );
        $this->Spr = check_init("SysSpr", "SysSpr");
        $this->multi = check_init_txt('TblBackMulti',TblBackMulti);
    }


    /**
     * AskedCtrl::ShowContent()
     * @author Yaroslav
     * @return
     */
    function ShowContent()
    {
        $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr;
        $script = $_SERVER['PHP_SELF']."?$script";

        if( !$this->sort ) $this->sort='id';
        if( strstr( $this->sort, 'display' ) )$this->sort = $this->sort.' desc';
        $q = "SELECT * FROM ".TblModAsked." WHERE 1 ";
        if( $this->fltr ) $q = $q." and $this->fltr";
        $q = $q." ORDER BY $this->sort";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        if( !$res )return false;

        $rows = $this->Right->db_GetNumRows();

        if($rows>$this->display) $ch = $this->display;
        else $ch = $rows;
        $this->Form->WriteHeader( $script );

        AdminHTML::TablePartH();

        /* Write Links on Pages */
        echo '<tr><td colspan="7">';
        $script1 = 'module='.$this->module.'&fltr='.$this->fltr;
        $script1 = $_SERVER['PHP_SELF']."?$script1";
        $this->Form->WriteLinkPages( $script1, $rows, $this->display, $this->start, $this->sort );


        echo '<tr><td colspan="4">';
        $this->Form->WriteTopPanel( $script );

        ?><tr>
        <th class="THead"><input value="0" id="cAll" onclick="if (this.value == '1') {unCheckAll(<?=$ch;?>); this.value = '0';} else {checkAll(<?=$ch;?>); this.value = '1';}" type="checkbox"></th><?
        echo "<th class=\"THead\">ID</th>";
        if(ASKED_AUTOR){echo "<th class=\"THead\">".$this->multi['AUTOR']."</th>";}
        if(ASKED_EMAIL){echo "<th class=\"THead\">".$this->multi['EMAIL']."</th>";}
        if(ASKED_DATE){echo "<th class=\"THead\">".$this->multi['FLD_DATE']."</th>";}
        if(ASKED_RATING){echo "<th class=\"THead\">".$this->multi['FLD_RATING']."</th>"; }
        echo "<th class=\"THead\">".$this->multi['QUESTIONS']."</th>";
        echo "<th class=\"THead\">".$this->multi['ANSVER']."</th>";
        echo "<th class=\"THead\">".$this->multi['PUBLISH']."</th>";
        echo "</tr>";

        $q = "SELECT * FROM " . TblModAsked . " ORDER BY `id` DESC";

        $res = $this->db->db_Query( $q );
        if(!$res) return false;
        $rows = $this->db->db_GetNumRows();

        $style1 = 'tr1';
        $style2 = 'tr2';
        for( $i = 0; $i < $rows; $i++ ) {
            $row = $this->db->db_FetchAssoc();

            if( $i >=$this->start && $i < ( $this->start+$this->display ) ) {
                if ($i & 1) {
                    echo '<tr class="'.$style1.'">';
                } else {
                    echo '<tr class="'.$style2.'">';
                }

                echo '<td>';
                //$this->Form->CheckBox( "id_del[]", $row['id'], $this->sel);
                $this->Form->CheckBox( "id_del[]", $row['id'], null, "check".$i );
                echo '</td>';

                echo '<td>';
                $this->Form->Link( $script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->multi['_TXT_EDIT_DATA'] );
                echo '</td>';

                if(ASKED_AUTOR){
                    echo '<td>';
                    $this->Form->Link( $script."&task=edit&id=".$row['id'], stripslashes( $row['author'] ), $this->multi['_TXT_EDIT_DATA'] );
                    echo '</td>';
                }
                if(ASKED_EMAIL){echo '<td align="center"><a href="mailto:"'.stripslashes($row['email']).'"">'.stripslashes($row['email']).'</a></td>';}
                if(ASKED_DATE){echo '<td align="center">'.trim($row['dttm']).'</td>';}
                if(ASKED_RATING){ echo '<td align="center">'.trim($row['rating']).'</td>';}
                echo '<td align="center">'.trim($row['question']).'</td>';
                echo '<td align="center">'.trim($row['answer']).'</td>';
                if ($row['visible'] == '1') {
                    $visible = '<img src="images/icons/tick.png">';
                } else {
                    $visible = '<img src="images/icons/publish_x.png">';
                }
                echo '<td align="center">'.$visible.'</td>';
                echo '</tr>';
            }
        }
        echo "</table>";

        AdminHTML::TablePartF();
        $this->Form->WriteFooter();
        return true;
    }


    /**
     * AskedCtrl::edit()
     * Edit/add records in Asked module
     * @author YAroslav
     * @param mixed $id - id of editing record / Void
     * @param mixed $mas / array of form values
     * @return true,false / Void
     */
    function edit( $id, $mas=NULL )
    {
        $row = array();
        $row['id'] = ''; $row['author'] = ''; $row['email'] = ''; $row['question'] = ''; $row['answer'] = ''; $row['visible'] = '';
        $calendar = new DHTML_Calendar(false, 'en', 'calendar-win2k-2', false);
        $calendar->load_files();

        if( $id!=NULL and ( $mas==NULL ) ) {
            $q = "SELECT * FROM `".TblModAsked."` where id='$id'";
            $res = $this->Right->Query($q, $this->user_id, $this->module);
            if( !$res ) return false;
            $row = $this->Right->db_FetchAssoc();
        }


        $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr;
        $script = $_SERVER['PHP_SELF']."?$script";
        $this->Form->WriteHeader( $script );

        if( $id!=NULL ) $txt = $this->multi['_TXT_EDIT_DATA'];
        else $txt = $this->multi['_TXT_ADD_DATA'];

        AdminHTML::PanelSubH( $txt );
        $this->Form->ShowErrBackEnd($this->Err);
        AdminHTML::PanelSimpleH();
        echo '<table border="0">';
        echo '<tr><td valign="top" width="150"><b>' . $this->multi['_FLD_ID'] . '</b></td>';

        if( $id ) {
            echo '<td>' . $row['id'] . '</td>';
            $this->Form->Hidden( 'id', $row['id'] );
        }
        echo '</tr>';
        if(ASKED_CATEGORY){
            ?>
            <tr>
                  <td width="110"><b><?=$this->multi['_FLD_CATEGORY'];?>:</b></td>
                  <td>
                   <?
                   if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->category : $val=$row['category'];
                   else $this->Err!=NULL ? $val=$this->category : $val='';
                   $this->Spr->ShowInComboBox( TblModAskedCat, 'category', stripslashes($val), 40, $this->multi['TXT_SELECT_CATEGORY'] );
                   ?>
                  </td>
                 </tr>
            <?
        }
        if(ASKED_AUTOR){
            $this->showField($this->multi['AUTOR'],'author',$row,$this->author);
        }
        if(ASKED_EMAIL){
            $this->showField($this->multi['EMAIL'],'email',$row,$this->email);
        }
        if(ASKED_DATE){
            ?>
            <tr>
                <td><b><?=$this->multi['FLD_DATE']?></b></td>
                <td><?
                  if( $this->id!=NULL ) $this->Err!=NULL ? $date_val = $this->date : $date_val = $row['dttm'];
                  else $date_val=strftime('%Y-%m-%d %H:%M', strtotime('now'));
                  //if( empty($start_date_val) ) $start_date_val = strftime('%Y-%m-%d %H:%M', strtotime('now'));
                  $a1 = array('firstDay'       => 1, // show Monday first
                             'showsTime'      => true,
                             'showOthers'     => true,
                             'ifFormat'       => '%Y-%m-%d %H:%M',
                             'timeFormat'     => '12');
                  $a2 = array('style'       => 'width: 15em; color: #840; background-color: #ff8; border: 1px solid #000; text-align: center',
                              'name'        => 'date',
                              'value'       => $date_val );
                  $calendar->make_input_field( $a1, $a2 );?>
              </td>
            </tr>
            <?
        }

        if(ASKED_RATING){
            echo '<tr><td><b>'.$this->multi['FLD_RATING'].'</b></td>';
            echo '<td>';
            for($i=0;$i<6;$i++){
                if($this->id!=NULL && $i==$row['rating'])$checked = ' checked=""';
                else $checked = '';
                ?><input type="radio"<?=$checked?> name="rating" value="<?=$i?>" id="rating<?=$i?>" />
                <label style="cursor: pointer" for="rating<?=$i?>"><?=$i?></label><br/><?
            }
            echo '</td>';
            echo '</tr>';
        }

        $this->showField($this->multi['QUESTIONS'],'question',$row,$this->question);
        $this->showField($this->multi['ANSVER'],'answer',$row,$this->answer);

        echo '<tr><td><b>'.$this->multi['PUBLISH'].'</b></td>';
        echo '<td>';
        $this->Form->CheckBox('visible', '1', $row['visible'] );
        echo '</td>';
        echo '</tr>';

        echo '<tr><td colspan="2" align="left">';
        $this->Form->WriteSavePanel( $script );?>&nbsp;<?
        $this->Form->WriteCancelPanel( $script );
        echo '</td></tr>';
        echo '</table>';
        AdminHTML::PanelSimpleF();
        AdminHTML::PanelSubF();
        $this->Form->WriteFooter();

        return true;
    }

    /**
     * AskedCtrl::getField()
     * @author Bogdan
     * @return
     */
    function showField($name_txt,$name,$row,$pole){
        ?><tr><td><b><?=$name_txt?></b></td><td><?

        if(!empty($pole)) $val = $pole;
        else $val = $row[$name];
        $this->Form->TextArea($name, $val);

        ?></td></tr><?
    }

    /**
     * AskedCtrl::CheckFields()
     * @author Bogdan
     * @return
     */
    function CheckFields(){
        $this->Err = '';
        if(empty($this->question)){
            $this->Err .= $this->multi['MSG_QUESTION_EMPTY'];
        }
    }

    /**
     * AskedCtrl::save()
     * @author Yaroslav
     * @return
     */
    function save()
    {
        $q = "SELECT * FROM `".TblModAsked."` WHERE `id` = '".$this->id."'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        if( !$res ) return false;
        $rows = $this->Right->db_GetNumRows();
        if( $rows > 0 ) {
            if(empty($this->date) || $this->date=='0000-00-00 00:00:00') {
                $this->date = strftime('%Y-%m-%d %H:%M', strtotime('now'));
            }
            $q = "UPDATE `".TblModAsked."` SET
                     `category` = '".$this->category."',
                     `author` = '".$this->author."',
                     `email` = '".$this->email."',
                     `question` = '".$this->question."',
                     `answer` = '".$this->answer."',
                     `visible`= '".$this->visible."',
                     `dttm`= '".$this->date."',
                     `rating` = '".$this->rating."'
                  WHERE id = '".$this->id."'";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );
            if( !$res ) return false;
       }
       else {
           $q = "INSERT INTO `".TblModAsked."` SET
                     `category` = '".$this->category."',
                     `author` = '".$this->author."',
                     `email` = '".$this->email."',
                     `question` = '".$this->question."',
                     `answer` = '".$this->answer."',
                     `visible`= '".$this->visible."',
                     `dttm`= '".strftime('%Y-%m-%d %H:%M', strtotime('now'))."',
                     `rating` = '".$this->rating."'";
           $res = $this->Right->Query( $q, $this->user_id, $this->module );
           if( !$res ) return false;
       }
        return true;
    }


    /**
     * AskedCtrl::del()
     * Remove data from the table
     * @author Yaroslav
     * @param mixed $id_del
     * @return
     */
    function del( $id_del )
    {
        $kol = count( $id_del );
        $del = 0;
        for( $i=0; $i<$kol; $i++ ) {
            $u = $id_del[$i];
            $q = "select * from ".TblModAsked." where id='$u'";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );
//            echo '<br>$q='.$q.' $res='.$res;
            if(!$res) return false;
            $rows = $this->Right->db_GetNumRows();
//            echo '<br>$rows='.$rows;
            if($rows>0){
                $q="DELETE FROM `".TblModAsked."` WHERE id='$u'";
                $res = $this->Right->Query( $q, $this->user_id, $this->module );
                if ( $res )
                    $del=$del+1;
                else
                    return false;
            }
        }
      return $del;
    }
}