<?
// ================================================================================================
//    System     : SEOCMS
//    Module     : Department
//    Date       : 01.07.2010
//    Licensed To: Yaroslav Gyryn
//    Purpose    : Class definition for Department - module
// ================================================================================================

include_once( SITE_PATH.'/modules/mod_department/department.defines.php' );

// ================================================================================================
//    Class             : DepartmentDoctorCtrl
//    Date              : 01.07.2010
//    Constructor       : Yes
//    Parms             :
//    Returns           : None
//    Description       : Department Module
//    Programmer        : Yaroslav Gyryn
// ================================================================================================

class DepartmentDoctorCtrl extends Department{

    var $is_tags = NULL;
    var $UploadImages = NULL;

    // ================================================================================================
    //    Function          : DepartmentDoctorCtrl (Constructor)
    //    Version           : 1.0.0
    //    Date              : 01.07.2010
    //    Parms             :
    //    Returns           :
    //    Description       : Department
    // ================================================================================================
    function __construct($user_id = NULL, $module = NULL)
    {
        $this->user_id = $user_id;
        $this->module = $module;

        if(defined("_LANG_ID")) $this->lang_id = _LANG_ID;
        $this->db =  DBs::getInstance();
        $this->Right =  check_init('RightsDep', 'Rights', "'".$this->user_id."','".$this->module."'");
        $this->Form = new Form( 'form_department_doctor' );     /* create Form object as a property of this class */
        $this->Msg = new ShowMsg();                   /* create ShowMsg object as a property of this class */
        $this->Msg->SetShowTable(TblModDepartmentSprTxt);
        $this->settings = $this->GetSettings();
        $this->ln_sys = new SysLang();
        $this->ln_arr = $this->ln_sys->LangArray( _LANG_ID );
        //$this->UploadImages = new UploadImage(149, null, $this->settings['img_path'],'mod_department_doctor_img',NULL,NULL,$this->ln_arr, 800, 1024);
        //$this->UploadImages->CreateTables();

        $this->Spr = new SysSpr( NULL, NULL, NULL, NULL, NULL, NULL, NULL ); /* create SysSpr object as a property of this class */
        $this->multi = check_init_txt('TblBackMulti',TblBackMulti);

        $this->UploadImages = check_init('UploadImage', 'UploadImage', "'94', 'null', 'uploads/images/departamentDoctor'");
    }


