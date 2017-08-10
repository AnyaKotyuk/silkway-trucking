<?php
include_once( SITE_PATH.'/sys/classes/sysPage.class.php' );

/**
 * PageAdmin
 *
 * @package
 * @author SEOTM
 * @copyright 2011
 * @version 2.0.0
 * @access public
 */
class PageAdmin extends Page
{

    public $module;   //--- execute module
    public $user;     //--- current user
    public $group;    //--- current users group
    public $Settings = NULL;
    public $adminMenuTree = NULL;
    public $adminMenuData = NULL;
    public $menu_id = NULL;
    public $module_name = NULL;
    static $type_action = array('' => '', 'back' => 'admin', 'front' => 'modules');

    /**
     * Class constructor
     * Constructor, Set the variabels
     * @return void
     */
    function PageAdmin()
    {
        if (!isset($_SESSION['session_id'])) {
            if (!headers_sent()) {
                session_start();
                $lifetime = time() + LOGOUT_TIME;
                setcookie(session_name(), session_id(), $lifetime, '/');
            }
        }

        //echo "<br>000SESSION['session_id'] = ".$_SESSION['session_id'];

        if (defined("MAKE_DEBUG") AND MAKE_DEBUG == 1) {
            $this->time_start = $this->getmicrotime();
            $_SESSION['cnt_db_queries'] = 0;
        }

        $this->Settings = check_init('SysSettings', 'SysSettings');
        if (isset($_SESSION['session_id'])) $session_id = $_SESSION['session_id'];
        else $session_id = NULL;
        $this->logon = check_init('logon', 'Authorization', "'$session_id'");
        //echo "<br>SESSION['session_id'] = ".$_SESSION['session_id'];

        // проверка переключеня языка
        if (isset($_GET['lang_pg'])) {
            //устанавливаем выбранный язык в сесию
            //$_SESSION['lang_pg'] = $_GET['lang_pg'];
            //устанавливаем выбранный язык для данного пользователя
            $this->Settings->SetLangBackend($this->logon->user_id, $_GET['lang_pg']);
        }
        //echo "<br>SESSION['session_id'] = ".$_SESSION['session_id'].'<br>$this->logon->user_id='.$this->logon->user_id;
        //echo "<br>_GET['lang_pg'] = ".$_GET['lang_pg'];
        //echo "<br>_SESSION['lang_pg'] = ".$_SESSION['lang_pg'];

        // установка языка из базы для данного пользователя
        if (!empty($this->logon->user_id))
            $tmp_lang = $this->Settings->GetLangBackend($this->logon->user_id, true);
        else
            $tmp_lang = '';
        //echo '<br>$this->logon->user_id='.$this->logon->user_id.' tmp_lang ='.$tmp_lang;print_r($tmp_lang);

        if (!isset($tmp_lang['cod']) OR empty($tmp_lang['cod'])) { // установка языка из базы для для всех пользователей
            $tmp_lang = SysLang::GetDefBackLangData();
            //echo "<br>tmp_lang = ".$tmp_lang;print_r($tmp_lang);
            if (count($tmp_lang) == 0 || empty($tmp_lang['cod'])) { // установка языка втупую
                if (!defined("_LANG_ID")) define("_LANG_ID", DEBUG_LANG);
                if (!defined("_LANG_SHORT")) define("_LANG_SHORT", DEBUG_LANG_SHORT);
            } else {
                if (!defined("_LANG_ID")) define("_LANG_ID", $tmp_lang["cod"]);
                if (!defined("_LANG_SHORT")) define("_LANG_SHORT", $tmp_lang["short_name"]);
            }
        } else {
            if (!defined("_LANG_ID")) define("_LANG_ID", $tmp_lang["cod"]);
            if (!defined("_LANG_SHORT")) define("_LANG_SHORT", $tmp_lang["short"]);
        }
        //устанавливаем переменную сессии, что бы можно было подключать визуальный редактор tiny_mce на нужной языковой версии.
        $_SESSION['_LANG_SHORT'] = _LANG_SHORT;
        //echo '<br>_LANG_ID='._LANG_ID.' _LANG_SHORT='._LANG_SHORT.' $_SESSION[_LANG_SHORT]='.$_SESSION['_LANG_SHORT'];


        //======== IMPORTANT!!! Class $Lang MUST created always after define _LANG_ID, ===========
        //======== othewize will be problems with multilanguages labels in admin part. ===========
        $this->Lang = check_init('BackendLang', 'BackendLang');
        $this->page_encode = $this->Lang->GetDefLangEncoding(_LANG_ID);

        if (empty($this->db)) $this->db = DBs::getInstance();
        if (empty($this->Form)) $this->Form = new Form();

        $this->title = 'Control panel';
        $this->msg = check_init_txt('TblBackMulti', TblBackMulti, _LANG_ID);
        $this->Msg = check_init('ShowMsg', 'ShowMsg');

        $this->send_headers();
    }


