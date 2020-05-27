<?php include 'check.php';?>
<!doctype html>
<html class="easy-sidebar-active">
<head>

    <meta charset="utf-8">
    <title>後台管理</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/easy-sidebar.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">

    <script defer src="https://kit.fontawesome.com/a076d05399.js"></script> 
    <script src="../js/rm/jquery-2.2.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>



</head>

<body>



<div class="container " style="margin-left: -0.5vw;width: 100vw; padding-left: 1vw">

    <div class="row" style="background-color: rgb(34,34,34)">

        <a class="btn easy-sidebar-toggle navbar-brand" ><span style="color: white;">&#9776;</span></a>

    </div>


<?php

include 'menu.php';

?>



</div>



<script>
    //easy-sidebar-toggle-right
    $('.easy-sidebar-toggle').click(function(e) {
        e.preventDefault();
        //$('body').toggleClass('toggled-right');
        $('body').toggleClass('toggled');
        //$('.navbar.easy-sidebar-right').removeClass('toggled-right');
        $('.navbar.easy-sidebar').removeClass('toggled');
    });

    $('.dropdownmenu_button').click(function(e) {

        $('.dropdownmenu').toggle();
    });

</script>



</body>
</html>
