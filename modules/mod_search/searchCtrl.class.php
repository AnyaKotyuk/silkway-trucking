<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 27.05.14
 * Time: 12:38
 */

class SearchCtrl extends Search{
    /**
     * @param null $user_id
     * @param null $module
     * @param null $display
     * @param null $sort
     * @param null $start
     * @param null $width
     */
    function __construct($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
        //Check if Constants are overrulled
        ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
        ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
        ( $display  !="" ? $this->display = 60  : $this->display = 60   );
        ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
        ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
        ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

        $this->db =  DBs::getInstance();
        $this->Right =  check_init('RightsNews', 'Rights', "'".$this->user_id."','".$this->module."'");
        $this->Form = check_init('FormNews', 'FrontForm', "'form_mod_links_set'");
        if (empty($this->Spr)) $this->Spr = check_init('SysSpr', 'SysSpr');
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->multi = check_init_txt('TblBackMulti',TblBackMulti);
    }

    /**
     * @author Bogdan Iglinsky  <bi@seotm.com>
     * @return bool
     */
    function show(){
        $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr.'&fln='.$this->fln;
        $script = $_SERVER['PHP_SELF']."?$script";

        if( !$this->sort ) $this->sort='id';
        if($this->sort=='result') $this->sort='`result` desc';
        //if( strstr( $this->sort, 'seria' ) )$this->sort = $this->sort.' desc';
        $q = "SELECT * FROM ".TblModSearchResult." where 1 order by ".$this->sort."";
        //if( $this->srch ) $q = $q." and (name LIKE '%$this->srch%' OR email LIKE '%$this->srch%')";
        if( $this->fltr ) $q = $q." and $this->fltr";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo '<br>$q='.$q.' $res='.$res.'$this->Right->result='.$this->Right->result.' $this->user_id='.$this->user_id;
        if( !$res )return false;
        $rows = $this->Right->db_GetNumRows();

        /* Write Form Header */
        $this->Form->WriteHeader( $script );

        /* Write Table Part */
        AdminHTML::TablePartH();

        /* Write Links on Pages */
        echo '<TR><TD COLSPAN=17>';
        $script1 = 'module='.$this->module.'&fltr='.$this->fltr;
        $script1 = $_SERVER['PHP_SELF']."?$script1";
        if( !$this->display ) $this->display = 20;
        //$this->Form->WriteLinkPages( $script1, $rows, $this->display, $this->start, $this->sort );
        $this->Form->WriteLinkPages( $script1.'&fltr='.$this->fltr, $rows, $this->display, $this->start, $this->sort );

        echo '<TR><TD COLSPAN=5>';
        $this->Form->WriteTopPanel( $script );

//        echo '<td colspan=5>';
//        echo $this->Form->TextBox('srch', $this->srch, 25);
//        echo '<input type=submit value='.$this->multi['BUTTON_SEARCH'].'>';

        /*
        echo '<td><td><td><td><td colspan=2>';
        $this->Form->WriteSelectLangChange( $script, $this->fln);
        */

        $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr;
        $script2 = $_SERVER['PHP_SELF']."?$script2";
        ?>
        <TR>
        <td class="THead">*</Th>
        <td class="THead"><A HREF=<?=$script2?>&sort=id><?=$this->multi['FLD_ID']?></A></Th>
        <td class="THead"><A HREF=<?=$script2?>&sort=query><?=$this->multi['FLD_QUERY']?></A></Th>
        <td class="THead"><A HREF=<?=$script2?>&sort=ip><?=$this->multi['FLD_IP']?></A></Th>
        <td class="THead"><A HREF=<?=$script2?>&sort=date><?=$this->multi['FLD_DATE']?></A></Th>

        <td class="THead"><A HREF=<?=$script2?>&sort=result><?=$this->multi['FLD_RESULT']?></A></Th>

        <?

        $up = 0;
        $down = 0;
        $a = $rows;
        $j = 0;
        $row_arr = NULL;
        for( $i = 0; $i < $rows; $i++ )
        {
            $row = $this->Right->db_FetchAssoc();
            if( $i >= $this->start && $i < ( $this->start+$this->display ) )
            {
                $row_arr[$j] = $row;
                $j = $j + 1;
            }
        }

        $style1 = 'TR1';
        $style2 = 'TR2';
        for( $i = 0; $i < count( $row_arr ); $i++ )
        {
            $row = $row_arr[$i];

            if ( (float)$i/2 == round( $i/2 ) )
            {
                echo '<TR CLASS="'.$style1.'">';
            }
            else echo '<TR CLASS="'.$style2.'">';

            echo '<TD>';
            $this->Form->CheckBox( "id_del[]", $row['id'] );

            echo '<TD>';
            $this->Form->Link( $script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ) );

            echo '<TD align=center>';
            if( trim( $row['query'] )!='' ) echo $row['query'];

            echo '<TD align=center>';
            if( trim( $row['ip'] )!='' ) echo $row['ip'];

            echo '<TD align=center>';
            if( trim( $row['date'] )!='' ) echo $row['date'];


            echo '<TD align=center>';
            if( trim($row['cnt'])!='' ) echo $row['cnt'];

        } //-- end for

