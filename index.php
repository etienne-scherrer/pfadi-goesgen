<?php
include('data/page/definitions.php');
$activePage = 'index';
?>
<!DOCTYPE html SYSTEM "about:legacy-compat">
<html xmlns="http://www.w3.org/1999/xhtml" manifest="cache.appcache" lang="de">
<head>
<?php include('data/page/header.php'); ?>
    <script type="text/javascript" src="js/index.js"></script>
    <title>Pfadi Gösgen</title>
    <style type="text/css">
        #short_news {
            position: relative;
            top: 200px;
            left: -80px;
            border-spacing: 40px;
        }
    </style>
</head>
<body class="nihilo">
<?php include('data/page/logo.php'); ?>
<div id="mitte">
<?php include('data/page/navigation.php'); ?>
    <table id="short_news">
        <tr>
            <td>
                <div id="info_kasten_news" class="info_kasten"><!--  News --><h1
                        ondblclick="$('#news').toggle('blind', null, 500 );">News</h1>

                    <div id="news">
                        <ul>
                            <li>Laden...</li>
                        </ul>
                    </div>
                </div>
            </td>
            <td>
                <div id="info_kasten_pfadi" class="info_kasten"><h1
                        ondblclick="$('#pfadi').toggle('blind', null, 500 );">Pfadi</h1>

                    <div id="pfadi">
                        <ul>
                            <li class="pointer">
                                <div><p>Laden...</p></div>
                            </li>
                        </ul>
                    </div>
                </div>
            </td>
            <td>
                <div id="info_kasten_woelfe" class="info_kasten"><h1
                        ondblclick="$('#woelfe').toggle('blind', null, 500 );">Wölfe</h1>

                    <div id="woelfe">
                        <ul>
                            <li class="pointer">
                                <div><p>Laden...</p></div>
                            </li>
                        </ul>
                    </div>
                </div>
            </td>
            <td>
                <div id="info_kasten_biber" class="info_kasten"><h1
                        ondblclick="$('#biber').toggle('blind', null, 500 );">Biber</h1>

                    <div id="biber">
                        <ul>
                            <li class="pointer">
                                <div><p>Laden...</p></div>
                            </li>
                        </ul>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <div id="content2">
        <div class="content_abstand"><br/></div>
    </div>
</div>
<?php include('data/page/footer.php') ?>
</body>
</html>