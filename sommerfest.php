<?php
include('data/page/definitions.php');
$activePage = 'sommerfest';
?>
<!DOCTYPE html SYSTEM "about:legacy-compat">
<html xmlns="http://www.w3.org/1999/xhtml" manifest="cache.appcache">
<head>
    <?php include('data/page/header.php'); ?>
    <script type="text/javascript" src="js/sommerfest.js"></script>
    <title>Pfadi Gösgen :: Sommerfest</title>
</head>
<body>
<?php include('data/page/logo.php'); ?>
<div id="mitte">
    <?php include('data/page/navigation.php'); ?>
    <div id="info_kasten"></div>
    <div id="content">
        <div>
            <ul>
                <li>Laden...</li>
            </ul>
        </div>
        <div class="content_abstand"></div>
    </div>
</div>
<?php include('data/page/footer.php'); ?>
</body>
</html>