    /**
     * Class method WriteLoginPage()
     * Write Menu of admin page
     * @param mixed $width
     * @return void
     */
    function WriteLoginPage( $Err=NULL )
    {
        //echo "<br>_SERVER['HTTP_REFERER']=".$_SERVER['HTTP_REFERER']." NAME_SERVER=".NAME_SERVER;
        if( isset($_SERVER['REQUEST_URI']) AND !empty($_SERVER['REQUEST_URI']) AND (strstr($_SERVER['REQUEST_URI'], '/admin')) ) $referer_page=$_SERVER['REQUEST_URI'];
        else $referer_page=NULL;
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <title><?php echo $this->title;?></title>
            <!-- Tell the browser to be responsive to screen width -->
            <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
            <!-- Bootstrap 3.3.5 -->
            <link rel="stylesheet" href="/admin/include/adminLTE/AdminLTE-2.3.0/bootstrap/css/bootstrap.min.css">
            <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
            <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
            <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
            <![endif]-->
            <link rel="stylesheet" href="/admin/include/adminLTE/css/backend-login.css"/>

        </head>
            <body>
            <div id="loginbox" class="loginbox">

                <form method='POST' action='/admin/'>
                    <input type="hidden" name="referer_page" value="<?=$referer_page;?>" />
                    <div class="row">
                        <label for="LoginForm_username" class="required"><?php echo $this->msg['FLD_USERNAME']?> <span class="required">*</span></label>
                        <input name="enter_login" id="LoginForm_username" type="text" />
                    </div>
                    <div class="row">
                        <label for="LoginForm_password" class="required"><?php echo $this->msg['FLD_PASSWORD'];?> <span class="required">*</span></label>
                        <input name="enter_pass" id="LoginForm_password" type="password" />
                    </div>
    <!--                <div class="row rememberMe">-->
    <!--                    <input type="checkbox" value="1" name="LoginForm[rememberMe]" id="LoginForm_rememberMe" /><label for="LoginForm_rememberMe">Remember Me</label>-->
    <!--                </div>-->
                    <div class="row ">
                        <input type="submit" name="yt0" value="<?php echo $this->msg['_TXT_LOGIN'];?>" />
    <!--                    <a href="index.php?task=forgot_pass">--><?//=$this->msg['TXT_SYS_USER_FORGOT_PASSWORD'];?><!--</a>-->
                    </div>
                </form>
                <div class="bg"></div>
                <div class="logo-container">
                    <img src="/admin/include/adminLTE/images/login-page/logo.png" class="logo" alt="">
                </div>
            </div>
            <?
            if(!empty($Err)){
                ?><script type="text/javascript">alert('<?=$Err?>');</script><?
            }
            ?>
        </body>
        </html>
        <?php
    }


