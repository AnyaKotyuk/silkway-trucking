<?
include_once( SITE_PATH.'/admin/modules/sys_group/sys_group.class.php' );

/**
* Class AdminMenu
* Class for all actions with Admin Menu of Content System Management
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
class AdminMenu {

    public  $Right;
    public  $Form;
    public  $Msg;
    public  $Spr;

    public  $display;
    public  $sort;
    public  $start=0;
    public  $fltr;

    public  $user_id;
    public  $module_id;
    public $treePageList = NULL; //array $this->treePageList[]=$id_cat
    public $treePageLevels = NULL; //array $this->treePageLevels[level][id_cat]=''
    public $treePageData = NULL; //array treePageData[id_cat]=array with category data

    /**
    * AdminMenu::__construct()
    *
    * @param integer $user_id
    * @param integer $module_id
    * @param integer $display
    * @param string $sort
    * @param integer $start
    * @return void
    */
    function __construct( $user_id=NULL, $module_id=NULL, $display=NULL, $start=NULL, $sort=NULL, $fltr=NULL )
    {
     $this->user_id = $user_id;
     $this->module_id = $module_id;
     $this->display = $display;
     if( !empty( $sort ) ) $this->sort = $sort;
     if( !empty( $start ) ) $this->start = $start;
     if( !empty( $fltr ) ) $this->fltr = $fltr;

     $this->Right =  new Rights($this->user_id, $this->module_id);
     $this->Form = new Form( 'form_sys_func' );
     $this->Msg = check_init_txt('TblBackMulti',TblBackMulti);
     $this->Spr = new SysSpr();

     $this->loadTree();

    }

    /**
     * Class method loadTree
     * load all data of catalog categories to arrays
     * @return true/false or arrays:
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.1, 05.04.2011
     */
    function loadTree()
    {
        if( is_array($this->GetTreePageLevelAll()) AND is_array($this->GetTreePageDataAll()) ) return true;

        $q = "SELECT
                `".TblSysMenuAdm."`.*,
                `".TblSysSprMenuAdm."`.`name`
              FROM
                `".TblSysMenuAdm."`,
                `".TblSysSprMenuAdm."`
              WHERE
                `".TblSysSprMenuAdm."`.cod=`".TblSysMenuAdm."`.id
                AND `".TblSysSprMenuAdm."`.lang_id='"._LANG_ID."'
             ";
        if(!empty($this->fltr)){
            $q .= " AND `".TblSysMenuAdm."`.`group`='".$this->fltr."'";
        }
        $q .= " ORDER BY `move` asc";

        $res = $this->Right->Query($q);
        //echo $q.' <br/>$res = '.$res.' $this->Right->result='.$this->Right->result;
        if(!$res OR !$this->Right->result) return false;
        $rows = $this->Right->db_GetNumRows($res);
        if($rows==0)
            return false;

        $tree = array();

        for($i = 0; $i < $rows; $i++){
            $row = $this->Right->db_FetchAssoc($res);

            if(empty($tree[$row['level']])) {
                $tree[$row['level']] = array();
            }
            $this->SetTreeCatLevel($row['level'], $row['id'], $row['name']);
            //$this->treePageLevels[$row['level']][$row['id']]='';
            $this->SetTreePageData($row);
            //$this->treePageData[$row['id']]=$row;
        }
        return true;
    } //end of function loadTree()

    /**
     * Class method SetTreeCatLevel
     * set new vlaue to property $this->treePageLevels. It build array $this->treePageLevels[level][id_cat]=''
     * @param integer $level - id of the parent category
     * @param integer $id - id of the category
     * @return none
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 17.05.2011
     */
    function SetTreeCatLevel($level, $id, $name)
    {
        $this->treePageLevels[$level][$id] = $name;
    } //end of function SetTreeCatLevel()

    /**
     * Class method GetTreePageLevelAll
     * get array $this->treePageLevels
     * @return array $this->treePageLevels
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 17.05.2011
     */
    function GetTreePageLevelAll()
    {
        return $this->treePageLevels;
    } //end of function GetTreePageLevelAll()

    /**
     * Class method GetTreePageLevel
     * get node of array $this->treePageLevels where store array with sublevels
     * @param integer $item - id of the category as node in array
     * @return node of array $this->treePageLevels[$item]
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 17.05.2011
     */
    function GetTreePageLevel($item=0)
    {
        if(!isset($this->treePageLevels[$item])) return false;
        return $this->treePageLevels[$item];
    } //end of function GetTreePageLevel()

    /**
     * Class method SettreePageData
     * set new vlaue to property $this->treePageData. It build array $this->treePageData[id_cat]=array with category data
     * @param array $row - assoc array with data of category
     * @return true
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 17.05.2011
     */
    function SetTreePageData($row)
    {
        $this->treePageData[$row['id']]=$row;
        return true;
    } //end of function SettreePageData()

    /**
     * Class method SettreePageDataAddNew
     * set new vlaue to property $this->treePageData. It build array $this->treePageData[id_cat]=array with category data
     * @param integer $id_cat - id of the category
     * @param varchar $key - name of new key
     * @param varchar $val - value for key $key
     * @return true
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 17.05.2011
     */
    function SetTreePageDataAddNew($id_cat, $key, $val)
    {
        $this->treePageData[$id_cat][$key]=$val;
        return true;
    } //end of function SettreePageDataAddNew()


    /**
     * Class method GettreePageDataAll
     * get array $this->treePageData
     * @return array $this->treePageData
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 17.05.2011
     */
    function GetTreePageDataAll()
    {
        return $this->treePageData;
    } //end of function GettreePageDataAll()

    /**
     * Class method GettreePageData
     * get node of array $this->treePageData where store array with data about category
     * @param integer $item - id of the category as node in array
     * @return node of array $this->treePageData[$item]
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 17.05.2011
     */
    function GettreePageData($item)
    {
        if(!isset($this->treePageData[$item])) return false;
        return $this->treePageData[$item];
    } //end of function GettreePageData()

    /**
     * Class method isPageASubcatOfLevel
     * Checking if the page $id_page is a subcategory of $item at any dept start from $arr[$item]
     * @param integer $id_page - id of the page
     * @param integer $item - as index for array $arr
     * @return array with index as counter
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.1, 05.04.2012
     */
    function isPageASubcatOfLevel($id_page, $item)
    {
        if($id_page==$item) return true;
        $a_tree = $this->GetTreePageLevel($item);
        if( !$a_tree ) return false;
        $keys = array_keys($a_tree);
        $rows = count($keys);
        if(array_key_exists($id_page, $a_tree)) return true;
        for ($i=0;$i<$rows;$i++) {
            $id = $keys[$i];
            //echo '<br />$id='.$id;
            if( $this->GetTreePageLevel($id) AND is_array($this->GetTreePageLevel($id)) ) {
                $res = $this->isCatASubcatOfLevel($id_page, $id);
                if($res) return true;
            }
        }
        return false;
    } // end of function isPageASubcatOfLevel()

    /**
     * Class method isSubLevels()
     * Checking exist or not sublevels for page $id_page
     * @param integer $id_page - id of the page
     * @return true or false
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.1, 05.04.2012
     */
    function isSubLevels($id_page)
    {
        $array = $this->GetTreePageLevel($id_page);
        if( !$array ) return false;
        return count($array);
    } // end of function isSubLevels()

    /**
     * Class method getSubLevels
     * return string with sublevels for page $id_page
     * @param integer $id_page - id of the page
     * @return sting with id of categories like (1,13,15,164? 222)
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.1, 05.04.2012
     */
    function getSubLevels( $id_page )
    {
        if( !$this->GetTreePageLevel($id_page) ) return false;
        $a_tree = $this->GetTreePageLevel($id_page);
        $keys = array_keys($a_tree);
        $rows = count($keys);
        for ($i=0;$i<$rows;$i++) {
            $id = $keys[$i];
            //echo '<br />$id='.$id;
            if( empty($arr_row)) $arr_row = $id;
            else $arr_row = $arr_row.','.$id;
            if(  $this->GetTreePageLevel($id) AND is_array($this->GetTreePageLevel($id)) ) {
                $arr_row .= ','.$this->getSubLevels($id);
            }
        }
        return $arr_row;
    } // end of function getSubLevels()

    /**
     * Class method getTopLevel
     * get the top level of pages for page $id_page
     * @param integer $id_page - id of the page
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 05.04.2012
     */
    function getTopLevel($id_page)
    {
        $cat_data = $this->GetTreePageData($id_page);
        if(!$cat_data) return false;
        if($cat_data['level']==0) return $id_page;
        return $this->getTopLevel($cat_data['level']);
    } // end of function getTopLevel()


    // ================================================================================================
    // Function : show()
    // Version : 1.0.0
    // Date : 28.01.2005
    // Parms :
    //        $module_id  - id of this module in system
    //        $user_id    - id of current user
    //        $action     - script action
    //        $display    - rows count display on form
    //        $sort=NULL  - sort parameter
    //        $start=NULL - start row for display
    //        $level = 0  - level of menu  (0 - first level)
    // Returns : true,false / Void
    // Description : Write Form Sys Function
    // ================================================================================================
    // Programmer : Oleg Morgalyuk
    // Date : 05.02.2010
    // Reason for change : new object Right using singleton BD
    // Change Request Nbr:
    // ================================================================================================

    function show( $user_id=NULL, $module_id=NULL, $display=NULL, $start=NULL, $sort=NULL, $level=0 )
    {
     $id = AntiHacker::AntiHackRequest('id');
     if( $module_id ) $this->module_id = $module_id;
     if( $user_id ) $this->user_id = $user_id;
     if( $display ) $this->display = $display;
     if( $sort ) $this->sort = $sort;
     if( $start ) $this->start = $start;
     $scriptact = 'module='.$this->module_id;              /* set action page-adress with parameters */
     $scriplink = $_SERVER['PHP_SELF']."?$scriptact&fltr=$this->fltr";

     if( !$sort ) $sort='move';
     //if( !$sort ) $sort='group_menu';
     //$q = "select `".TblSysMenuAdm."`.*,`".TblSysSprMenuAdm."`.name from `".TblSysMenuAdm."`,`".TblSysSprMenuAdm."` where level='$level' and `".TblSysMenuAdm."`.id=`".TblSysSprMenuAdm."`.cod and `".TblSysSprMenuAdm."`.lang_id='"._LANG_ID."'";


     $q = "select `".TblSysMenuAdm."`.*,`".TblSysSprMenuAdm."`.name
            from `".TblSysMenuAdm."` LEFT JOIN `".TblSysSprMenuAdm."`
                on (`".TblSysMenuAdm."`.id=`".TblSysSprMenuAdm."`.cod)
            where level='$level' and  `".TblSysSprMenuAdm."`.lang_id='"._LANG_ID."'";
     if( $this->fltr ) $q = $q." and `group`='$this->fltr'";
     $q = $q." order by $sort";
     //echo $q;
     $result = $this->Right->db_QueryResult( $q, $this->user_id, $this->module_id );
     //echo '<br />$result='.$result;print_r($result);
     //if( !$result )return false;
     $rows = count($result);
     //echo '<br />$rows='.$rows;

     /* Write Form Header */
     $this->Form->WriteHeader( $scriplink."&level=$level".'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort );
     /* Write Table Part */
     /* Write Links on Pages */
     echo '<div>';
     $this->Form->WriteLinkPages( $scriplink."&level=$level", $rows, $this->display, $this->start, $this->sort );
     echo '</div><div class="topPanel"><div class="SavePanel">';

     $scriplink = $scriplink.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort;
     /* Write Top Panel (NEW,DELETE - Buttons) */
     $this->Form->WriteTopPanel( $scriplink."&level=$level".'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort );

     //echo '<TR><TD COLSPAN=7>';
     $top_lev = $this->get_top_level( $level );
     if( $top_lev )
     {
       $top = $this->get_level_name( $top_lev['level'] );
       if( $top_lev['level']!=0 )
       {
         $tmp = $this->Spr->GetNameByCod( TblSysSprMenuAdm, $top['id'], _LANG_ID, 1 );
       }
       else
       {
         $tmp = $this->Msg['_LNK_UP_LEVEL'];
       }
       ?>
        <a class="r-button" href=<?=$_SERVER['PHP_SELF']."?$scriptact"."&task=show&level=".$top_lev['level'].'&fltr='.$this->fltr;?>>
        <span><span><IMG src='images/icons/restore.png' alt="Go to:" align="middle" border="0" name="restore"><?=$tmp?></span></span></a>
       <?
     }

     echo '</div><div class="SelectType">';
     $SysGroup = check_init("SysGroup","SysGroup");
     $arr = $SysGroup->GetGrpToArr( $this->user_id, $this->module_id, NULL );
     $grp = NULL;
     $max = count( $arr );
     for( $i = 0; $i < $max; ++$i )
     {
       $grp[$arr[$i]['id']] = $arr[$i]['name'];
     }
     $this->Form->SelectAct( $grp, 'group', $this->fltr, "onChange=\"location='$scriplink'+'&fltr='+this.value\"" );
    ?>
    </div>
    </div>
    <?
    if( $top_lev ){
        ?><div class="SpecialCaption"><b class="link-pages-sel"><?=$this->Spr->GetNameByCod( TblSysSprMenuAdm, $top_lev['id'] )?></b></div><?
    }
    AdminHTML::TablePartH(); ?>
     <tr>
     <td class="THead"> *</td>
     <td class="THead"> <?=$this->Msg['FLD_ID']?></td>
     <td class="THead"> <?=$this->Msg['FLD_DESCRIPTION']?></td>
     <td class="THead"> <?=$this->Msg['_FLD_FUNCTION']?></td>
     <td class="THead"> <?=$this->Msg['FLD_SUBLEVEL']?></td>
     <td class="THead"> <?=$this->Msg['_FLD_UP']?></td>
     <td class="THead"> <?=$this->Msg['_FLD_DOWN']?></td>
     <td class="THead"> <?=$this->Msg['FLD_GROUP']?></td>
    <?
    $a=$rows;
    $up=0;
    $down=0;
    for( $i = 0; $i < $rows; ++$i )
    {

        $row = &$result[$i];
     if( $i >=$this->start && $i < ( $this->start+$this->display ) )
     {
       if ( (float)$i/2 == round( $i/2 ) )
               echo '<TR CLASS="TR1">';
       else
               echo '<TR CLASS="TR2">';
       $down=$row['id'];
       echo '<TD>';
       $this->Form->CheckBox( "id_del[]", $row['id'] );
       echo '<TD>';
       $this->Form->Link( $scriplink."&task=edit&id=".$row['id']."&level=".$row['level'], stripslashes( $row['id'] ), $this->Msg['TXT_EDIT'] );

       echo '<TD> ';
       $function_id = $row['function'];
       if( $function_id )
        $this->Form->Link( $_SERVER['PHP_SELF']."?module=".$function_id, $row['name'] );
       else
        echo $row['name'] ;
       echo ' </TD><TD>',$this->Spr->GetNameByCod( TblSysSprFunc, $row['function'], _LANG_ID, 1 ),'</TD><TD>';
       $this->Form->Link( $scriplink."&task=show&level=".$row['id'], $this->Msg['FLD_SUBLEVEL'] );
       $arrSubLevels = $this->GetTreePageLevel($row['id']);
       if($arrSubLevels){
           echo ' ['.count($arrSubLevels).']';
       }
       echo '<TD align=center>';
       if( $up!=0 )
       {
       ?>
        <a href=<?=$scriplink?>&level=<?=$level?>&task=up&move=<?=$row['move']?>>
        <?=$this->Form->ButtonUp( $row['id'] );?>
        </a>
       <?
       }
       echo '<TD>';
       if( $i!=($rows-1) )
       {
       ?>
         <a href=<?=$scriplink?>&level=<?=$level?>&task=down&move=<?=$row['move']?>>
         <?=$this->Form->ButtonDown( $row['id'] );?>
         </a>
       <?
       }
       $up=$row['id'];
    //   echo '<td>';
    //   $arr = SysGroup::GetGrpToArr( $this->user_id, $this->module_id,  $row['group'] );
    //   echo $arr[0]['name'];
       echo '<td>',$grp[$row['group']],'</TR>';
       $a=$a-1;
      }
    }
    AdminHTML::TablePartF();
    $this->Form->WriteFooter();
    }



    // ================================================================================================
    // Function : edit()
    // Version : 1.0.0
    // Date : 28.01.2005
    //
    // Parms :   $user_id=NULL, $module_id=NULL, $id=NULL, $mas=NULL, $level=0
    // Returns : true,false / Void
    // Description : Show data from $spr table for editing
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 31.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================

    function edit( $user_id=NULL, $module_id=NULL, $id=NULL, $level=0, $mas=NULL )
    {
     if( $module_id ) $this->module_id = $module_id;
     if( $user_id ) $this->user_id = $user_id;

     $Panel = new Panel();
     $ln_sys = check_init('LangSys','SysLang');
    // $ln_sys = check_init('LangSys','SysLang');

     $fl = NULL;
     if( $mas )
     {
       $fl = 1;
     }

     /* set action page-adress with parameters */
     $scriptact = $_SERVER['PHP_SELF'].'?module='.$this->module_id.'&display='.$_REQUEST['display'].'&start='.$_REQUEST['start'].'&sort'.$_REQUEST['sort'];

     if( $id!=NULL and ( $mas==NULL ) )
     {
      $q="select `".TblSysMenuAdm."`.*,`".TblSysSprMenuAdm."`.name from `".TblSysMenuAdm."` LEFT JOIN `".TblSysSprMenuAdm."` ON
       (`".TblSysSprMenuAdm."`.cod = `".TblSysMenuAdm."`.level AND `".TblSysSprMenuAdm."`.lang_id = '"._LANG_ID."' )
        where `".TblSysMenuAdm."`.id='$id'";
    //  echo $q;
    //  exit();
      $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
      if( !$res )return false;
      $mas = $this->Right->db_FetchAssoc();
     }
     /* Write Form Header */
     $this->Form->WriteHeader( $scriptact );
    ?>
    <?
       if( $id!=NULL ) $txt = $this->Msg['TXT_EDIT'];
       else $txt = $this->Msg['_TXT_ADD_DATA'];
       AdminHTML::PanelSubH( $txt );
       AdminHTML::PanelSimpleH();
    ?>
     <TR><TD><b><?echo $this->Msg['FLD_ID'];?></b>
     <TD>
    <?
     if( $id!=NULL )
     {
       echo $mas['id'];
       $this->Form->Hidden( 'id', $mas['id'] );
     }else $this->Form->Hidden( 'id', '' );
     $this->Form->Hidden( 'level', $level );
     $level_name = $this->get_level_name( $mas['level']);

     echo '<TR><TD><b>',$this->Msg['FLD_GROUP'],'</b><TD>';

     if( $id!=NULL or ( $mas!=NULL ) )
     {
      $SysGroup = check_init("SysGroup","SysGroup");
      $arr = $SysGroup->GetGrpToArr( $this->user_id, $this->module_id,  $mas['group'] );
      echo $arr[0]['id'],' - ',$arr[0]['name'];
      $this->Form->Hidden( 'group', $mas['group'] );
      $this->Form->Hidden( 'fltr', $mas['group'] );
      $this->fltr = $mas['group'];
     }else
     {
      $SysGroup = check_init("SysGroup","SysGroup");
      $arr = $SysGroup->GetGrpToArr( $this->user_id, $this->module_id,  $this->fltr );
      echo $arr[0]['id'],' - ',$arr[0]['name'];
      $this->Form->Hidden( 'group', $this->fltr );
      $this->Form->Hidden( 'fltr', $this->fltr );
     }
     if($mas['level']>0){
    ?>
     <TR><TD><b><?echo $this->Msg['_FLD_LEVEL']?></b>
         <TD><b><?=$mas['level'].' - '.$mas['name'];?></b>
    <?
     }


     ?>
<tr>
    <td colspan="3">
        <?
        $arr = $this->GetPagesInArray(0, $this->Msg['TXT_ROOT_CATEGORY']);
        if(isset($mas['level'])) $tmp_lev = $mas['level'];
        else $tmp_lev = $level;
        if( $fl ){
            ?><b><?=$this->Msg['_FLD_ADD_TO_LEVEL'];?>:</b><?
            $params = '';
        }
        else{
            ?><b><?=$this->Msg['_FLD_PAGE_IN_LEVEL'];?>:</b><?
            $params = 'disabled';
            //$this->Form->Hidden( 'level', $tmp_lev );
        }
        $this->Form->Select( $arr, 'level', $tmp_lev, NULL, 'id="idlevelp" ' );
        ?>
    </td>
</tr>

     <tr><td colspan=2>
    <?
     $Panel->WritePanelHead( "SubPanel_" );
     $ln_arr = $ln_sys->LangArray( _LANG_ID );
      while( $el = each( $ln_arr ) )
     {
          $lang_id = $el['key'];
          $lang = $el['value'];
          $mas_s[$lang_id] = $lang;

          $Panel->WriteItemHeader( $lang );
          echo "\n <table border=0 class='EditTable'> \n <tr><td><b>",$this->Msg['FLD_DESCRIPTION'],":</b>\n <td>";
          $row = $this->Spr->GetByCod( TblSysSprMenuAdm, $mas['id'], $lang_id );
          if( $fl )
            $this->Form->TextBox( 'description['.$lang_id.']', $mas['description'][$lang_id], 80 );
          else
            $this->Form->TextBox( 'description['.$lang_id.']', $row[$lang_id], 80 );
          echo "\n <td rowspan=3>\n </table>";
          $Panel->WriteItemFooter();
     }
     $Panel->WritePanelFooter();

    ?>

     <TR><TD><b><?echo $this->Msg['_FLD_FUNCTION']?></b>
         <TD>
    <?
     $arr = NULL;
     $arr['']='';
     $tmp_db = new Rights($this->user_id, $this->module_id);
     $tmp_q = "select `".TblSysFunc."`.id, `".TblSysFunc."`.name, `".TblSysSprFunc."`.name as tdesc
     from `".TblSysAccess."`, `".TblSysFunc."` LEFT JOIN `".TblSysSprFunc."` ON
       (`".TblSysSprFunc."`.cod = `".TblSysFunc."`.id AND `".TblSysSprFunc."`.lang_id = '"._LANG_ID."' )
               where `".TblSysAccess."`.group = $this->fltr
                 and `".TblSysAccess."`.function = `".TblSysFunc."`.id Order BY tdesc";
    //echo $tmp_q;
     $tmp_db->Query( $tmp_q, $this->user_id, $this->module_id );
     $tmp_rows = $tmp_db->db_GetNumRows();
     for( $i=0; $i<$tmp_rows; $i++ )
     {
      $tmp_row = $tmp_db->db_FetchAssoc();
    //  $arr[$tmp_row['id']] = $this->Spr->GetNameByCod( TblSysSprFunc, $tmp_row['id'] ).'  ('.$tmp_row['name'].')';
      $arr[$tmp_row['id']] = $tmp_row['tdesc'].'  ('.$tmp_row['name'].')';
     }
     $this->Form->Select( $arr, $name = 'function', $mas['function'] );

     if( $id!=NULL or ( $mas!=NULL ) )
     {
      $this->Form->Hidden( 'move', $mas['move'] );
     }
     else
     {
      $tmp_q = "select * from ".TblSysMenuAdm." order by move desc";
      $res = $tmp_db->Query( $tmp_q, $this->user_id, $this->module_id );
      if( !$res )return false;
      $tmp_row = $tmp_db->db_FetchAssoc();
      $move = $tmp_row['move'];
      $this->Form->Hidden( 'move', ($move+1) );
     }
     AdminHTML::PanelSimpleF();
     $this->Form->WriteSavePanel( $scriptact );
     AdminHTML::PanelSubF();
     $this->Form->WriteFooter();
    return true;
    }


    // ================================================================================================
    // Function : GetPagesInArray()
    // Date : 04.04.2006
    // Returns : true,false / Void
    // Description : Show structure of pages in Combo box
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetPagesInArray($level = NULL, $default_val = NULL, $mas = NULL, $spacer = NULL, $show_content = 1, $front_back = 'back', $show_sublevels = 1)
    {
        $arr_data = $this->GetTreePageLevel($level);
        //return $arr_data;
        //var_dump($arr_data);
        $mas[0] = $default_val;
        //for( $i = 0; $i < $rows; $i++ )
                foreach($arr_data as $sublevel=>$name){
                    //echo '<br>$sublevel='.$sublevel.' $name='.$name;
                    $mas[''.$sublevel] = $spacer.'- '.stripslashes($name);
                    //----------------- show subcategory ----------------------------
                    if( $show_sublevels==1 ){
                        if ($this->isSubLevels($sublevel)>0) $mas = $mas + $this->GetPagesInArray($sublevel, $default_val, $mas, $spacer.'&nbsp;&nbsp;&nbsp;', $show_content, $front_back, $show_sublevels);
                    }
                    //------------------------------------------------------------------
                }
        //var_dump($mas);
        return $mas;
    } // end of function GetPagesInArray()

    // ================================================================================================
    // Function : save()
    // Version : 1.0.0
    // Date : 31.01.2005
    //
    // Parms :   $user_id, $module_id, $id, $group_menu, $level, $description, $function, $move
    // Returns : true,false / Void
    // Description : Store data to the table TblSysMenuAdm
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 31.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================

    function save( $user_id, $module_id, $id, $group, $level, $description, $function, $move )
    {
     if( $module_id ) $this->module_id = $module_id;
     if( $user_id ) $this->user_id = $user_id;

     if ( empty($description[_LANG_ID]) ) {
        $this->Msg['_EMPTY_DESCRIPTION_FIELD'];
        $this->edit( $user_id, $module_id, $id, $level, $_REQUEST );
        return false;
     }
     $ln_sys = check_init('LangSys','SysLang');

     $q="select `id` from ".TblSysMenuAdm." where id='$id'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
     if( !$res ) return false;
     $rows = $this->Right->db_GetNumRows();
     if($rows>0)
     {
       $q="update ".TblSysMenuAdm." set
          `group`='$group', level='$level', function='$function', move='$move'";
          $q=$q." where id='$id'";
      $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
      //if( $res ) return true;
      //else return false;
     }
     else
     {
      $q = "select `id` from ".TblSysMenuAdm." order by id desc";
      $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
      $row = $this->Right->db_FetchAssoc();
      $id = $row['id']+1;

      $q = "insert into ".TblSysMenuAdm." values('$id','$group','$level','$function', '$move')";
      $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
      if( !$res ) return false;
     }

     // Save Description on different languages
     $ln_arr = $ln_sys->LangArray( _LANG_ID );
     while( $el = each( $ln_arr ) )
     {
          $description1 = addslashes($description[ $el['key'] ]);
          //if (empty($description1)) continue;
          $lang_id = $el['key'];
          $res = $this->Spr->SaveToSpr( TblSysSprMenuAdm, $id, $lang_id, $description1 );
          if( !$res ) return false;
     } //--- end while

     return true;
    }


    // ================================================================================================
    // Function : del()
    // Version : 1.0.0
    // Date : 28.01.2005
    // Parms :   $user_id, $module_id, $id_del
    // Returns : true,false / Void
    // Description :  Remove data from the table
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 28.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================

    function del( $user_id, $module_id, $id_del )
    {
     if( $module_id ) $this->module_id = $module_id;
     if( $user_id ) $this->user_id = $user_id;

        $del = 0;
        $kol = count( $id_del );
        for( $i=0; $i<$kol; $i++ )
        {
         $u=$id_del[$i];

         $q="select * from ".TblSysMenuAdm." where level='$u'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
         $rows = $this->Right->db_GetNumRows();
         for( $i_ = 0; $i_ < $rows; $i_++ )
         {
          $row = $this->Right->db_FetchAssoc();
          $id_del_l[$i_] = $row['id'];
         }
         if( $rows>0 )$this->del( $user_id, $module_id, $id_del_l );

          $q = "delete from ".TblSysMenuAdm." where id='$u'";
          $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
          $res = $this->Spr->DelFromSpr( TblSysSprMenuAdm, $u );
          if( !$res )return false;
          if ( $res )
           $del=$del+1;
          else
           return false;
        }
      return $del;
    }


    // ================================================================================================
    // Function : up_menu()
    // Version : 1.0.0
    // Date : 31.01.2005
    // Parms :
    //           $user_id=NULL
    //           $module_id=NULL
    //           $display=NULL
    //           $start=NULL
    //           $sort=NULL
    //           $level = 0  - level of menu  (0 - first level)
    //           $move  -  move field..
    // Returns : true,false / Void
    // Description : Get Top Level of menu
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 31.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================

    function up_menu( $user_id=NULL, $module_id=NULL, $display=NULL, $start=NULL, $sort=NULL, $level, $move )
    {
     if( $module_id ) $this->module_id = $module_id;
     if( $user_id ) $this->user_id = $user_id;
     if( $display ) $this->display = $display;
     if( $sort ) $this->sort = $sort;
     if( $start ) $this->start = $start;

     $scriptact = 'module='.$this->module_id;              /* set action page-adress with parameters */
     $scriplink = $_SERVER['PHP_SELF']."?$scriptact";

     $q="select * from ".TblSysMenuAdm." where level='$level' AND move='$move'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
     if( !$res )return false;

     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_down = $row['move'];
     $id_down = $row['id'];

     $q="select * from ".TblSysMenuAdm." where level='$level' AND move<'$move' order by move desc";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
     if( !$res )return false;

     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_up = $row['move'];
     $id_up = $row['id'];

     $q="update ".TblSysMenuAdm." set
         move='$move_down' where id='$id_up'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );

     $q="update ".TblSysMenuAdm." set
         move='$move_up' where id='$id_down'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );

    }


    // ================================================================================================
    // Function : down_menu()
    // Version : 1.0.0
    // Date : 31.01.2005
    // Parms :
    //           $user_id=NULL
    //           $module_id=NULL
    //           $display=NULL
    //           $start=NULL
    //           $sort=NULL
    //           $level = 0  - level of menu  (0 - first level)
    //           $move  -  move field..
    // Returns : true,false / Void
    // Description : Down  menu item
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 31.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================

    function down_menu( $user_id=NULL, $module_id=NULL, $display=NULL, $start=NULL, $sort=NULL, $level, $move )
    {
     if( $module_id ) $this->module_id = $module_id;
     if( $user_id ) $this->user_id = $user_id;
     if( $display ) $this->display = $display;
     if( $sort ) $this->sort = $sort;
     if( $start ) $this->start = $start;

     $q="select * from ".TblSysMenuAdm." where level='$level' AND move='$move'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
     if( !$res )return false;

     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_up = $row['move'];
     $id_up = $row['id'];


     $q="select * from ".TblSysMenuAdm." where level='$level' AND move>'$move' order by move";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
     if( !$res )return false;

     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     $move_down = $row['move'];
     $id_down = $row['id'];

     $q="update ".TblSysMenuAdm." set
         move='$move_down' where id='$id_up'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );

     $q="update ".TblSysMenuAdm." set
         move='$move_up' where id='$id_down'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );
    }


    // ================================================================================================
    // Function : get_top_level()
    // Version : 1.0.0
    // Date : 30.01.2005
    // Parms :
    //           $level = 0  - level of menu  (0 - first level)
    // Returns : true,false / Void
    // Description : Get Top Level of menu
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 30.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================

    function get_top_level( $level )
    {
     $q = "select * from ".TblSysMenuAdm." where id='$level'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );

     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     return $row;
    }

    // ================================================================================================
    // Function : get_level_name()
    // Version : 1.0.0
    // Date : 30.01.2005
    // Parms :
    //           $id = 0  - level of menu  (0 - first level)
    // Returns : true,false / Void
    // Description : Get Level Name
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 30.01.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================

    function get_level_name( $id )
    {
     //$db = new Rights($this->user_id, $this->module_id);
     $q = "select * from ".TblSysMenuAdm." where id='$id'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module_id );

     if( !$res )return false;
     $rows = $this->Right->db_GetNumRows();
     $row = $this->Right->db_FetchAssoc();
     return $row;
    }


} // end of class
?>