    // ================================================================================================
    // Function : show()
    // Date : 01.07.2010
    // Parms :
    // Returns :     true,false / Void
    // Description : Show Department
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function show()
    {
        $db = DBs::getInstance();
        $frm = new Form('fltr');
        $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr.'&fln='.$this->fln;
        $script = $_SERVER['PHP_SELF']."?$script";

        if( !$this->sort ) $this->sort = 'position';
        if( strstr( $this->sort, 'position' ) ) $this->sort = $this->sort.' desc';

        $q = "SELECT `".TblModDepartmentDoctor."`.*,
                          `".TblModDepartmentTxt."`.name AS `department_name`,
                          `".TblModDepartmentDoctorTxt."`.name,
                          `".TblModDepartmentDoctorTxt."`.post,
                          `".TblModDepartmentDoctorTxt."`.work_time
              FROM `".TblModDepartmentDoctor."`, `".TblModDepartmentTxt."`, `".TblModDepartmentDoctorTxt."`
              WHERE `".TblModDepartmentDoctor."`.id_department =`".TblModDepartmentTxt."`.cod
              AND `".TblModDepartmentTxt."`.lang_id='".$this->lang_id."'
              AND `".TblModDepartmentDoctor."`.id=`".TblModDepartmentDoctorTxt."`.cod
              AND `".TblModDepartmentDoctorTxt."`.lang_id='".$this->lang_id."'
             ";

        if( $this->fltr ) $q = $q." AND `".TblModDepartmentDoctor."`.".$this->fltr;
        $q = $q." ORDER BY `".TblModDepartmentDoctor."`.".$this->sort;

        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo '<br>'.$q.' <br/>$res='.$res;
        if( !$res )return false;

        $rows = $this->Right->db_GetNumRows();
        for( $i = 0; $i < $rows; $i++ )
        {
            $arr_row[] = $this->Right->db_FetchAssoc();
        }
        /* Write Form Header */
        $this->Form->WriteHeader( $script );

        /* Write Table Part */
        AdminHTML::TablePartH();

        /* Write Links on Pages */
        echo '<TR><TD COLSPAN=11>';
        $script1 = 'module='.$this->module.'&fltr='.$this->fltr;
        $script1 = $_SERVER['PHP_SELF']."?$script1";
        $this->Form->WriteLinkPages( $script1, $rows, $this->display, $this->start, $this->sort );

        echo '<TR><TD COLSPAN=3>';
        $this->Form->WriteTopPanel( $script );
        echo '<td colspan=6 CLASS="TR1">'.$this->multi['_TXT_FILTR'].": ";
        $arr = NULL;
        $arr[''] = 'All';

        $q = "SELECT `cod`, `name`
           FROM `".TblModDepartmentTxt."`
           WHERE `lang_id`='".$this->lang_id."'
          ";
        $res = $db->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res;
        $rows1 = $db->db_GetNumRows();
        for( $i = 0; $i < $rows1; $i++ )
        {
            $row1 = $db->db_FetchAssoc();
            $arr['id_department='.$row1['cod']] = stripslashes($row1['name']);
        }
        $this->Form->SelectAct( $arr, 'id_department', $this->fltr, "onChange=\"location='".$script."'+'&fltr='+this.value\"" );

        echo '<td><td><td colspan=2>';
        $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr;
        $script2 = $_SERVER['PHP_SELF']."?$script2";

        if($rows1>$this->display) $ch = $this->display;
        else $ch = $rows;
//    var_dump($this->multi);
        ?>
        <TR>
        <td class="THead"><input value="0" id="cAll" onclick="if (this.value == '1') {unCheckAll(<?=$ch;?>); this.value = '0';} else {checkAll(<?=$ch;?>); this.value = '1';}" type="checkbox"></Th>
        <td class="THead"><A HREF=<?=$script2?>&sort=id><?=$this->multi['_FLD_ID'];?></A></Th>
        <td class="THead"><?=$this->multi['_FLD_F_NAME'];?></Th>
        <td class="THead"><?=$this->multi['TXT_DEPARTMENT_TITLE'];?></Th>
        <td class="THead"><?=$this->multi['_FLD_STATUS'];?></Th>
        <td class="THead"><?=$this->multi['FLD_POSITION'];?></Th>
        <td class="THead"><?=$this->multi['TXT_OPERATION_TIME'];/*?></Th>
     <td class="THead"><?=$this->multi['_FLD_IMG'];*/?></Th>
        <td class="THead"><?=$this->multi['FLD_EMAIL'];?></Th>
        <td class="THead"><?=$this->multi['FLD_DISPLAY'];?></Th>
        <?
        $up = 0;
        $down = 0;
        $id = 0;

        $a = $rows;
        $j = 0;
        $row_arr = NULL;
        for( $i = 0; $i < $rows; $i++ )
        {
            $row = $arr_row[$i];
            if( $i >= $this->start && $i < ( $this->start+$this->display ) )
            {
                $row_arr[$j] = $row;
                $j = $j + 1;
            }
        }

        $style1 = 'TR1';
        $style2 = 'TR2';
        $n = count( $row_arr );
        for( $i = 0; $i <$n; $i++ )
        {
            $row = $row_arr[$i];

            if ( (float)$i/2 == round( $i/2 ) )
            {
                echo '<TR CLASS="'.$style1.'">';
            }
            else echo '<TR CLASS="'.$style2.'">';

            echo '<TD>';
            $this->Form->CheckBox( "id_del[]", $row['id'], null, "check".$i );

            echo '<TD>';
            $this->Form->Link( $script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->multi['_TXT_EDIT_DATA'] );

            /* Name */
            echo '<TD align=center>';
            $this->Form->Link( $script."&task=edit&id=".$row['id'], stripslashes($row['name']), $this->multi['_TXT_EDIT_DATA'] );

            /* Category */
            echo '<TD align=center>';
            $this->Form->Link( $script."&task=show&fltr=id_department=".$row['id_department'], stripslashes($row['department_name']), $this->multi['_TXT_FILTR'] );

            /*Status*/
            echo '<TD align=center>';
            if( $row['status'] =='i') echo $this->multi['_FLD_INACTIVE'];
            //if( $row['status'] =='e') echo $this->multi['_FLD_EXPIRED'];
            if( $row['status'] =='a') echo $this->multi['FLD_ACTIVE'];

            /*Post*/
            echo '<TD align=center>';
            $post = trim( stripslashes($row['post']) );
            if( $post ) $this->Form->ButtonCheck();

            /*Work_time*/
            echo '<TD align=center>';
            $work_time = trim( stripslashes($row['work_time']) );
            if( $work_time ) $this->Form->ButtonCheck();

            /*Email*/
            echo '<TD align=center>';
            $email = trim( stripslashes($row['email']) );
            if( $email ) {
                //$this->Form->ButtonCheck();
                echo $email;
            }

            echo '<TD align=center>';
            if( $up != 0 )
            {
                ?>
                <a href=<?=$script?>&task=up&move=<?=$row['position']?>>
                    <?=$this->Form->ButtonUp( $row['id'] );?>
                </a>
            <?
            }

            if( $i != ( $rows - 1 ) )
            {
                ?>
                <a href=<?=$script?>&task=down&move=<?=$row['position']?>>
                    <?=$this->Form->ButtonDown( $row['id'] );?>
                </a>
            <?
            }
            $up = $row['id'];
            $a = $a - 1;
        } //-- end for

        AdminHTML::TablePartF();
        $this->Form->WriteFooter();
        return true;
    }