    /**
     * Class method WriteHeader()
     * Write Header of Admin Page
     * @param mixed $module_name
     * @return void
     */
    function WriteHeader($module_name=NULL)
    {
        $this->send_headers();

        if(empty($module_name)){
            $module_name = $this->module_name;
        }

        //load admin menu into arrays $this->adminMenuTree
        $this->loadAdminMenu();
        $this->menu_id = $this->getMenuIdByModuleId($this->module);
        //echo '<br />adminMenuTree=';print_r($this->adminMenuTree);

        if (!empty($module_name)){
            $this->title = $this->msg["_TXT_TITLE"] . ' - ' . $module_name;
        }else {
            if (empty($this->logon->user_id)) $this->title = $this->msg["_TXT_TITLE"] . ' - ' . $this->msg["_TXT_LOGIN"];
            else $this->title = $this->msg["_TXT_TITLE"] . ' - ' . $this->msg["_TXT_CONTROL_PANEL"] . ', ' . $this->logon->login . '!';
        }

        ?>
        <!DOCTYPE html>
        <!--
        This is a starter template page. Use this page to start your new project from
        scratch. This page gets rid of all links and provides the needed markup only.
        -->
        <html>
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <title><?php echo $this->title;?></title>
            <!-- Tell the browser to be responsive to screen width -->
            <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

            <link href="/admin/include/adminLTE/AdminLTE-2.3.0/plugins/select2/select2.min.css" rel="stylesheet" />

            <!-- Bootstrap 3.3.5 -->
            <link rel="stylesheet" href="/admin/include/adminLTE/AdminLTE-2.3.0/bootstrap/css/bootstrap.min.css">
            <!-- Font Awesome -->
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
            <!-- Ionicons -->
            <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
            <!-- Theme style -->
            <link rel="stylesheet" href="/admin/include/adminLTE/AdminLTE-2.3.0/dist/css/AdminLTE.css">
            <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
                  page. However, you can choose any other skin. Make sure you
                  apply the skin class to the body tag so the changes take effect.
            -->
            <link rel="stylesheet" href="/admin/include/adminLTE/AdminLTE-2.3.0/dist/css/skins/skin-blue.min.css">

            <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
            <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
            <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
            <![endif]-->


            <!-- REQUIRED JS SCRIPTS -->

            <!-- jQuery 2.1.4 -->
            <script src="/admin/include/adminLTE/AdminLTE-2.3.0/plugins/jQuery/jQuery-2.1.4.min.js"></script>
            <!-- Bootstrap 3.3.5 -->
            <script src="/admin/include/adminLTE/AdminLTE-2.3.0/bootstrap/js/bootstrap.min.js"></script>
            <!-- AdminLTE App -->
            <script src="/admin/include/adminLTE/AdminLTE-2.3.0/dist/js/app.min.js"></script>

            <!-- Select2 -->
            <script src="/admin/include/adminLTE/AdminLTE-2.3.0/plugins/select2/select2.full.min.js"></script>
            <!-- InputMask -->
            <script src="/admin/include/adminLTE/AdminLTE-2.3.0/plugins/input-mask/jquery.inputmask.js"></script>
            <script src="/admin/include/adminLTE/AdminLTE-2.3.0/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
            <script src="/admin/include/adminLTE/AdminLTE-2.3.0/plugins/input-mask/jquery.inputmask.extensions.js"></script>

            <!-- Optionally, you can add Slimscroll and FastClick plugins.
                 Both of these plugins are recommended to enhance the
                 user experience. Slimscroll is required when using the
                 fixed layout. -->

            <link rel="stylesheet" type="text/css" href="http://<?=NAME_SERVER?>/admin/include/adminLTE/css/adminLTE.css" />

<!--            <link rel="stylesheet" type="text/css" href="http://--><?//=NAME_SERVER?><!--/admin/include/adminLTE/css/Admin.css" />-->
<!--            <link rel="stylesheet" type="text/css" href="http://--><?//=NAME_SERVER?><!--/admin/include/adminLTE/css/AdminHTML.css" />-->
<!--            <link rel="stylesheet" type="text/css" href="http://--><?//=NAME_SERVER?><!--/admin/include/adminLTE/css/style.css" />-->

            <script type="text/javascript" src="http://<?=NAME_SERVER?>/sys/js/jQuery/jquery.tablednd.js"></script>
            <script type="text/javascript" src="http://<?=NAME_SERVER?>/sys/js/jQuery/easyTooltip.js"></script>
            <script type="text/javascript" src="http://<?=NAME_SERVER?>/sys/js/jQuery/jquery.form.js"></script>
            <script type="text/javascript" src="http://<?=NAME_SERVER?>/sys/js/jQuery/jquery.AjaxUpload.js"></script>
<!--            <script type="text/javascript" src="http://--><?//=NAME_SERVER?><!--/sys/js/fancybox/jquery.fancybox-1.3.4.js"></script>-->
<!--            <link rel="stylesheet" type="text/css" href="http://--><?//=NAME_SERVER?><!--/sys/js/fancybox/jquery.fancybox-1.3.4.css" />-->
            <!-- Add fancyBox -->
            <link rel="stylesheet" href="http://<?=NAME_SERVER?>/admin/include/adminLTE/js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
            <script type="text/javascript" src="http://<?=NAME_SERVER?>/admin/include/adminLTE/js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
            <!-- End Add fancyBox -->
            <script type="text/javascript" src="http://<?=NAME_SERVER?>/admin/include/adminLTE/js/funcs.js"></script>
            <script type="text/javascript" src="http://<?=NAME_SERVER?>/sys/js/overlib422/overlib.js"></script>

            <?
            $jqueryUi=new jqueryUi();
            $jqueryUi->load_files();
            ?>
        </head>
        <!--
        BODY TAG OPTIONS:
        =================
        Apply one or more of the following classes to get the
        desired effect
        |---------------------------------------------------------|
        | SKINS         | skin-blue                               |
        |               | skin-black                              |
        |               | skin-purple                             |
        |               | skin-yellow                             |
        |               | skin-red                                |
        |               | skin-green                              |
        |---------------------------------------------------------|
        |LAYOUT OPTIONS | fixed                                   |
        |               | layout-boxed                            |
        |               | layout-top-nav                          |
        |               | sidebar-collapse                        |
        |               | sidebar-mini                            |
        |---------------------------------------------------------|
        -->
        <body class="hold-transition skin-blue sidebar-mini">

        <div class="wrapper">

            <!-- Main Header -->
            <header class="main-header">

                <!-- Logo -->
                <a href="/admin/" class="logo">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span class="logo-mini"><b>SEOCMS</b></span>
                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg"><img src="/admin/include/adminLTE/images/seotm-seocms.png" /></span>
                </a>

                <!-- Header Navbar -->
                <nav class="navbar navbar-static-top" role="navigation">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                    </a>
                    <!-- Navbar Right Menu -->
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <?php
                            $path = SITE_PATH.'/modules/mod_feedback';
                            if(file_exists($path)) {
                                include_once( SITE_PATH.'/modules/mod_feedback/feedback.defines.php' );
                                $Feedback = check_init('Feedback', 'Feedback');
                                $arr_f = $Feedback->GetNewContacts();
                                $cnt_f = count($arr_f);
                                ?>
                                <!-- Messages: style can be found in dropdown.less-->
                                <li class="dropdown messages-menu">
                                    <!-- Menu toggle button -->
                                    <a href="/admin/index.php?module=57" title="<?php if(isset($this->msg['FLD_TXT_ALL_FEEDBACK_MESSAGES'])){ echo $this->msg['FLD_TXT_ALL_FEEDBACK_MESSAGES']; }?>" <?/*class="dropdown-toggle" data-toggle="dropdown"*/?>>
                                        <i class="fa fa-envelope-o"></i>
                                        <span class="label label-success"><?if($cnt_f>0){echo $cnt_f;}?></span>
                                    </a>
                                    <?/*
                                    <ul class="dropdown-menu">
                                        <li class="header">You have 4 messages</li>
                                        <li>
                                            <!-- inner menu: contains the messages -->
                                            <ul class="menu">
                                                <li><!-- start message -->
                                                    <a href="#">
                                                        <div class="pull-left">
                                                            <!-- User Image -->
                                                            <img
                                                                src="/admin/include/adminLTE/AdminLTE-2.3.0/dist/img/user2-160x160.jpg"
                                                                class="img-circle"
                                                                alt="User Image">
                                                        </div>
                                                        <!-- Message title and timestamp -->
                                                        <h4>
                                                            Support Team
                                                            <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                                        </h4>
                                                        <!-- The message -->
                                                        <p>Why not buy a new awesome theme?</p>
                                                    </a>
                                                </li>
                                                <!-- end message -->
                                            </ul>
                                            <!-- /.menu -->
                                        </li>
                                        <li class="footer"><a href="#">See All Messages</a></li>
                                    </ul>
                                    */?>
                                </li>
                                <!-- /.messages-menu -->
                                <?php
                            }
                            ?>

                            <?php
                            $path = SITE_PATH.'/modules/mod_order';
                            if(file_exists($path)) {
                                include_once( SITE_PATH.'/modules/mod_order/order.defines.php' );
                                $Orders = check_init('Order', 'Order');
                                $arr_f = $Orders->GetNewOrders();
                                $cnt_f = count($arr_f);
                                ?>
                                <!-- Order Menu -->
                                <li class="dropdown notifications-menu">
                                    <!-- Menu toggle button -->
                                    <a href="/admin/index.php?module=106" title="<?php echo $this->msg['FLD_TXT_ALL_ORDERS']?>" <?/*class="dropdown-toggle" data-toggle="dropdown"*/?>>
                                        <i class="fa fa-cart-plus"></i>
                                        <span class="label label-warning"><?php echo $cnt_f;?></span>
                                    </a>
                                    <?/*
                                    <ul class="dropdown-menu">
                                        <li class="header">You have 10 notifications</li>
                                        <li>
                                            <!-- Inner Menu: contains the notifications -->
                                            <ul class="menu">
                                                <li><!-- start notification -->
                                                    <a href="#">
                                                        <i class="fa fa-users text-aqua"></i> 5 new members joined today
                                                    </a>
                                                </li>
                                                <!-- end notification -->
                                            </ul>
                                        </li>
                                        <li class="footer"><a href="#">View all</a></li>
                                    </ul>
                                    */?>
                                </li>
                                <?php
                            }
                            /*
                            ?>
                            <!-- Tasks Menu -->
                            <li class="dropdown tasks-menu">
                                <!-- Menu Toggle Button -->
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-flag-o"></i>
                                    <span class="label label-danger">9</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header">You have 9 tasks</li>
                                    <li>
                                        <!-- Inner menu: contains the tasks -->
                                        <ul class="menu">
                                            <li><!-- Task item -->
                                                <a href="#">
                                                    <!-- Task title and progress text -->
                                                    <h3>
                                                        Design some buttons
                                                        <small class="pull-right">20%</small>
                                                    </h3>
                                                    <!-- The progress bar -->
                                                    <div class="progress xs">
                                                        <!-- Change the css width attribute to simulate progress -->
                                                        <div class="progress-bar progress-bar-aqua" style="width: 20%"
                                                             role="progressbar" aria-valuenow="20" aria-valuemin="0"
                                                             aria-valuemax="100">
                                                            <span class="sr-only">20% Complete</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                            <!-- end task item -->
                                        </ul>
                                    </li>
                                    <li class="footer">
                                        <a href="#">View all tasks</a>
                                    </li>
                                </ul>
                            </li>
                            <?*/?>

                            <!-- User Account Menu -->
                            <li class="dropdown user user-menu">
                                <!-- Menu Toggle Button -->
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <!-- The user image in the navbar-->
                                    <img src="/admin/include/adminLTE/AdminLTE-2.3.0/dist/img/user0-160x160.png" class="user-image" alt="User Image">
                                    <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                    <span class="hidden-xs"><?php echo $this->user;?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- The user image in the menu -->
                                    <li class="user-header">
                                        <img src="/admin/include/adminLTE/AdminLTE-2.3.0/dist/img/user0-160x160.png" class="img-circle" alt="User Image">

                                        <p>
                                            <?php
                                            //var_dump($this->logon);
                                            echo $this->logon->login;
                                            ?>
                                            <small><?php echo $this->logon->email?></small>
                                        </p>
                                    </li>
                                    <!-- Menu Body -->
<!--                                    <li class="user-body">-->
<!--                                        <div class="col-xs-4 text-center">-->
<!--                                            <a href="#">Followers</a>-->
<!--                                        </div>-->
<!--                                        <div class="col-xs-4 text-center">-->
<!--                                            <a href="#">Sales</a>-->
<!--                                        </div>-->
<!--                                        <div class="col-xs-4 text-center">-->
<!--                                            <a href="#">Friends</a>-->
<!--                                        </div>-->
<!--                                    </li>-->
                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="<?php echo $_SERVER['PHP_SELF'].'?module=14&task=edit&id='.$this->logon->user_id;?>" class="btn btn-default btn-flat" title='<?php echo $this->msg["_TXT_PROFILE"]?>'><?php echo $this->msg["_TXT_PROFILE"]?></a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="<?php echo $_SERVER['PHP_SELF'].'?logout=logout';?>" class="btn btn-default btn-flat"  title='<?php echo $this->msg["_TXT_LOGOUT"]?>'><?php echo $this->msg["_TXT_LOGOUT"]?></a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <!-- Control Sidebar Toggle Button -->
                            <li>
                                <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar">

                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">

                    <!-- Sidebar user panel (optional) -->
<!--                    <div class="user-panel">-->
<!--                        <div class="pull-left image">-->
<!--                            <img src="/admin/include/AdminLTE-2.3.0/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">-->
<!--                        </div>-->
<!--                        <div class="pull-left info">-->
<!--                            <p>Alexander Pierce</p>-->
<!--                            <!-- Status -->-->
<!--                            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>-->
<!--                        </div>-->
<!--                    </div>-->

                    <!-- search form (Optional) -->
<!--                    <form action="#" method="get" class="sidebar-form">-->
<!--                        <div class="input-group">-->
<!--                            <input type="text" name="q" class="form-control" placeholder="Search...">-->
<!--                            <span class="input-group-btn">-->
<!--                                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>-->
<!--                                </button>-->
<!--                            </span>-->
<!--                        </div>-->
<!--                    </form>-->
                    <!-- /.search form -->

                    <!-- Sidebar Menu -->
                    <?php $this->WriteSidebarMenu();?>
                    <!-- /.sidebar-menu -->
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?php echo $module_name;?>
<!--                        <small>Optional description</small>-->
                    </h1>
                    <?php echo $this->WriteBreadcrumbs();?>
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Your Page Content Here -->




    <?
    }

