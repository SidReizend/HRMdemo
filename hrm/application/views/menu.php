<?php
$login_details = $this->session->userdata('logged_in');
$approval_user = $login_details['is_approval_user'];
$is_talent = $login_details['is_talent'];
$is_management = $login_details['is_management'];
$ci->load->model('defaultValues');
$is_encashment = ($ci->defaultValues->getValue(26) == 1) ? true : false;
$instance = & get_instance();
$menu = $instance->menu;
$backgroundColour = $ci->defaultValues->getValue(43);
?>
<header class="main-header">
    <nav class="navbar navbar-static-top" style="background-color: <?php echo $backgroundColour ? $backgroundColour : '#3c8dbc' ?>">
        <div class="container">
            <div class="navbar-header">
                <?php echo anchor('home', '<i class="fa fa-home fa-2x navbar-brand"></i>') ?>
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                    <i class="fa fa-bars"></i>
                </button>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                <ul class="nav navbar-nav">
                    <?php
                    $parent_attributes = array(
                        'class' => 'dropdown-toggle',
                        'data-toggle' => 'dropdown',
                    );
                    foreach ($menu as $menu_parent) {
                        ?>
                        <li class="dropdown">
                            <?php echo anchor($menu_parent['link'], $menu_parent['name'] . "<span class=\"caret\"></span>", $parent_attributes); ?>
                            <ul class="dropdown-menu" role="menu">
                                <?php
                                foreach ($menu_parent['children'] as $menu_child) {
                                    if (($is_management == TRUE && $menu_child['id'] == 14) || ($menu_child['id'] == 20 && $is_encashment == FALSE)) {
                                        ;
                                    } else {
                                        ?>
                                        <li> <?php echo anchor($menu_child['link'], $menu_child['name']); ?></li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
            <!-- Navbar Right Menu -->
            <?php
            $imagePath = (null == $login_details['imagePath'] || '' == $login_details['imagePath']) ? 'assets/dist/img/default.png' : 'uploads/' . $login_details['imagePath'];
            ?>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">

                    <li class="dropdown messages-menu" id="view_messages">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-envelope-o"></i>
                            <span class="label label-success unread_message_count" style="display: none;" >0</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have <span class="unread_message_count">0</span> new messages</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu" id="show-messages-area">


                                </ul>
                            </li>
                            <li class="footer"><?php echo anchor('notification', "See All Messages"); ?></li>
                        </ul>
                    </li>

                    <!-- User Account Menu -->
                    <li class="dropdown user user-menu">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false">
                            <img alt="User Image" class="user-image" src="<?php echo base_url() . $imagePath; ?>">
                            <span class="hidden-xs"><?php echo $login_details['username']; ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img alt="User Image" class="img-circle" src="<?php echo base_url() . $imagePath; ?>">

                                <p>
                                    <?php echo (null === $login_details['firstName']) ? $login_details['username'] : strtoupper($login_details['firstName']) . " " . strtoupper($login_details['lastName']); ?>
                                    <?php echo (null === $login_details['role']) ? '' : '<small>' . $login_details['role'] . '</small>'; ?>
                                </p>
                            </li>
                            <li class="user-body">
                                <div class="col-xs-12 text-center">

                                </div>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <?php
                                if (isset($login_details['talentId'])) {
                                    ?>
                                    <div class="pull-left">
                                        <?php echo anchor('talents/viewTalentDetails/talentId/' . $login_details['talentId'], 'Profile', array('class' => 'btn btn-default btn-flat')) ?>
                                    </div>
                                    <?php
                                }
                                ?>
                                <div class="pull-right">
                                    <?php echo anchor('passwordChange', 'Change Password', array('class' => 'btn btn-default btn-flat')) ?>
                                </div>
                            </li>
                            <li class="user-footer">
                                <?php
                                if (true == $login_details['is_administrator'] || true == $login_details['is_management']) {
                                    ?>
                                    <div class="pull-left">
                                        <?php echo anchor('PrevilageCntrler/', 'Privilege', array('class' => 'btn btn-default btn-flat')) ?>
                                    </div>
                                    <?php
                                }
                                ?>
                                <div class="pull-right">
                                    <?php echo anchor('home/logout', 'Sign out', array('class' => 'btn btn-default btn-flat')); ?>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <?php
                    if (isset($recentLogDetails)) {
                        ?>
                        <li>
                            <a data-toggle="" class="recent_login" href="#"><i class="fa fa-gears"></i></a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
            <!-- /.navbar-custom-menu -->
        </div>
        <!-- /.container-fluid -->
    </nav>
</header>