    // ================================================================================================
    // Function : edit()
    // Date : 01.07.2010
    // Parms :
    // Returns : true,false / Void
    // Description : edit/add records in Department module
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function edit()
    {
        $Panel = new Panel();

        $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr;
        $script = $_SERVER['PHP_SELF']."?$script";

        if( $this->id!=NULL )
        {
            $q = "SELECT `".TblModDepartmentDoctor."`.*
             FROM `".TblModDepartmentDoctor."`
             WHERE `".TblModDepartmentDoctor."`.`id`='".$this->id."'
            ";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );
            if( !$res ) return false;
            $mas = $this->Right->db_FetchAssoc();
        }

        /* Write Form Header */
        $this->Form->WriteHeaderFormImg( $script );
        $this->Form->textarea_editor = 'FCK'; //'tinyMCE';
        $this->Form->IncludeSpecialTextArea();

        if( $this->id!=NULL ) $txt = $this->multi['_TXT_EDIT_DATA'];
        else $txt = $this->multi['_TXT_ADD_DATA'];

        AdminHTML::PanelSubH( $txt );
        //-------- Show Error text for validation fields --------------
        $this->ShowErrBackEnd();
        //------------------------------------------------------------
        AdminHTML::PanelSimpleH();
        ?>
        <table class="EditTable"><tr><td valign=top align=left>
                    <TABLE BORDER=0 class="EditTable" width="100">
                        <TR><TD><?=$this->multi['_FLD_ID'];?>:&nbsp;
                                <?
                                if( $this->id!=NULL )
                                {
                                    echo $mas['id'];
                                    $this->Form->Hidden( 'id', $mas['id'] );
                                }
                                else $this->Form->Hidden( 'id', '' );
                                ?>
                        <TR><TD><?=$this->multi['FLD_DISPLAY'];?>:&nbsp;
                                <?
                                if( $this->id!=NULL )
                                {
                                    echo $mas["position"];
                                    echo '<input type="hidden" name=position VALUE="'.$mas["position"].'">';
                                }
                                else
                                {
                                    $q="select MAX(`position`) from `".TblModDepartmentDoctor."`";
                                    $res = mysql_query( $q );
                                    $tmp = mysql_fetch_array( $res );
                                    $maxx = $tmp['MAX(`position`)'] + 1;
                                    echo $maxx;
                                    echo '<INPUT TYPE=hidden NAME=position VALUE="'.$maxx.'">';
                                }
                                ?>
                    </table>

                <td width=100%>
                    <table class="EditTable">
                        <TR>
                            <TD><b><?=$this->multi['_FLD_STATUS'];?>:</b>
                            <TD>
                                <?
                                $arr = NULL;
                                $arr['a'] = $this->multi['FLD_ACTIVE'];
                                $arr['i'] = $this->multi['_FLD_INACTIVE'];

                                if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->status : $val=$mas['status'];
                                else $this->Err!=NULL ? $val=$this->status : $val='a';
                                $this->Form->Select( $arr, 'status', $val, NULL );
                                ?>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <br/>
                    <b><?=$this->multi['TXT_DEPARTMENT_TITLE'];?>:</b><br/>
                    <?
                    $emptyVal='';
                    if(!empty($this->fltr)) {
                        $fltr = explode("=", $this->fltr);
                        $emptyVal = $fltr[1];
                    }

                    //echo '<br/>mas[id_department]'.$mas['id_department'];
                    $arr_categs = $this->PrepareDepartmentForSelect(0, NULL, NULL, 'back', true, true, false, false, NULL, NULL);
                    $arr_props = $this->PreparePositionsTreeForSelect('all', 'back', 'move', 'asc', null);
                    if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->category : $val=$mas['id_department'];
                    else $this->Err!=NULL ? $val=$this->category : $val=$emptyVal;

                    $this->ShowDepartmentInSelect($arr_categs, $arr_props, '--- '.$this->multi['TXT_SELECT_OFFICE'].' ---', 'category', $val,'');
                    ?>
                    <br/>
                    <br/>
                    <b><?=$this->multi['FLD_EMAIL']?>:</b><br><?
                    if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->email : $val=$mas['email'];
                    else $this->Err!=NULL ? $val=$this->email : $val='';
                    $this->Form->TextBox( 'email', stripslashes($val), 110 );
                    ?>
                    <br><br>
                </td>
            </tr>
        </table>
        <?
        $Panel->WritePanelHead( "SubPanel_" );
        $tmp_bd= new DB();
        $q1="select * from `".TblModDepartmentDoctorTxt."` where 1";
        if( $this->id!=NULL )
            $q1 = $q1." AND `".TblModDepartmentDoctorTxt."`.`cod`='".$this->id."'";

        $res = $tmp_bd->db_query( $q1);
        if( !$tmp_bd->result ) return false;
        $rows1 = $tmp_bd->db_GetNumRows();
        $txt= array();
        for($i=0; $i<$rows1;$i++)
        {
            $row1 = $tmp_bd->db_FetchAssoc();
            $txt[$row1['lang_id']]=$row1;
        }

        while( $el = each( $this->ln_arr ) )
        {
            $lang_id = $el['key'];
            $lang = $el['value'];
            $mas_s[$lang_id] = $lang;

            $Panel->WriteItemHeader( $lang );
            ?><table border="0" class="EditTable" width="100%">
            <tr>
                <td>
                    <b><?=$this->multi['_FLD_F_NAME']?>:</b><br><?
                    $row = NULL;
                    if (isset($txt[$lang_id]['name']))
                        $row = $txt[$lang_id]['name'];
                    else $row='';
                    if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->name[$lang_id] : $val=$row;
                    else $this->Err!=NULL ? $val=$this->name[$lang_id] : $val = '';
                    $this->Form->TextBox( 'name['.$lang_id.']', stripslashes($val), 110 );
                    ?>
                    <br><br>

                    <b><?=$this->multi['FLD_POSITION']?>:</b><br><?
                    $row = NULL;
                    if (isset($txt[$lang_id]['post']))
                        $row = $txt[$lang_id]['post'];
                    else $row='';
                    if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->post[$lang_id] : $val=$row;
                    else $this->Err!=NULL ? $val=$this->post[$lang_id] : $val = '';
                    $this->Form->TextBox( 'post['.$lang_id.']', stripslashes($val), 110 );
                    ?>
                    <br><br>

                    <b><?=$this->multi['TXT_OPERATION_TIME']?>:</b><br><?
                    if (isset($txt[$lang_id]['work_time']))
                        $row = $txt[$lang_id]['work_time'];
                    else $row='';
                    if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->work_time[$lang_id] : $val=$row;
                    else $this->Err!=NULL ? $val=$this->work_time[$lang_id] : $val = '';
                    //$this->Form->SpecialTextArea(NULL, 'work_time['.$lang_id.']', stripslashes($val), 15, 70, NULL, $lang_id );
                    $this->Form->Textarea( 'work_time['.$lang_id.']', stripslashes($val), 10, 110 );
                    ?>
                    <br><br>

                </td>
            </tr>
            </table><?
            $Panel->WriteItemFooter();
        }

        //-------------------- Upload Files Start ---------------------
        $this->UploadImages->ShowFormToUpload(1,$this->id);?><br/><?
        //-------------------- Upload Files End ---------------------

        $Panel->WritePanelFooter();
        $this->Form->WriteSavePanel( $script );
        $this->Form->WriteCancelPanel( $script );
        //$this->Form->WritePreviewPanel( 'http://'.NAME_SERVER."/modules/mod_department/department.preview.php" );

        $this->Form->WriteFooter();
        AdminHTML::PanelSimpleF();
        AdminHTML::PanelSubF();
    }

