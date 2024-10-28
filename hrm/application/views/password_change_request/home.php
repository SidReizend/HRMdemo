<!DOCTYPE html>
<?php
$ci = &get_instance();
$ci->load->model('defaultValues');
$siteUrl = $ci->defaultValues->getValue(20);
$siteUrlWithouHttp = $ci->defaultValues->getValue(22);
$companyHrm = $ci->defaultValues->getValue(21);
$infoUrl = $ci->defaultValues->getValue(23);
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>HRM | <?php echo $companyHrm; ?></title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/skins/_all-skins.min.css">

        <!-- iCheck -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/iCheck/square/blue.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="hold-transition">

        <!----------------------------------------------------------------------->
        <div class="col-sm-12 coro-main">
            <div class="col-sm-8 coro-lt">
                <div class="col-sm-12 right-gry">
                    <div id="myCarousel" class="carousel slide" data-ride="carousel" style="border:solid 10px #FFF;"> 
                        <!-- Indicators 
                  <ol class="carousel-indicators">
                    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                    <li data-target="#myCarousel" data-slide-to="1"></li>
                    <li data-target="#myCarousel" data-slide-to="2"></li>
                    <li data-target="#myCarousel" data-slide-to="3"></li>
                    <li data-target="#myCarousel" data-slide-to="4"></li>
                  </ol>--> 

                        <!-- Wrapper for slides -->
                        <div class="col-sm-12 bx-banner"> </div>
                        <div class="carousel-inner" role="listbox">
                            <div class="item active"> <img src="<?php echo base_url(); ?>assets/img/1.jpg" alt="Chania"> 
                                <!--<div class="carousel-caption">
                            <h2 style="font-family: 'Raleway', sans-serif; font-weight:100;">The technology partner who assist you <br>to accomplish your vison</h2>
                            
                          </div>--> 
                            </div>
                            <div class="item"> <img src="<?php echo base_url(); ?>assets/img/2.jpg" alt="Chania"> 
                                <!--<div class="carousel-caption">
                            <h3 style="font-family: 'Raleway', sans-serif; background:url(images/bg.png); padding:15px;">Leveraging technology to solve the most persistent, vexing and seemingly irresolvable challenges</h3>
                           
                          </div>--> 
                            </div>
                            <div class="item"> <img src="<?php echo base_url(); ?>assets/img/3.jpg" alt="Chania"> 
                                <!--<div class="carousel-caption">
                            <h3 style="font-family: 'Raleway', sans-serif; background:url(images/bg.png); padding:15px;">We hold a supreme head. And, he is Mr Customer</h3>
                           
                          </div>--> 
                            </div>
                            <div class="item"> <img src="<?php echo base_url(); ?>assets/img/4.jpg" alt="Chania"> 
                                <!--<div class="carousel-caption">
                            <h3 style="font-family: 'Raleway', sans-serif; background:url(images/bg.png); padding:15px;">We perceive quality as the cardinal right of our customers  </h3>
                           
                          </div>--> 
                            </div>
                            <div class="item"> <img src="<?php echo base_url(); ?>assets/img/5.jpg" alt="Chania"> 
                                <!--<div class="carousel-caption">
                            <h3 style="font-family: 'Raleway', sans-serif; background:url(images/bg.png); padding:15px;">We perceive quality as the cardinal right of our customers  </h3>
                           
                          </div>--> 
                            </div>
                        </div>

                        <!-- Left and right controls --> 

                    </div>
                </div>
            </div>
            <div class="col-sm-4 left-blue">
                <div class="login-box">
                    <div class="login-logo" style="color:#efefef;"> <a href="../../index2.html"><b style="color:#FFF;"><?php echo $companyHrm; ?></a> </div>
                    <!--/.login-logo -->
                    <div class="login-box-body sig-bg">
                        <p class="login-box-msg"><strong>Enter your username </strong></p>
                        <?php
                        if ($this->session->flashdata('flashError')) {
                            ?>
                            <br/>
                            <div class="callout callout-danger">
                                <h4>Error!</h4>
                                <p><?php echo $this->session->flashdata('flashError'); ?>.</p>
                            </div> 
                            <?php
                        }
                        if ($this->session->flashdata('flashSuccess')) {
                            ?>
                            <br/>
                            <div class="callout callout-success">
                                <h4>Success!</h4>
                                <p><?php echo $this->session->flashdata('flashSuccess'); ?>.</p>
                            </div> 
                            <?php
                        }
                        ?>
                        <div class = "text-danger">
                            <?php echo validation_errors();
                            ?>
                        </div>

                        <?php echo form_open(''); ?>
                        <div class="form-group has-feedback">
                            <?php
                            $username_text = array(
                                'name' => 'username',
                                'class' => 'form-control',
                                'placeholder' => 'Username'
                            );
                            $sign_in_button = array(
                                'type' => 'submit',
                                'content' => 'Send Me',
                                'class' => 'btn btn-primary btn-block btn-flat'
                            );
                            ?>
                            <?php echo form_input($username_text) ?>
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        </div>
                        <div class="row">
                            <!-- /.col -->
                            <div class="col-xs-12">
                                <?php echo form_button($sign_in_button) ?>
                            </div>
                            <!-- /.col -->
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
                <!-- /.login-box-body --> 
            </div>
        </div>
        <div class="col-sm-12" style=" background-color:#FFF;">
            <div class="col-sm-8 mid-lt">
                <h3 class="bg-whi"> <span class="lipt">Lorem Ipsum is simply dummy</span></br>
                    The printing and typesetting industry. </h3>
                <p class="au-name"> George Bernard Shaw </p>
            </div>
            <div class="col-sm-3 rt-img"> <img src="<?php echo base_url(); ?>assets/img/hm.jpg" class="img-responsive" style="text-align:right;"> </div>
        </div>
        <div class="col-sm-12 btm-mid">
            <p style="margin-bottom:0px;"><a style="color:#FFF;" href="<?php echo $siteUrl; ?>" target="_blank"><?php echo $siteUrlWithouHttp; ?></a> | 
                <a style="color:#FFF;" href="<?php echo $infoUrl; ?>"><?php echo $infoUrl; ?></a></p>
        </div>
        <!-- /.login-box -->

        <!-- jQuery 2.2.3 -->
        <script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
        <!-- Bootstrap 3.3.6 -->
        <script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
        <!-- iCheck -->
        <script src="<?php echo base_url(); ?>assets/plugins/iCheck/icheck.min.js"></script>
        <script>
            $(function () {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%' // optional
                });
            });
        </script>
    </body>
</html>
