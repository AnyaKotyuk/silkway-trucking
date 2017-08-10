<?
/**
 * Dictionary
 *
 * @package
 * @author Yaroslav
 * @copyright 2011
 * @version 01
 * @access public
 */
class Dictionary{
    var $alphabet = array(
                        "А","Б","В","Г","Д","Е","Є","Ж","З","И","І","Ї","Й","К","Л","М","Н","О",
                        "П","Р","С","Т","У","Ф","Х","Ц","Ч","Ш","Щ","Ъ","Ы","Ь","Э","Ю","Я",
                        "A","B","C","D","E","F","G","H","I","J","K","L","M",
                        "N","O","P","Q","R","S","T","U","V","W","X","Y","Z"
    );

    /**
     * Dictionary::__construct()
     *
     * @return void
     */
    function __construct(){
        if (empty($this->db)) $this->db =  DBs::getInstance();
        if(empty($this->multi)) $this->multi = check_init_txt('TblFrontMulti', TblFrontMulti);
        if (empty($this->Crypt)) $this->Crypt = check_init('Crypt', 'Crypt');
        $this->lang_id =  _LANG_ID;
    }

    /**
     * Dictionary::SetMetaData()
     *
     * @param mixed $page
     * @return
     */
    function SetMetaData($page)
    {
            if(!isset ($this->FrontendPages))
                $this->FrontendPages = Singleton::getInstance('FrontendPages');
            $this->FrontendPages->page_txt = $this->FrontendPages->GetPageTxt($page);

            $this->title = $this->FrontendPages->GetTitle();
            $this->description = $this->FrontendPages->GetDescription();
            $this->keywords = $this->FrontendPages->GetKeywords();
    }



    /**
     * Dictionary::ShowWords()
     *
     * @param mixed $cur_lit
     * @return
     */
    function ShowWords($cur_lit=NULL){
        $words = $this->GetData();
        //else $words = $this->GetData($cur_lit);
        if(empty($words)){
            ?><div class="err"><?=$this->multi['DICT_EMPTY'];?></div><?
        }
        else{
            $first = $this->ShowListOfFirst($words,$cur_lit);
            if($cur_lit==NULL)
                $words = $this->GetData($first);
            else
                $words = $this->GetData($cur_lit);
            $n = count($words);
            for($i=0; $i<$n; $i++){
                $name = stripslashes($words[$i]['name']);
                $short = strip_tags(stripslashes($words[$i]['descr']), '<a>');
                $short  = $this->Crypt->TruncateStr($short, 530);
                $link = $this->Link($words[$i]['cod']);
                //$link = "/dictionary/termin".$words[$i]['cod'];
                ?>
                <div class="news-spacer15">
                    <div class="dictionary-item"><?=$name;?></div><div class="clear"></div>
                    <?/*<div id="cur_lit"><?if($cur_lit!=NULL){echo $cur_lit;}else{echo $first;}?></div>*/?>
                    <div class="newsShort">
                        <?=$short;?><br/>
                        <a class="detail" href="<?=$link;?>"><?=$this->multi['MOD_NEWS_READ_MORE'];?></a>
                    </div>
                </div>
                <?
            }
        }
        //print_r($words);
    }

    /**
     * Dictionary::ShowTermin()
     *
     * @param mixed $cod
     * @return
     */
    function ShowTermin($cod){
        $q = "SELECT *
              FROM `".TblDict."`
              WHERE `lang_id`='".$this->lang_id."'
              AND `cod` ='".$cod."' ";
        $res = $this->db->db_Query( $q );
        if(!$res)
            return false;
        //echo 'q='.$q.'res='.$res;
        $row = $this->db->db_FetchAssoc();
        ?>
        <div id="termin">
            <span class="news-title-detail"><?=$row['name'];?></span>
            <?=$row['descr'];?>
        </div>
        <?

    }


