<?php

/**
 * Class informer
 * author bogdan iglinsky
 */
class informer {

    /**
     *
     * author bogdan iglinsky
     */
    function __construct(){
        $this->lang_id = _LANG_ID;
    }

    /**
     * @return array|bool
     * author bogdan iglinsky
     */
    function getMainSlider(){

        $q = "SELECT *
        FROM `".TblModInformerMainSlider."`
        WHERE
            `".TblModInformerMainSlider."`.visible = '1'
            AND `".TblModInformerMainSlider."`.`lang_id`='".$this->lang_id."'
        ORDER BY  `".TblModInformerMainSlider."`.`move` ASC";

        $res = $this->db->db_Query($q);
//        echo $q.' <br/>$res = '.$res.' $this->db->result='.$this->db->result;
        if(!$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNUmRows($res);
        if($rows==0) return false;
        $arr = array();
        for($i = 0; $i < $rows; $i++){
            $row = $this->db->db_FetchAssoc($res);
            if(!empty($row['img'])){
                $path = '/images/spr/'.TblModInformerMainSlider.'/'.$this->lang_id.'/'.$row['img'];
                $row['path'] = ImageK::getResizedImg($path,'size_rect=1920x600',85);
//                $row['path'] = $path;
                $arr[] = $row;
            }
        }
        return $arr;
    }

    /**
     * @return array|bool
     * author bogdan iglinsky
     */
    function getSocial(){
        $q = "SELECT *
        FROM `".TblModInformerSocial."`
        WHERE
            `".TblModInformerSocial."`.visible = '1'
        ORDER BY  `".TblModInformerSocial."`.`move` ASC";

        $res = $this->db->db_Query($q);
//        echo $q.' <br/>$res = '.$res.' $this->db->result='.$this->db->result;
        if(!$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNUmRows($res);
        if($rows==0) return false;

        for($i = 0; $i < $rows; $i++){
            $row = $this->db->db_FetchAssoc($res);
            $arr_row[$row['cod']][$row['lang_id']] = $row;
        }
        $arr = array();
        foreach($arr_row as $row)
        {
            if(!isset($row[$this->lang_id]['name']) || empty($row[$this->lang_id]['name'])) continue;
            $rowShow['name'] = $row[$this->lang_id]['name'];
            $rowShow['target'] = $row[$this->lang_id]['target'];
            if(!empty($row[$this->lang_id]['img'])){
                $path = '/images/spr/'.TblModInformerSocial.'/'.$this->lang_id.'/'.$row[$this->lang_id]['img'];
            }else{
                $path = '';
                foreach($row as $lang_id=>$row_lang_id){
                    if($row_lang_id['img'] && empty($path)){
                        $path = '/images/spr/'.TblModInformerSocial.'/'.$lang_id.'/'.$row_lang_id['img'];
                    }
                }
                if(empty($path)) continue;
            }
            $rowShow['path'] = ImageK::getResizedImg($path,'size_auto=46',85);
            if(!empty($row[$this->lang_id]['href'])){
                $rowShow['href'] = $row[$this->lang_id]['href'];
            }else{
                $rowShow['href'] = '';
                foreach($row as $row_lang_id){
                    if($row_lang_id['href'] && empty($rowShow['href'])){
                        $rowShow['href'] = $row_lang_id['href'];
                    }
                }
            }
            $arr[] = $rowShow;
        }
       return $arr;
    }


}