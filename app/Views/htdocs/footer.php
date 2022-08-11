
</div>
</section>

<!-- Jquery Core Js -->
<script src="<?=base_url("assets/bundles/libscripts.bundle.js")?>"></script>
<script src="<?=base_url("assets/bundles/vendorscripts.bundle.js")?>"></script> <!-- Lib Scripts Plugin Js -->
<script src="<?=base_url("assets/plugins/bootstrap-notify/bootstrap-notify.js")?>"></script>
<script src="<?=base_url("assets/bundles/mainscripts.bundle.js")?>"></script> <!-- Lib Scripts Plugin Js -->
<script src="<?=base_url("assets/vendor/js/pages/ui/notifications.js")?>"></script>
<script src="<?=base_url("assets/js/custom.js")?>"></script> <!-- Custom Js -->

<?php
if (isset($scripts)) {
    foreach ($scripts as $script) {
        echo '<script src="'.$script.'" type="text/javascript"></script>';
    }//foreach
}//if scripts
?>
</body>