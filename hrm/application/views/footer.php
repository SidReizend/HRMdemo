</div>
<!-- ./wrapper -->
</div>
<!-- /.content-wrapper -->
<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Version</b> 2.0
    </div>
    <strong>Copyright &copy; 2022-2023 <a  target="_blank" href="<?php echo $ci->defaultValues->getValue(20); ?>"><?php echo $ci->defaultValues->getValue(21); ?></a>.</strong> All rights
    reserved.
</footer>
<?php
if (isset($recentLogDetails)) {
    ?>
    <aside class="control-sidebar control-sidebar-dark" id="elemtId" style="display:none;">

        <div class="tab-content">
            Home tab content 
            <div id="control-sidebar-home-tab">
                <h3 class="control-sidebar-heading">Recent Logins</h3>
                <ul class="control-sidebar-menu">
                    <?php
                    if (isset($recentLogDetails)) {
                        foreach ($recentLogDetails as $recLog) {
                            ?>
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="menu-icon fa fa-file-code-o bg-green"></i>

                                    <div class="menu-info">
                                        <h4 class="control-sidebar-subheading"><?php echo $recLog['ip'] . " " . $recLog['platform'] . " " . $recLog['mobile'] . " " . $recLog['robot']; ?></h4>

                                        <p><?php echo $recLog['logged_in_time']; ?></p>
                                        <p><?php echo $recLog['browser'] . " " . $recLog['version']; ?></p>
                                        <p><?php echo $recLog['referer']; ?></p>
                                    </div>
                                </a>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>

            </div>
        </div>
    </aside>
    <?php
}
?>
<div class="control-sidebar-bg"></div>

<!-- jQuery 2.2.3 -->
<script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery.cookie.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/moment/moment.min.js"></script>
<!-- SlimScroll -->
<script src="<?php echo base_url(); ?>assets/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo base_url(); ?>assets/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url(); ?>assets/dist/js/app.js"></script>

<script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap-slider.js"></script>
<script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap-slider.min.js"></script>

<?php
if (isset($datatables)) {
    ?>
    <!-- DataTables -->
    <script src="<?php echo base_url(); ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.bootstrap.min.js"></script>
    <?php
}
?>
<!--Alertify-->
<?php //if (isset($alertify)) {  ?>
<script src="<?php echo base_url(); ?>assets/plugins/alertify/alertify.min.js"></script>
<?php //}  ?>
<?php
if (isset($select2)) {
    ?>
    <!-- DataTables -->
    <script src="<?php echo base_url(); ?>assets/plugins/select2/select2.full.min.js"></script>
    <?php
}
?>
<?php
if (isset($form_validator)) {
    ?>
    <!-- DataTables -->
    <script src="<?php echo base_url(); ?>assets/plugins/jquery-form-validator/form-validator/jquery.form-validator.min.js"></script>
    <?php
}
?>
<?php
if (isset($form_validator2)) {
    ?>
    <!-- Select2 -->
    <script src="<?php echo base_url(); ?>assets/plugins/validation/jquery.validate.min.js"></script>
    <?php
}
?>
<?php
if (isset($datepicker)) {
    ?>
    <!-- bootstrap datepicker -->
    <script src="<?php echo base_url(); ?>assets/plugins/datepicker/bootstrap-datepicker.js"></script>
    <?php
}
?>
<?php
if (isset($full_calendar)) {
    ?>
    <script src="<?php echo base_url(); ?>assets/plugins/fullcalendar/fullcalendar.min.js"></script>
    <?php
}
?>
<script src="<?php echo base_url(); ?>assets/plugins/fullcalendar/fullcalendar.min.js"></script>

<?php
if (isset($wysihtml5)) {
    ?>
    <!-- Bootstrap WYSIHTML5 -->
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/tinymce/js/tinymce/tinymce.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery.form.js"></script>
    <!--<script src="<?php //echo base_url();    ?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>-->
    <?php
}
?>
<?php
if (isset($timepicker)) {
    ?>
    <script src="<?php echo base_url(); ?>assets/plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <?php
}
?>
<?php
if (isset($chartjs)) {
    ?>
    <script src="<?php echo base_url(); ?>assets/plugins/chartjs/Chart.min.js"></script>
    <?php
}
?>
</body>
</html>
<?php
if (isset($datatables)) {
    ?>
    <script>
        $('#table_responsive').DataTable();
    </script>
    <?php
}
?>

<script>
    jQuery(document).ready(function () {
        $("body").on("click", ".recent_login", function (e) {
            var btn = $(this);
            if (btn.hasClass("selected")) {
                btn.removeClass("selected");
                document.getElementById('elemtId').style.display = 'none';
                $(".control-sidebar").removeClass("control-sidebar-open");

            } else {
                btn.addClass("selected");
                document.getElementById('elemtId').style.display = 'block';
                $(".control-sidebar").addClass("control-sidebar-open");

            }
        });

        base_url = '<?php echo base_url(); ?>';
        $.ajax({
            url: base_url + "index.php/notification/checknew",
        }).done(function (data) {
            if (data > 0) {
                $(".unread_message_count").show();
                $(".unread_message_count").text(data);
                alertify.success("You have " + data + " unread messages!");
            }
        });

        $("#view_messages").click(function () {
            var ths = $(this);
            $.ajax({
                url: base_url + "index.php/notification/getMessages",
                beforeSend: function () {
                    $(ths).find("#show-messages-area").html("<div class=\"text-center\"> <i class=\"fa fa-spin fa-spinner fa-5x\"></i></div>");
                },
            }).done(function (data) {
                $(ths).find("#show-messages-area").html(data);
            });
        });

    });
</script>
<script type="text/javascript" src="https://cdn.ywxi.net/js/1.js" async></script>