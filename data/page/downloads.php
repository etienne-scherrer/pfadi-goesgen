<?php

require_once('../library/AbstractFileHandler.php');

class DownloadsHandler extends AbstractFileHandler
{
    function getDownloadFiles()
    {
        return $this->getFiles(self::UPLOAD_DIR . self::DOWNLOADS_DIR, false);
    }

    protected function addFileToResult(&$files, $browsePath, $relativePath, $item, $onlyImages)
    {
        $fileName  = (string)$item;
        $extension = strtolower(pathinfo($item->getFilename(), PATHINFO_EXTENSION));
        if ($extension) {
            $files[$fileName] = [
                'href'  => $relativePath . $fileName,
                'title' => $fileName
            ];
        }
    }

    protected function addFolderToResult(&$files, $relativePath, $item)
    {
        $filename         = $item->getFilename();
        $path             = $relativePath . $filename . '/';
        $headImgName      = null;
        $count            = 0;
        $files[$filename] = [
            'path'     => $path,
            'name'     => $filename,
            'count'    => $count,
            'head_img' => $path . $headImgName
        ];
    }

    protected function execHandle()
    {
        $result = null;
        $result = $this->getDownloadFiles();
        $this->returnSuccess($result);
    }
}

$handler = new DownloadsHandler();
$handler->handle();