    /**
     * Class method WriteFooter()
     * Write Footer of admin page
     * @return void
     * @author Ihor Trokhymchuk  <ihor@seotm.com>
     */
    function WriteFooter()
    {
        ?>
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

            <!-- Main Footer -->
            <footer class="main-footer">
                <!-- To the right -->
                <div class="pull-right hidden-xs">
                    Powered by <a href="http://seotm.com" target="_blank" title="SEOTM.COM">SEOTM.COM</a>
                </div>
                <!-- Default to the left -->
                <strong>Copyright &copy; 2005-<?= date("Y");?> Content Management System SEOCMS.</strong> All rights reserved.
                <?
                if (defined("MAKE_DEBUG") AND MAKE_DEBUG == 1) {
                    $this->time_end = $this->getmicrotime();
                    ?>
                    <small class="text-muted"><br/><i class="fa fa-clock-o"></i><?php printf(" TIME: %2.3fs", $this->time_end - $this->time_start);
                    if (isset($_SESSION['cnt_db_queries'])){
                        ?><br/><i class="fa fa-server"></i> QUERIES: <?php echo $_SESSION['cnt_db_queries'];?><?
                    }
                    ?>
                    </small>
                    <?
                }
                ?>
        </div>
            </footer>

            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Create the tabs -->
                <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
                    <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a>
                    </li>
<!--                    <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>-->
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <!-- Home tab content -->
                    <div class="tab-pane active" id="control-sidebar-home-tab">
                        <h3 class="control-sidebar-heading"><?php echo $this->msg['_FLD_LANGUAGE'];?>:</h3>
                        <?php echo $this->Lang->WriteLangPanel(_LANG_ID);?>
<!--                        <ul class="control-sidebar-menu">-->
<!--                            <li>-->
<!--                                <a href="javascript::;">-->
<!--                                    <i class="menu-icon fa fa-birthday-cake bg-red"></i>-->
<!---->
<!--                                    <div class="menu-info">-->
<!--                                        <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>-->
<!---->
<!--                                        <p>Will be 23 on April 24th</p>-->
<!--                                    </div>-->
<!--                                </a>-->
<!--                            </li>-->
<!--                        </ul>-->
                        <!-- /.control-sidebar-menu -->

<!--                        <h3 class="control-sidebar-heading">Tasks Progress</h3>-->
<!--                        <ul class="control-sidebar-menu">-->
<!--                            <li>-->
<!--                                <a href="javascript::;">-->
<!--                                    <h4 class="control-sidebar-subheading">-->
<!--                                        Custom Template Design-->
<!--                                        <span class="label label-danger pull-right">70%</span>-->
<!--                                    </h4>-->
<!---->
<!--                                    <div class="progress progress-xxs">-->
<!--                                        <div class="progress-bar progress-bar-danger" style="width: 70%"></div>-->
<!--                                    </div>-->
<!--                                </a>-->
<!--                            </li>-->
<!--                        </ul>-->
                        <!-- /.control-sidebar-menu -->

                    </div>
                    <!-- /.tab-pane -->
                    <!-- Stats tab content -->
<!--                    <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>-->
                    <!-- /.tab-pane -->
                    <!-- Settings tab content -->
<!--                    <div class="tab-pane" id="control-sidebar-settings-tab">-->
<!--                        <form method="post">-->
<!--                            <h3 class="control-sidebar-heading">General Settings</h3>-->
<!---->
<!--                            <div class="form-group">-->
<!--                                <label class="control-sidebar-subheading">-->
<!--                                    Report panel usage-->
<!--                                    <input type="checkbox" class="pull-right" checked>-->
<!--                                </label>-->
<!---->
<!--                                <p>-->
<!--                                    Some information about this general settings option-->
<!--                                </p>-->
<!--                            </div>-->
                            <!-- /.form-group -->
<!--                        </form>-->
<!--                    </div>-->
                    <!-- /.tab-pane -->
                </div>
            </aside>
            <!-- /.control-sidebar -->
            <!-- Add the sidebar's background. This div must be placed
                 immediately after the control sidebar -->
            <div class="control-sidebar-bg"></div>
        </div><!-- ./wrapper -->

            <script type="text/javascript">
                $('select.select2').select2();

                //Datemask dd/mm/yyyy
                //$("[data-mask]").inputmask("/yyyy/mm/dd", {"placeholder": "yyyy/mm/dd"});
                //Money Euro
                //$("[data-mask]").inputmask();
            </script>


        </body>
        </html>
    <?php
    }