    /**
     * Dictionary::ShowRandomTermin()
     *
     * @return
     */
    function ShowRandomTermin(){
        /*$q = "SELECT COUNT(*) as count FROM `".TblDict."`";
        $res = $this->db->db_Query( $q );
        $row = $this->db->db_FetchAssoc();
        $count = $row['count'];
        $rand_row = rand(0, $count);
        $q = "SELECT * FROM `".TblDict."` LIMIT $rand_row, 1";
        $res = $this->db->db_Query( $q );
        $row = $this->db->db_FetchAssoc();*/

        $q = "SELECT *
              FROM `".TblDict."`
              WHERE `lang_id`='".$this->lang_id."'
              AND `name` !=''
              ORDER BY RAND()
              LIMIT 1;
              ";
        $res = $this->db->db_Query( $q );
        if(!$res)
            return false;
        //echo $q.'<br/>res='.$res;
        $row = $this->db->db_FetchAssoc();
        $rows = $this->db->db_GetNumRows();
        ?><div class="random-termin"><?
        if($rows>0) {
            $name = stripslashes($row['name']);
            $short = strip_tags(stripslashes($row['descr']), '<a>');
            $short  = $this->Crypt->TruncateStr($short, 230);
            $link = $this->Link($row['cod']);
            //$link = "/dictionary/termin".$row['cod'];
            ?>
                <div class="captionTermin"><span><?=$this->multi['TXT_DICTIONARY_TITLE'];?></span>
                    <span class="icoTermin">&nbsp;</span>
                </div>
                <div class="termin-item"><a href="<?=$link;?>"><?=$name;?></a></div>
                <div class="short">- <?=$short;?></div>
            <?
        }
        ?></div><?
    }

    /**
     * Dictionary::ShowRandomWord()
     *
     * @return
     */
    /*function ShowRandomWord(){
        $q = "SELECT *
              FROM `".TblDict."`
              WHERE `lang_id`='".$this->lang_id."'
              ORDER BY RAND() LIMIT 1 ";
        $res = $this->db->db_Query( $q );
        //echo 'q='.$q.'res='.$res;
        $row = $this->db->db_FetchAssoc();
        $mas=explode(" ", strip_tags($row['descr']));
        if(count($mas)>30) $word_count = 30;
        else $word_count = count($mas);
        ?>
        <div id="dict_rand">
            <div class="title"><?=$this->multi['TXT_DICTIONARY'];?></div>
            <div class="text">
                <b><?=strip_tags($row['name']);?></b> -
                <?for($i=0;$i<$word_count;$i++){
                    echo $mas[$i].' ';
                }?>
                ...
            </div>
            <div class="more"><a href="/dictionary/termin<?=$row['cod'];?>"><?=$this->multi['GO_TO_DICTIONARY'];?>→</a></div>
        </div>
        <?
    }*/


    /**
     * Dictionary::Link()
     *
     * @param mixed $cod
     * @return string $link;
     */
    function Link ($cod =null) {
        $link = _LINK."dictionary/termin".$cod;
        return $link;
    }


    /**
     * Dictionary::GetData()
     *
     * @param mixed $firstWord
     * @return
     */
    function GetData($firstWord=NULL){
        $q = "SELECT *
              FROM `".TblDict."`
              WHERE `lang_id`='".$this->lang_id."'
              AND `name` !='' ";
        if($firstWord != NULL)
            $q.= " AND `name` like ucase('".$firstWord."%')";
        $q.=" ORDER BY `name` ASC ";
        $res = $this->db->db_Query( $q );
        //echo $q.'<br/>$res='.$res;
        $rows = $this->db->db_GetNumRows();

        $arr = array();
        for( $i = 0; $i < $rows; $i++ ){
            $row = $this->db->db_FetchAssoc();
            $arr[$i] = $row;
        }

        return $arr;
    }

    /**
     * Dictionary::MAP()
     *
     * @return void
     */
    function MAP () {
        $words = $this->GetData();
        $n = count($words);
        ?><ul><?
        for($i=0; $i<$n; $i++){
            $name = stripslashes($words[$i]['name']);
            $link = $this->Link($words[$i]['cod']);
            ?><li><a href="<?=$link?>"><?=$name;?></a></li><?
        }
        ?></ul><?
    }

    /**
     * Dictionary::ShowListOfFirst()
     *
     * @param mixed $w_arr
     * @param mixed $cur_lit
     * @return
     */
    function ShowListOfFirst($w_arr,$cur_lit=NULL){
        $first=null;
        ?><div id="litList"><?
        $n = count($this->alphabet);
        for($i=0; $i<$n; $i++){
            for($j=0;$j<count($w_arr);$j++){
                $pos = strripos($w_arr[$j]['name'],$this->alphabet[$i]);
                if( $pos === 0){
                    if((empty($first) && $cur_lit==null) || $this->alphabet[$i]==$cur_lit){
                        ?><div class="icoLetterActive"><?echo $this->alphabet[$i];?></div><?
                    }
                    else{
                        ?><div class="item"><a href="<?=_LINK;?>dictionary/letter<?=$i;?>"><?=$this->alphabet[$i];?></a></div><?
                    }

                    if(empty($first))
                        $first = $this->alphabet[$i];
                    break;
                }
            }
        }
        ?></div><?
        return $first;
    }


}
?>