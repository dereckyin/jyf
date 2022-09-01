<script src="js/device-uuid.min.js"></script>
<script type="text/javascript" src="js/rm/jquery-3.4.1.min.js"></script>



<script>
    $(document).ready(function() {
        var uuid = new DeviceUUID().get();
        alert(uuid);
});
</script>