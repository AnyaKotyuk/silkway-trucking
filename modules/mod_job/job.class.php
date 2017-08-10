<?php
// ================================================================================================
//    System     : LTW
//    Module     : job.class.php
//    Version    : 1.0.0
//    Date       : 24.10.2006
//
//    Purpose    : Class definition for all actions with Job opportunity    
//
//    Called by  : *ANY
//
// ================================================================================================

// Include needed for the script
//include_once( SITE_PATH.'/include/lib.php' );
include_once( $_SERVER['DOCUMENT_ROOT'].'/sys/classes/sysRights.class.php' );
include_once( $_SERVER['DOCUMENT_ROOT'].'/sys/classes/sysForm.class.php' );

include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_job/job.defines.php' ); 

// ================================================================================================
//    Class             : Job
//    Version           : 1.0.0
//    Date              : 24.10.2006 
//
//    Constructor       : Yes
//    Parms             :
//    Returns           : None
//    Description       : Class definition for all actions with Job opportunity
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  24.10.2006 
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class Job {
       var $user_id = NULL;
       var $module = NULL;
       var $sort = NULL;
       var $display = 10;
       var $start = 0;
       var $fln = NULL;
       var $fltr = NULL;
       var $width = 500;
	   
	   var $Err = NULL;

       var $Rights = NULL;
       var $Form = NULL;
       var $Spr = NULL;
       var $Crypt = NULL;
	   
	   var $id = NULL;
	   var $date = NULL;
	   var $cat = NULL;
	   var $status = NULL;
	   var $vac = NULL;
	   var $age = NULL;
	   var $visible = NULL;
	   var $move = NULL;
	   var $position = NULL;
	   var $education = NULL;
	   var $experience = NULL;
	   var $contacts = NULL;
	   

       // ================================================================================================
       //    Function          : Job (Constructor)
       //    Version           : 1.0.0
       //    Date              : 24.10.2006
       //    Parms
       //    Returns           : Error Indicator
       //
       //    Description       :
       // ================================================================================================
       function Job($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL)
       {
                ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
                ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
                ( $display  !="" ? $this->display = $display  : $this->display = 10   );
                ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
                ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
                ( $width    !="" ? $this->width   = $width    : $this->width   = '700');

                if (empty($this->Right)) $this->Right = new Rights($this->user_id, $this->module);
                if (empty($this->db)) $this->db = new DB();
                if (empty($this->Form)) $this->Form = new Form('form_job');
                if (empty($this->Spr)) $this->Spr = new SysSpr( NULL, NULL, NULL, NULL, NULL, NULL, NULL );;
				
				if (empty($this->Msg)) $this->Msg= new ShowMsg();                   /* create ShowMsg object as a property of this class */
 				$this->Msg->SetShowTable(TblModJobSprTxt);

       } //end of Job Constructor




        // ================================================================================================
        // Function : JobOpSearch
        // Version :  1.0.0
        // Date :     06.10.2006
        // Parms :    $title
        // Returns :  true,false / Void
        // Description : Show page
        // ================================================================================================
        // Programmer : Igor Trohkymchuk
        // Date : 06.10.2006 
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function JobOpSearch()
        {
          $Arr = NULL;
          //echo '<br>$this->srch='.$this->srch;            
          if (empty($this->srch)) return $Arr;
          
          $q = "select * from ".TableJobOpp." where 1";
          //$q = $q." position like '%$srch%'";
          //$q = $q." or education like '%$srch%'";
          //$q = $q." or experience like '%$srch%'";
          //$q = $q." or description like '%$srch%'";
          //$q = $q." or contactinf like '%$srch%'";
          //$q = $q." or date like '%$srch%'";
          $q = $q." order by date desc";
          $res = $this->db->db_Query( $q );
          //echo '<br>$q='.$q;
          if (!$res or !$this->db->result) return false;

          $rows = $this->db->db_GetNumRows();
          //echo '<br>$rows='.$rows; 
          for( $i = 0; $i < $rows; $i++ )
          {
            $m =  $this->db->db_FetchAssoc();
            if( $this->srch=='All' OR stristr( strip_tags( $m['position'] ), $this->srch ) OR stristr( strip_tags( $m['education'] ), $this->srch ) OR stristr( strip_tags( $m['experience'] ), $this->srch ) OR stristr( strip_tags( $m['description'] ), $this->srch ) OR stristr( strip_tags( $m['contactinf'] ), $this->srch ) OR stristr( strip_tags( $m['date'] ), $this->srch ) )
            {
             $Arr[$m['id']] = $m;
            }
          }
          //echo '<br>$Arr='; print_r($Arr);
          return $Arr;
        } //--- end of JobOpSearch
       
 } //end of class Job
?>
