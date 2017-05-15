<?php
require_once('Zend/Config.php');
require_once('Zend/Config/Ini.php');
include('data/page/definitions.php');
$activePage = 'index';
$config     = (new Zend_Config_Ini('data/config.ini'))->toArray();
?>
<!DOCTYPE html SYSTEM "about:legacy-compat">
<html xmlns="http://www.w3.org/1999/xhtml" manifest="cache.appcache" lang="de">
<head>
    <?php include('data/page/header.php'); ?>
    <script type="text/javascript" src="js/index.js"></script>
    <style type="text/css">@import url("css/index.css");</style>
    <title>Pfadi Gösgen</title>
</head>
<body class="nihilo">
<?php include('data/page/logo.php'); ?>
<div id="mitte">
    <?php include('data/page/navigation.php');
    $bannerContent = preg_replace('/<!--.*?-->/s', '', file_get_contents('data/page/banner.phtml'));
    if (!empty($bannerContent)) { ?>
        <div id="banner-news">
            <div id="banner-news-content"><?php echo $bannerContent ?></div>
        </div>
    <?php } ?>
    <div id="short_news">
        <table>
            <tr>
                <td>
                    <div id="info_kasten_news" class="info_kasten"><!--  News -->
                        <h1>News</h1>
                        <div id="news">
                            <ul>
                                <li>Laden...</li>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div id="short_infos">
        <table>
            <tr>
                <td>
                    <div id="info_kasten_pfadi" class="info_kasten">
                        <h1>Pfadi</h1>
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
                    <div id="info_kasten_woelfe" class="info_kasten">
                        <h1>Wölfe</h1>
                        <div id="woelfe">
                            <ul>
                                <li class="pointer">
                                    <div><p>Laden...</p></div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div id="info_kasten_biber" class="info_kasten">
                        <h1>Biber</h1>
                        <div id="biber">
                            <ul>
                                <li class="pointer">
                                    <div><p>Laden...</p></div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    <div id="info_kasten_pios" class="info_kasten">
                        <h1>Pios</h1>
                        <div id="pios">
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
    </div>
    <div id="content2">
        <div class="content_abstand"><br/></div>
    </div>
</div>
<?php if ((int)$config['page']['important']['enabled'] === 1) { ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#important-news-window-content').css({"background": "url(bilder/layout/<?php echo $config['page']['important']['background'] ?>) no-repeat fixed center center / cover"});
        });
    </script>
    <div id="important-news-window">
        <div id="important-news-window-content">
            <div id="loaded-news-content">
                <?php include('data/page/important.phtml'); ?>
            </div>
        </div>
    </div>
<?php }
include('data/page/footer.php'); ?>
</body>
</html>