    /**
     * Class method WriteDashboard()
     * Write Footer of admin page
     * @return void
     * @author Ihor Trokhymchuk  <ihor@seotm.com>
     */
    function WriteDashboard(){
        ?>

        <?php
    }

    /**
     * Class method getMenuIdByModuleId()
     * return id of the menu item by $module_id
     * @param integer $module_id - id of the module (function)
     * @return void
     * @author Ihor Trokhymchuk  <ihor@seotm.com>
     */
    function getMenuIdByModuleId($module_id)
    {
        if(empty($module_id)){
            return false;
        }
        $db = DBs::getInstance();
        $q = "SELECT
                `" . TblSysMenuAdm . "`.`id`
              FROM
                `" . TblSysMenuAdm . "`
              WHERE
                `" . TblSysMenuAdm . "`.`group` = '" . $this->group . "'
                AND `" . TblSysMenuAdm . "`.`function` = '".$module_id."'
              ORDER BY `" . TblSysMenuAdm . "`.`level`, `" . TblSysMenuAdm . "`.`id`
        ";

        $res = $db->db_Query($q);
//        echo '<br>WriteMenu:: $q='.$q.' $res='.$res;
        if (!$res) {
            return false;
        }
        $rows = $db->db_GetNumRows($res);
//        echo '<br>$rows='.$rows;
        if ($rows == 0) {
            return false;
        }
        //for ($i = 0; $i < $rows; $i++) {
            $row = $db->db_FetchAssoc($res);
        //}
        return $row['id'];
    }

