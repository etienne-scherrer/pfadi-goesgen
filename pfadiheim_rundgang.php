<?php
include('data/page/definitions.php');
$activePage = 'pfadiheim_rundgang';
?>
<!DOCTYPE html SYSTEM "about:legacy-compat">
<html xmlns="http://www.w3.org/1999/xhtml" manifest="cache.appcache" lang="de">
<head>
    <?php include('data/page/header.php') ?>
    <script type="text/javascript" src="js/pfadiheim_rundgang.js"></script>
    <style type="text/css">@import url("css/pfadiheim_rundgang.css");</style>
    <title>Pfadi Gösgen :: Pfadiheim</title>
</head>
<body>
<?php include('data/page/logo.php') ?>
<div id="mitte">
    <?php include('data/page/navigation.php') ?>
    <div id="content_breit">
        <div id="content_bilder">
            <div id="galerie">
                <div id="galerie_post_loaded_content"><!-- Content here will be loaded by javascript --></div>
            </div>
        </div>
        <div id="content_beschreibung"></div>
        <div id="content_grundriss">
            <div id="grundriss-bild"><img src="bilder/pfadiheim/grundriss/grundriss-trans.png" alt="" width="550"
                                          height="200"/></div>
            <div class="raum" id="grundriss-aufenthaltsraum" onmouseout="apollomin.local.out()"
                 onmouseover="apollomin.local.showInfo(this)" onclick="apollomin.local.showImages(this)"></div>
            <div class="raum" id="grundriss-schlafraum" onmouseout="apollomin.local.out()"
                 onmouseover="apollomin.local.showInfo(this)" onclick="apollomin.local.showImages(this)"></div>
            <div class="raum" id="grundriss-duschraum" onmouseout="apollomin.local.out()"
                 onmouseover="apollomin.local.showInfo(this)" onclick="apollomin.local.showImages(this)"></div>
            <div class="raum" id="grundriss-wc" onmouseout="apollomin.local.out()"
                 onmouseover="apollomin.local.showInfo(this)" onclick="apollomin.local.showImages(this)"></div>
            <div class="raum" id="grundriss-pfadiraum1" onmouseout="apollomin.local.out()"
                 onmouseover="apollomin.local.showInfo(this)" onclick="apollomin.local.showImages(this)"></div>
            <div class="raum" id="grundriss-pfadiraum2" onmouseout="apollomin.local.out()"
                 onmouseover="apollomin.local.showInfo(this)" onclick="apollomin.local.showImages(this)"></div>
            <div class="raum" id="grundriss-kueche" onmouseout="apollomin.local.out()"
                 onmouseover="apollomin.local.showInfo(this)" onclick="apollomin.local.showImages(this)"></div>
            <div class="raum" id="grundriss-eingang_mieter" onmouseout="apollomin.local.out()"
                 onmouseover="apollomin.local.showInfo(this)" onclick="apollomin.local.showImages(this)"></div>
            <div class="raum" id="grundriss-eingang_pfadi" onmouseout="apollomin.local.out()"
                 onmouseover="apollomin.local.showInfo(this)" onclick="apollomin.local.showImages(this)"></div>
        </div>
        <div class="content_abstand"><br/></div>
    </div>
</div>
<?php include('data/page/footer.php') ?>
</body>
</html>