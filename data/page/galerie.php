<?php

require_once('../library/AbstractFileHandler.php');

class GalerieHandler extends AbstractFileHandler
{

    function isModeGalerien()
    {
        return !isset($_GET['path']);
    }

    function getGalerien()
    {
        return $this->getFiles(self::UPLOAD_DIR . self::GALERIE_DIR, false);
    }

    function getGalerie()
    {
        return $this->getFiles($_GET['path'], true);
    }

    protected function addFileToResult(&$files, $browsePath, $relativePath, $item, $onlyImages)
    {
        if (!$this->isModeGalerien()) {
            $fileName     = (string)$item;
            $extension    = strtolower(pathinfo($item->getFilename(), PATHINFO_EXTENSION));
            $hasThumbnail = $this->checkThumbnail($browsePath, $fileName, $extension);
            if (!$onlyImages || $hasThumbnail) {
                if ($extension)
                    $files[$fileName] = [
                        'src'  => $relativePath . $fileName
                    ];
            }
        }
    }

    protected function addFolderToResult(&$files, $relativePath, $item)
    {
        if ($this->isModeGalerien()) {
            $filename        = $item->getFilename();
            $path            = $relativePath . $filename . '/';
            $innerBrowsePath = $this->getBasePath() . $path;
            $headImgName     = null;
            $count           = 0;
            $iterator        = new DirectoryIterator($innerBrowsePath);
            foreach ($iterator as $innerItem) {
                if (!$this->isIgnoredFileItem($innerItem) && $innerItem->isFile()) {
                    $innerFileName = $innerItem->getFilename();
                    $extension     = strtolower(pathinfo($innerFileName, PATHINFO_EXTENSION));
                    $hasThumbnail  = $this->checkThumbnail($innerBrowsePath, $innerFileName, $extension);
                    if ($hasThumbnail) {
                        $count = $count + 1;
                        if ($headImgName == null || strcasecmp($innerFileName, $headImgName) < 0) {
                            $headImgName = $innerFileName;
                        }
                    }
                }
            }
            //compute sort sting; try to find a year
            $sortString = $filename;
            preg_match('/\b20[0-9]{2}\b/', $sortString, $matches);
            if ($matches && count($matches) > 0) {
                $sortString = $matches[0] . $filename;
            }
            $files[$sortString] = [
                'path'     => $path,
                'name'     => $filename,
                'count'    => $count,
                'head_img' => $path . $headImgName
            ];
        }
    }

    protected function execHandle()
    {
        $result = null;
        if ($this->isModeGalerien()) {
            $result = $this->getGalerien();
        } else {
            $result = $this->getGalerie();
        }
        $this->returnSuccess($result);
    }
}

$handler = new GalerieHandler();
$handler->handle();
?>