    /**
     * Class method loadAdminMenu()
     * load tree of admin menu to array
     * @return void
     * @author Ihor Trokhymchuk  <ihor@seotm.com>
     */
    function loadAdminMenu()
    {
        $db = DBs::getInstance();
        $q = "SELECT
                `" . TblSysMenuAdm . "`.*, `" . TblSysSprMenuAdm . "`.`name`
              FROM
                `" . TblSysMenuAdm . "`, `" . TblSysSprMenuAdm . "`
              WHERE
                `" . TblSysMenuAdm . "`.`group` = '" . $this->group . "'
                AND `" . TblSysSprMenuAdm . "`.`cod` = `" . TblSysMenuAdm . "`.`id`
                AND `" . TblSysSprMenuAdm . "`.`lang_id` = '" . _LANG_ID . "'
              ORDER BY `" . TblSysMenuAdm . "`.`level`, `" . TblSysMenuAdm . "`.`move`
        ";

        $res = $db->db_Query($q);
//        echo '<br>WriteMenu:: $q='.$q.' $res='.$res;
        if (!$res) {
            return false;
        }
        $rows = $db->db_GetNumRows($res);
//        echo '<br>$rows='.$rows;
        if ($rows == 0) {
            return false;
        }

        for ($i = 0; $i < $rows; $i++) {
            $row = $db->db_FetchAssoc($res);
            $this->setAdminMenuTreeItem($row['level'], $row['id'], $row);
            $this->setAdminMenuDataItem($row['id'], $row);
        }
//        var_dump($this->adminMenuTree);
//        var_dump($this->adminMenuData);
        return true;
    }