    // ================================================================================================
    // Function : CheckFields()
    // Version : 1.0.0
    // Date : 01.07.2010
    // Parms :
    // Returns :      true,false / Void
    // Description :  Checking all fields for filling and validation
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function CheckFields()
    {
        $this->Err=NULL;
        if (empty( $this->category ))
            $this->Err=$this->Err.$this->multi['MSG_FLD_CATEGORY_EMPTY'].'<br>';

        $i=0;
        while( $el = each( $this->ln_arr ) ){
            $lang_id = $el['key'];
            if( !empty( $this->name[$lang_id] ) )
                continue;
            else $i++;
        }
        if($i==count($this->name))
            $this->Err=$this->Err.$this->multi['MSG_FLD_NAME_EMPTY'].'<br>';

        //echo '<br>$this->Err='.$this->Err.' $this->Msg->table='.$this->Msg->table;
        return $this->Err;
    } //end of fuinction CheckFields()

    // ================================================================================================
    // Function : save()
    // Description : Store DepartmentDoctor ( Save )
    // Returns : true,false / Void
    // Date : 01.07.2010
    // ================================================================================================
    function save()
    {
        if( $this->id )
        {
            $q = "SELECT * FROM ".TblModDepartmentDoctor." WHERE `id`='$this->id'";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );
            if( !$res ) return false;

            $rows = $this->Right->db_GetNumRows();
        }
        else $rows = 0;