        AdminHTML::TablePartF();
        $this->Form->WriteFooter();
        return true;
    } //end of fuinction show

    /**
     * @author Bogdan Iglinsky  <bi@seotm.com>
     * @return bool
     */
    function showCount(){
        $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr.'&fln='.$this->fln;
        $script = $_SERVER['PHP_SELF']."?$script";

        if( !$this->sort ) $this->sort='id';
        if($this->sort=='result') $this->sort='`result` desc';
        //if( strstr( $this->sort, 'seria' ) )$this->sort = $this->sort.' desc';
        $q = "SELECT * FROM ".TblModSearchResult." where 1 order by ".$this->sort."";
//        if( $this->srch ) $q = $q." and (name LIKE '%$this->srch%' OR email LIKE '%$this->srch%')";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
//        echo '<br>$q='.$q.' $res='.$res.'$this->Right->result='.$this->Right->result.' $this->user_id='.$this->user_id;
        if( !$res )return false;
        $rows = $this->Right->db_GetNumRows();


        $j = 0;
        $row_arr = NULL;
        for( $i = 0; $i < $rows; $i++ )
        {
            $row_arr[] = $this->Right->db_FetchAssoc();
        }
        $arrShow = array();
        foreach($row_arr as $row){
            if(empty($row['query'])) continue;
            if(isset($arrShow[$row['query']][$row['ip']])){
                $arrShow[$row['query']][$row['ip']]++;
            }else{
                $arrShow[$row['query']][$row['ip']] = 1;
            }
        }
        /* Write Form Header */
        $this->Form->WriteHeader( $script );

        /* Write Table Part */
        AdminHTML::TablePartH();

        /* Write Links on Pages */
        echo '<TR><TD COLSPAN=17>';
        $script1 = 'module='.$this->module.'&fltr='.$this->fltr;
        $script1 = $_SERVER['PHP_SELF']."?$script1";
        if( !$this->display ) $this->display = 20;
        $rows = count($arrShow);
        $this->Form->WriteLinkPages( $script1.'&fltr='.$this->fltr, $rows, $this->display, $this->start, $this->sort );
        $arrTmpShow = $arrShow;
        $arrShow = array();
        $i=0;
        $end = $this->start+$this->display;
        foreach($arrTmpShow as $key => $val){
            if( $i >= $this->start && $i < $end ){
                $arrShow[$key] = $val;
            }
            $i++;
        }
        echo '<TR><TD COLSPAN=5>';
        $this->Form->WriteTopPanel( $script );

        $script2 = 'module='.$this->module.'&display=20&start=0&fltr='.$this->fltr;
        $script2 = $_SERVER['PHP_SELF']."?$script2";
        ?>
        <TR>
        <td class="THead">№</Th>
        <td class="THead"><?=$this->multi['FLD_QUERY']?></Th>
        <td class="THead"><?=$this->multi['FLD_IP']?></Th>
        <td class="THead"><?=$this->multi['_FLD_USED_COUNTER']?></Th>

        <?

        $style1 = 'TR1';
        $style2 = 'TR2';
        $i = 0;
        foreach($arrShow as $key => $row)
        {
//            var_dump($row);
            $i++;
            if ( (float)$i/2 == round( $i/2 ) )
            {
                echo '<TR CLASS="'.$style1.'">';
            }
            else echo '<TR CLASS="'.$style2.'">';

            echo '<TD align=center>';
            echo $i;

            echo '<TD align=center>';
            echo $key;
            $cnt = 0;
            echo '<TD align=center>';
            if( !empty($row) ){
                foreach($row as $k => $v):
                    $cnt += $v;
                    echo $k.' => '.$v.'<br>';
                endforeach;
            }

            echo '<TD align=center>';
            echo $cnt;

        } //-- end for

        AdminHTML::TablePartF();
        $this->Form->WriteFooter();
        return true;
    }

    /**
     * @author Bogdan Iglinsky  <bi@seotm.com>
     * @param $id_del
     * @return bool|int
     */
    function del( $id_del ){
        $kol = count( $id_del );
        $del = 0;
        for( $i=0; $i<$kol; $i++ )
        {
            $u = $id_del[$i];

            $q = "DELETE FROM `".TblModSearchResult."` WHERE id='$u'";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );

            if ( $res )
                $del=$del+1;
            else
                return false;
        }
        return $del;
    } //end of fuinction del()
}