    /**
     * PClass method setAdminMenuTreeItem()
     * Set item data to admin menu tree
     * @param integer $level
     * @param integer $id
     * @param integer $data
     * @return void
     * @author Ihor Trokhymchuk  <ihor@seotm.com>
     */
    function setAdminMenuTreeItem($level, $id, $data)
    {
        $this->adminMenuTree[$level][$id] = $data;
    }

    /**
     * Class method getAdminMenuTreeAll
     * get array $this->adminMenuTree
     * @return array $this->adminMenuTree
     * @author Ihor Trokhymchuk  <ihor@seotm.com>
     */
    function getAdminMenuTreeAll()
    {
        return $this->adminMenuTree;
    }

    /**
     * Class method getAdminMenuTreeItem()
     * Get item data to admin menu tree
     * @param integer $item
     * @return array
     * @author Ihor Trokhymchuk  <ihor@seotm.com>
     */
    function getAdminMenuTreeItem($item=0)
    {
        if(!isset($this->adminMenuTree[$item])) return false;
        return $this->adminMenuTree[$item];
    }

    /**
     * Class method setAdminMenuDataItem()
     * Set item data to admin menu data array
     * @param integer $id
     * @param integer $data
     * @return void
     * @author Ihor Trokhymchuk  <ihor@seotm.com>
     */
    function setAdminMenuDataItem($id, $data)
    {
        $this->adminMenuData[$id] = $data;
    }

    /**
     * Class method getAdminMenuTreeItem()
     * Get item data to admin menu data array
     * @param integer $item
     * @return array
     * @author Ihor Trokhymchuk  <ihor@seotm.com>
     */
    function getAdminMenuDataItem($item=0)
    {
        if(!isset($this->adminMenuData[$item])) return false;
        return $this->adminMenuData[$item];
    }

    /**
     * Class method isSubLevels()
     * Checking exist or not sublevels for category $id_cat
     * @param integer $id - id of the category
     * @return integer count of sublevels
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     */
    function isSubLevels($id)
    {
        if( !$this->getAdminMenuTreeItem($id) ) return false;
        $count = count($this->getAdminMenuTreeItem($id));
        return $count;
    }

    /**
     * Class method isCatASubcatOfLevel
     * Checking if the category $id_cat is a subcategory of $item at any dept start from $arr[$item]
     * @param integer $id_cat - id of the category
     * @param integer $item - as index for array $arr
     * @return array with index as counter
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     */
    function isCatASubcatOfLevel($id_cat, $item)
    {
        if($id_cat==$item) return true;
        $a_tree = $this->getAdminMenuTreeItem($item);
//        echo '<br>$id_cat='.$id_cat;
//        var_dump($a_tree);
        if( !$a_tree ) return false;
        $keys = array_keys($a_tree);
        $rows = count($keys);
        if(array_key_exists($id_cat, $a_tree)){
            return true;
        }
        for ($i=0;$i<$rows;$i++) {
            $id = $keys[$i];
            //echo '<br />$id='.$id;
            if( $this->getAdminMenuTreeItem($id) AND is_array($this->getAdminMenuTreeItem($id)) ) {
                $res = $this->isCatASubcatOfLevel($id_cat, $id);
                if($res) return true;
            }
        }
        return false;
    } // end of function isCatASubcatOfLevel()

