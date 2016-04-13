<?php
include('data/page/definitions.php');
$activePage = 'galerie';
?>
<!DOCTYPE html SYSTEM "about:legacy-compat">
<html xmlns="http://www.w3.org/1999/xhtml" manifest="cache.appcache" lang="de">
<head>
    <?php include('data/page/header.php'); ?>
    <script type="text/javascript" src="js/galerie.js"></script>
    <style type="text/css">@import url("css/galerie.css");</style>
    <title>Pfadi Gösgen :: Galerie</title>
</head>
<body class="nihilo">
<?php include('data/page/logo.php'); ?>
<div id="mitte">
    <?php include('data/page/navigation.php'); ?>
    <div id="content">
        <div>
            <table id="galerien">
                <tbody>
                <tr>
                    <td><h1>Lade Galerien...</h1></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="content_abstand"><br/></div>
    </div>
    <div class="content_abstand"><br/></div>
</div>
<?php include('data/page/footer.php') ?>
</body>
</html>