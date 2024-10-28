<!DOCTYPE html>
<?php
//using url for get id
$this->load->helper('url');
$url = $this->uri->segment(1);
$session_data = $this->session->userdata('logged_in');
if ($url != 'passwordChange' && $session_data['force_password_change'] == '1') {
    $this->session->set_flashdata('flashSuccess', 'Please change password');
    redirect('passwordChange', 'refresh');
}
$ci = &get_instance();
$ci->load->model('defaultValues');
$companyHrm = $ci->defaultValues->getValue(21);
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>HRM | <?php echo $companyHrm; ?></title>
        <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/favicon.png">
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables/tableResponsive.css">
        <?php
        if (isset($datatables)) {
            ?>
            <!-- DataTables -->
            <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.bootstrap.css">
            <?php
        }
        ?>
        <?php
        if (isset($select2)) {
            ?>
            <!-- Select2 -->
            <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/select2/select2.min.css">
            <?php
        }
        ?>
        <?php
        if (isset($form_validator)) {
            ?>
            <!-- Select2 -->
            <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/jquery-form-validator/form-validator/theme-default.min.css">
            <?php
        }
        ?>
        <?php
        if (isset($form_validator2)) {
            ?>
            <!-- Select2 -->
            <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/validation/cmxform.css">
            <?php
        }
        ?>
        <?php //if (isset($alertify)) { ?>
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/alertify/alertify.core.css"/>
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/alertify/alertify.default.css" id="toggleCSS"/>
        <?php //} ?>
        <?php
        if (isset($datepicker)) {
            ?>
            <!--bootstrap datepicker -->
            <link rel = "stylesheet" href = "<?php echo base_url(); ?>assets/plugins/datepicker/datepicker3.css">
            <?php
        }
        ?>
        <?php
        if (isset($pagination)) {
            ?>
            <!--bootstrap datepicker -->
            <link rel = "stylesheet" href = "<?php echo base_url(); ?>assets/plugins/pagination.css">
            <?php
        }
        ?>
        <?php
        if (isset($full_calendar)) {
            ?>
            <!--bootstrap datepicker -->
            <link rel = "stylesheet" href = "<?php echo base_url(); ?>assets/plugins/fullcalendar/fullcalendar.min.css">
            <?php
        }
        ?>
        <?php
        if (isset($wysihtml5)) {
            ?>
            <!--bootstrap datepicker -->
            <link rel = "stylesheet" href = "<?php echo base_url(); ?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
            <!-- bootstrap wysihtml5 - text editor -->
            <?php
        }
        ?>
        <?php
        if (isset($timepicker)) {
            ?>
            <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/timepicker/bootstrap-timepicker.min.css">
            <?php
        }
        ?>
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/skins/_all-skins.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/main.css">
    </head>
    <style>
        @media screen and (min-width: 1024px) {
            .outsideTimeTd {
                width: 10%;
            }
        }
    </style>
    <!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
    <body class="hold-transition skin-blue layout-top-nav">
        <div class="wrapper">
            <?php
            if (!isset($no_menu)) {
                include_once 'menu.php';
            }
            ?>
            <!-- Full Width Column -->
            <div class="content-wrapper">
                <div class="container">
                    <div class="row" id="flashMessage">
                        <?php
                        if ($this->session->flashdata('flashError')) {
                            ?>
                            <br/>
                            <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                <h4>Error!</h4>
                                <p><?php echo $this->session->flashdata('flashError'); ?>.</p>
                            </div> 
                            <?php
                        }
                        if ($this->session->flashdata('flashSuccess')) {
                            ?>
                            <br/>
                            <div class="alert alert-success alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                <h4>Success!</h4>
                                <p><?php echo $this->session->flashdata('flashSuccess'); ?>.</p>
                            </div> 
                            <?php
                        }
                        ?>

                    </div>
                </div>
