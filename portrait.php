<?php
include('data/page/definitions.php');
$activePage = 'portrait';
?>
<!DOCTYPE html SYSTEM "about:legacy-compat">
<html xmlns="http://www.w3.org/1999/xhtml" manifest="cache.appcache" lang="de">
<head>
    <?php include('data/page/header.php'); ?>
    <script type="text/javascript" src="js/portrait.js"></script>
    <title>Pfadi Gösgen :: Portrait</title>
</head>
<body class="nihilo">
<?php include('data/page/logo.php'); ?>
<div id="mitte">
    <?php include('data/page/navigation.php'); ?>
    <div id="content">
        <div>
            <ul>
                <li>Laden...</li>
            </ul>
        </div>
        <div class="content_abstand"><br/></div>
    </div>
    <div class="content_abstand"><br/></div>
</div>
<?php include('data/page/footer.php'); ?>
</body>
</html>