<?php

require_once('../library/AbstractHandler.php');

class AbstractFileHandler extends AbstractHandler
{
    const THUMB_DIR        = 'thumb/';
    const MAX_THUMB_HEIGHT = 150;
    const MAX_THUMB_WIDTH  = 150;
    const UPLOAD_DIR       = 'upload/';
    const GALERIE_DIR      = 'galerie/';

    private $basePath;

    function __construct()
    {
        parent::__construct();
        $this->basePath = realpath(dirname(__FILE__) . '/../../') . '/';
    }

    function getBasePath()
    {
        return $this->basePath;
    }

    function supportsThumbnail($extension)
    {
        return $extension == 'jpg' || $extension == 'gif' || $extension == 'png';
    }

    function checkThumbnail($imagepath, $fname, $extension)
    {
        if ($this->supportsThumbnail($extension)) {
            //check if thumb exists
            if (!$this->thumbExists($imagepath, $fname)) {
                return $this->createThumb($imagepath, $fname);
            }
            return true;
        }
        return false;
    }

    function thumbExists($imagepath, $fname)
    {
        //test if thumb dir exists
        $thumbdir = $imagepath . self::THUMB_DIR;
        if (!file_exists($thumbdir)) {
            //create dir and return false
            mkdir($thumbdir);
            return false;
        } else {
            return file_exists($thumbdir . $fname);
        }
    }

    function purgeOldThumbs($path)
    {
        $thumbdir = $path . self::THUMB_DIR;
        if (!file_exists($thumbdir)) {
            return;
        } else {
            $iterator = new DirectoryIterator($thumbdir);
            foreach ($iterator as $item) {
                if (!$this->isIgnoredFileItem($item)) {
                    if ($item->isFile()) {
                        $fname = (string)$item;
                        if (!file_exists($path . '/' . $fname)) {
                            @unlink($thumbdir . $fname);
                        }
                    }
                }
            }
            if ($this->isEmptyDir($thumbdir)) {
                @rmdir($thumbdir);
            }
        }
    }

    function isEmptyDir($dir)
    {
        return (($files = @scandir($dir)) && count($files) <= 2);
    }