    /**
     * Class method getTopLevel
     * get the top level of categary $id_cat
     * @param integer $id_cat - id_cat as index for array $arr
     * @param array $arr - pointer to array with indexes as $id_cat
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     */
    function getTopLevel($id_cat)
    {
        $cat_data = $this->getAdminMenuTreeItem($id_cat);
        if(!$cat_data) return false;
        if($cat_data['level']==0) return $id_cat;
        return $this->getTopLevel($cat_data['level']);
    } // end of function getTopLevel()

    /**
     * PageAdmin::WriteSidebarMenu()
     * Write sidebar menu
     * @param integer $item - id of the category menu
     * @param integer $node - node of sublevel
     * @return void
     */
    function WriteSidebarMenu($item=0, $node=0){
        $menu = $this->getAdminMenuTreeItem($item);
//        var_dump($menu);
        if(!is_array($menu) OR count($menu)==0){
            ?><div class="alert alert-warning alert-dismissible"><h4><i class="icon fa fa-warning"></i>Alert!</h4>Sorry, Admin menu not exist :(</div><?
            return false;
        }
        if($node==0){
            $ul_class = "sidebar-menu";
        }else{
            $ul_class = "treeview-menu";
        }
        if($node!=1){
            ?><ul class="<?php echo $ul_class;?>"><?php
        }

        foreach($menu as $level=>$data){
            $pointer_right = '';
            if($node==0){
                $li_class = "header";
                $href = "#";
            }elseif($this->isSubLevels($level)){
                $li_class = "treeview";
                //echo '<br>$this->module='.$this->module.' $level='.$level;
                //echo '<br>qqq='.$this->isCatASubcatOfLevel($this->module, $level);
                if($this->menu_id>0 AND $this->isCatASubcatOfLevel($this->menu_id, $level)){
                    $li_class .= ' active';
                }
                $href = "#";
                $pointer_right = '<i class="fa fa-angle-left pull-right"></i>';
            }elseif($data['function']==$this->module){
                $li_class = "active";
                $href = "?module=".$data['function'];
            }else{
                $li_class = "";
                $href = "?module=".$data['function'];
            }
            ?>
            <li class="<?php echo $li_class;?>">
                <?php
                if($node>0){
                    ?><a href="<?php echo $href;?>"><i class="fa fa-circle-o"></i> <span><?php echo $data['name'];?></span> <?php echo $pointer_right?></a><?php
                }else{
                    echo $data['name'];
                }

                if($this->isSubLevels($level)){
                    $this->WriteSidebarMenu($level, $node+1);
                }
                ?>
            </li>
        <?php

        }
        if($node!=1) {
            ?></ul><?php
        }
    }

    /**
     * PageAdmin::loadAdminMenu()
     * return id of the menu item by $module_id
     * @return void
     * @author Ihor Trokhymchuk  <ihor@seotm.com>
     */
    function WriteBreadcrumbs($item = NULL, $breadcrumbs=NULL){
        if(empty($item)){
            $item = $this->menu_id;
        }
        //echo '<br>$item='.$item.' $this->menu_id='.$this->menu_id;
        $menu = $this->getAdminMenuDataItem($item);
        //var_dump($menu);
        $name = stripslashes($menu['name']);
        $level = $menu['level'];
        //echo '<br>$level='.$level;
        if(empty($item) AND $level==0){
            return false;
        }
        if($level==0){
            $breadcrumbs = '<ol class="breadcrumb"><li><i class="fa fa-home"></i>&nbsp;&nbsp;&nbsp;'.$name.'</li>'.$breadcrumbs.'</ol>';
        }else{
            if($item==$this->menu_id){
                $class_li = 'active';
                $href = '/admin/index.php?module='.$menu['function'];
            }else{
                $class_li = '';
                $href = '';
            }
            $breadcrumbs0 = '<li class="'.$class_li.'">';
            if(!empty($href)){
                $breadcrumbs0 .= '<a href="'.$href.'" title="'.$name.'">';
            }
            $breadcrumbs0 .= $name;
            if(!empty($href)){
                $breadcrumbs0 .= '</a>';
            }
            $breadcrumbs0 .= '</li>'.$breadcrumbs;
            $breadcrumbs = $this->WriteBreadcrumbs($level, $breadcrumbs0);
        }
        return $breadcrumbs;
    }

    /**
     * PageAdmin::WriteContentH()
     * Write Header of Content Admin Page
     * @return void
     */
    function WriteContentH(){
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary"><?
    }


    /**
     * PageAdmin::WriteContentF()
     * Write Footer of Content of admin page
     * @return void
     */
    function WriteContentF()
    {
        ?>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    <?
    }
}