        if( $rows > 0 )   //--- update
        {
            $q = "update `".TblModDepartmentDoctor."` set
               `id_department`='$this->category',
               `email`='$this->email',
               `status`='$this->status',
               `position`='$this->position'
                where
                id='$this->id'";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );
            if( !$res ) return false;
        }
        else          //--- insert
        {
            $q = "insert into `".TblModDepartmentDoctor."` values(NULL,'$this->category', '$this->email', '$this->status', '$this->position')";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );
            //echo '<br/>'.$q.'<br/> $res = '.$res;
            if( !$res ) return false;

            $this->id = $this->Right->db_GetInsertID();
        }

        $q="select * from `".TblModDepartmentDoctorTxt."` where `cod`='$this->id'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo '<br><br>'.$q.'<br/> res='.$res.' $this->Right->result='.$this->Right->result;
        if( !$this->Right->result ) return false;
        $rows = $this->Right->db_GetNumRows();
        $_txst= array();

        for($i=0; $i<$rows; $i++)
        {
            $row = $this->Right->db_FetchAssoc();
            $_txst[$row['lang_id']]='1';
        }
        $count = count($this->ln_arr);
        $el = array_keys($this->ln_arr);
        //print_r($_txst);
        for( $i=0; $i < $count; $i++ )
        {
            $lang_id = $el[$i];
            $name_ = addslashes( strip_tags(trim($this->name[ $lang_id])) );
            $post_ = addslashes( strip_tags(trim($this->post[ $lang_id ])) );
            $work_time_ = addslashes( $this->work_time[ $lang_id ] );

            if (isset($_txst[$lang_id]))
            {
                $q="update `".TblModDepartmentDoctorTxt."` set
                  `name`='".$name_."',
                  `post`='".$post_."',
                  `work_time`='".$work_time_."'
                  WHERE `lang_id`='$lang_id' and `cod`='$this->id'";
                $res = $this->Right->Query( $q, $this->user_id, $this->module );
                //echo '<br><br>'.$q.'<br/> res='.$res.' $this->Right->result='.$this->Right->result;
                if( !$res ) return false;
                if( !$this->Right->result ) return false;
            }
            else
            {
                $q="insert into `".TblModDepartmentDoctorTxt."`  set
                  `name`='".$name_."',
                  `post`='".$post_."',
                  `work_time`='".$work_time_."',
                  `lang_id`='$lang_id',`cod`='$this->id'";
                $res = $this->Right->Query( $q, $this->user_id, $this->module );
                //echo '<br><br>'.$q.'<br/> $res='.$res.' $this->Right->result='.$this->Right->result;
                if( !$this->Right->result) return false;
            }
        } //--- end while

        $this->UploadImages->SaveImages($this->id);
        return true;
    }


    // ================================================================================================
    // Function : del()
    // Date : 01.07.2010
    // Parms :
    // Returns :      true,false / Void
    // Description :  Remove data from the table
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function del( $id_del )
    {
        $kol = count( $id_del );
        $del = 0;
        for( $i = 0; $i < $kol; $i++ )
        {
            $u = $id_del[$i];
            $q = "DELETE FROM `".TblModDepartmentDoctor."` WHERE id='$u'";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );
            $q1 = "DELETE FROM `".TblModDepartmentDoctorTxt."` WHERE cod='$u'";
            $res = $this->Right->Query( $q1, $this->user_id, $this->module );

            //$this->UploadImages->DeleteAllImagesForPosition($u);

            if ( $res )
                $del = $del + 1;
            else
                return false;
        }
        return $del;
    }

    // ================================================================================================
    // Function : up()
    // Date : 01.07.2010
    // Parms :
    // Returns :      true,false / Void
    // Description :  Up
    // Programmer :  Yaroslav Gyryn
    // Date : 01.07.2010
    // ================================================================================================
    function up( $move )
    {
        $q = "select * from ".TblModDepartmentDoctor." where position='$move'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        if( !$res )return false;
        $rows = $this->Right->db_GetNumRows();
        $row = $this->Right->db_FetchAssoc();
        $move_down = $row['position'];
        $id_down = $row['id'];

        $q = "select * from ".TblModDepartmentDoctor." where position>'$move' order by position limit 1";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        if( !$res )return false;
        $rows = $this->Right->db_GetNumRows();
        $row = $this->Right->db_FetchAssoc();
        $move_up = $row['position'];
        $id_up = $row['id'];

        if( $move_down!=0 AND $move_up!=0 )
        {
            $q = "update ".TblModDepartmentDoctor." set
             position='$move_down' where id='$id_up'";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );

            $q = "update ".TblModDepartmentDoctor." set
             position='$move_up' where id='$id_down'";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );
        }
    }



    // ================================================================================================
    // Function : down()
    // Date : 01.07.2010
    // Parms :
    // Returns :      true,false / Void
    // Description :  Down
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function down( $move )
    {
        $q = "select * from ".TblModDepartmentDoctor." where position='$move'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        if( !$res )return false;
        $rows = $this->Right->db_GetNumRows();
        $row = $this->Right->db_FetchAssoc();
        $move_up = $row['position'];
        $id_up = $row['id'];

        $q = "select * from ".TblModDepartmentDoctor." where position<'$move' order by position desc limit 1";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        if( !$res )return false;
        $rows = $this->Right->db_GetNumRows();
        $row = $this->Right->db_FetchAssoc();
        $move_down = $row['position'];
        $id_down = $row['id'];

        if( $move_down!=0 AND $move_up!=0 )
        {
            $q = "update ".TblModDepartmentDoctor." set
         position='$move_down' where id='$id_up'";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );

            $q = "update ".TblModDepartmentDoctor." set
         position='$move_up' where id='$id_down'";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );
        }
    }

    // ================================================================================================
    // Function : ShowErrBackEnd()
    // Date : 01.07.2010
    // Parms :
    // Returns :      true,false / Void
    // Description :  Show errors
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function ShowErrBackEnd()
    {
        if ($this->Err){
            $title=$this->Msg->show_text('MSG_ERRORS', TblSysTxt);
            echo '
        <fieldset class="err" title="'.$title.'"> <legend>'.$title.'</legend>
        <div class="err_text">'.$this->Err.'</div>
        </fieldset>';
        }
    } //end of fuinction ShowErrBackEnd()


} // end of DepartmentDoctorCtrl Class
?>