    function createThumb($imagepath, $fname)
    {
        //get image metadata
        $fullpath = $imagepath . $fname;
        $fullthumbpath = $imagepath . self::THUMB_DIR . $fname;
        $size = getimagesize($fullpath);
        $width = $size[0];
        $height = $size[1];
        //compute new sizes
        $newHeight = self::MAX_THUMB_HEIGHT;
        $newWidth = self::MAX_THUMB_WIDTH;
        if ($height < $width) {
            $newHeight = floor($height * $newWidth / $width);
        } else {
            $newWidth = floor($width * $newHeight / $height);
        }

        if ($width > $newWidth && $height > $newHeight) {
            $img = null;
            $newImg = null;
            if ($size[2] == 1) {
                // GIF
                $img = imagecreatefromgif($fullpath);
                $newImg = imagecreate($newWidth, $newHeight);
                imagecopyresampled($newImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagegif($newImg, $fullthumbpath);
            } else if ($size[2] == 2) {
                // JPG
                $img = imagecreatefromjpeg($fullpath);
                $newImg = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($newImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagejpeg($newImg, $fullthumbpath);
            } else if ($size[2] == 3) {
                // PNG
                $img = imagecreatefrompng($fullpath);
                $newImg = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($newImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagepng($newImg, $fullthumbpath);
            }
            if ($img != null) {
                imagedestroy($img);
            }
            if ($newImg != null) {
                imagedestroy($newImg);
            }
        } else {
            //image is already small enough; just copy it
            copy($fullpath, $fullthumbpath);
        }
        // check file rights
        chmod($fullthumbpath, 0777);
        return true;
    }

    function getIconClass($extension)
    {
        $result = 'filebrowser-icon-unknown-file';
        $iconClasses = array(
            '7z' => 'filebrowser-icon-archive-file',
            'aac' => 'filebrowser-icon-audio-file',
            'ai' => 'filebrowser-icon-vector-file',
            'avi' => 'filebrowser-icon-video-file',
            'bmp' => 'filebrowser-icon-image-file',
            'divx' => 'filebrowser-icon-video-file',
            'doc' => 'filebrowser-icon-document-file',
            'eps' => 'filebrowser-icon-vector-file',
            'flac' => 'filebrowser-icon-audio-file',
            'flv' => 'filebrowser-icon-video-file',
            'gif' => 'filebrowser-icon-image-file',
            'jpg' => 'filebrowser-icon-image-file',
            'mov' => 'filebrowser-icon-video-file',
            'mp3' => 'filebrowser-icon-audio-file',
            'mpg' => 'filebrowser-icon-video-file',
            'pdf' => 'filebrowser-icon-acrobat-file',
            'png' => 'filebrowser-icon-image-file',
            'pps' => 'filebrowser-icon-presentation-file',
            'ppt' => 'filebrowser-icon-presentation-file',
            'rar' => 'filebrowser-icon-archive-file',
            'psd' => 'filebrowser-icon-image-file',
            'svg' => 'filebrowser-icon-vector-file',
            'swf' => 'filebrowser-icon-flash-file',
            'tif' => 'filebrowser-icon-image-file',
            'txt' => 'filebrowser-icon-text-file',
            'wav' => 'filebrowser-icon-audio-file',
            'wma' => 'filebrowser-icon-video-file',
            'xls' => 'filebrowser-icon-spreadsheet-file',
            'zip' => 'filebrowser-icon-archive-file'
        );
        if (array_key_exists($extension, $iconClasses)) {
            $result = $iconClasses[$extension];
        }
        return $result;
    }

    function getImageMetadata()
    {
        $picinfo = array();
        getimagesize('lighthouse_spain.jpg', $picinfo);
        if (isset($picinfo['APP13'])) {
            $iptc = iptcparse($picinfo["APP13"]);
            if (is_array($iptc)) {
                $description = $iptc['2#105'][0];
                $time = $iptc['2#055'][0];
                $year = substr($time, 0, 4);
                $month = substr($time, 4, 2);
                $day = substr($time, -2);
                $datetaken = date('l F jS Y', mktime(0, 0, 0, $month, $day, $year));
                $city = $iptc["2#090"][0];
                $country = $iptc["2#101"][0];
                $creator = $iptc["2#080"][0];
            }
        }

        //XMP
        $content = file_get_contents($image);
        $xmp_data_start = strpos($content, '<x:xmpmeta');
        $xmp_data_end = strpos($content, '</x:xmpmeta>');
        $xmp_length = $xmp_data_end - $xmp_data_start;
        $xmp_data = substr($content, $xmp_data_start, $xmp_length + 12);
        $xmp = simplexml_load_string($xmp_data);
    }

    function getXmpData($filename, $chunkSize)
    {
        if (!is_int($chunkSize)) {
            throw new RuntimeException('Expected integer value for argument #2 (chunk_size)');
        }

        if (($file_pointer = fopen($filename, 'r')) === FALSE) {
            throw new RuntimeException('Could not open file for reading');
        }

        $startTag = '<x:xmpmeta';
        $endTag = '</x:xmpmeta>';
        $buffer = NULL;
        $hasXmp = FALSE;

        while (($chunk = fread($file_pointer, $chunkSize)) !== FALSE) {
            if ($chunk === "") {
                break;
            }

            $buffer .= $chunk;
            $startPosition = strpos($buffer, $startTag);
            $endPosition = strpos($buffer, $endTag);

            if ($startPosition !== FALSE && $endPosition !== FALSE) {
                $buffer = substr($buffer, $startPosition, $endPosition - $startPosition + 12);
                $hasXmp = TRUE;
                break;
            } elseif ($startPosition !== FALSE) {
                $buffer = substr($buffer, $startPosition);
                $hasXmp = TRUE;
            } elseif (strlen($buffer) > (strlen($startTag) * 2)) {
                $buffer = substr($buffer, strlen($startTag));
            }
        }

        fclose($file_pointer);
        return ($hasXmp) ? $buffer : NULL;
    }

    function isIgnoredFileItem($item)
    {
        //ignore thumb dirs and synology's '@eaDir'
        $fname = (string)$item;
        return $item->isDot() || $item->isLink() || ($fname . '/') == self::THUMB_DIR || $fname == '@eaDir';
    }

    function getFiles($relativePath, $onlyImages)
    {
        $browsePath = $this->getBasePath() . $relativePath;

        $files = array();
        $iterator = new DirectoryIterator($browsePath);
        foreach ($iterator as $item) {
            if (!$this->isIgnoredFileItem($item)) {
                if ($item->isFile()) {
                    $this->addFileToResult($files, $browsePath, $relativePath, $item, $onlyImages);
                } else if ($item->isDir()) {
                    $this->addFolderToResult($files, $relativePath, $item);
                }
                //item type 'link' is ignored
            }
        }

        //Sort files by year (newest first)
        krsort($files);
        $this->purgeOldThumbs($browsePath);
        return array_values($files);
    }

    protected function addFileToResult(&$files, $browsePath, $relativePath, $item, $onlyImages)
    {
        $fname = (string)$item;
        $ext = strtolower(pathinfo($item->getFilename(), PATHINFO_EXTENSION));
        $hasThumbnail = $this->checkThumbnail($browsePath, $fname, $ext);
        if (!$onlyImages || $hasThumbnail) {
            $files[$fname] = array(
                'path' => $relativePath . $fname,
                'text' => $fname,
                'leaf' => $item->isFile(),
                'size' => $item->getSize(),
                'iconCls' => $this->getIconClass($ext),
                'extension' => $ext,
                'date_modified' => $item->getMTime(),
                'has_thumbnail' => $hasThumbnail
            );
        }
    }

    protected function addFolderToResult(&$files, $relativePath, $item)
    {
        $fname = (string)$item;
        $files[$fname] = array(
            'path' => $relativePath . $fname . '/',
            'text' => $fname,
            'leaf' => false,
            'size' => null,
            'iconCls' => null,
            'extension' => null,
            'date_modified' => $item->getMTime(),
            'has_thumbnail' => false
        );
    }
}

?>