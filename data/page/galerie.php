<?php

require_once('../library/AbstractFileHandler.php');

class GalerieHandler extends AbstractFileHandler {

  function isModeGalerien() {
    return !isset($_GET['path']);
  }

  function getGalerien() {
    return $this->getFiles(self::UPLOAD_DIR . self::GALERIE_DIR, false);
  }

  function getGalerie() {
    return $this->getFiles($_GET['path'], true);
  }

  protected function addFileToResult(&$files, $browsePath, $relativePath, $item, $onlyImages){
    if(!$this->isModeGalerien()){
      $fname = (string) $item;
      $ext = strtolower(pathinfo($item->getFilename(), PATHINFO_EXTENSION));
      $hasThumbnail = $this->checkThumbnail($browsePath, $fname, $ext);
      if(!$onlyImages || $hasThumbnail){
        if($ext)
        $files[$fname] = array(
                'href' => $relativePath . $fname,
                'title' => ''//$fname ; Hier koennte man einen Bezeichnung fuer ein Bild setzen. Z.B. aus den Metadaten (IPCT / XMP)
        );
      }
    }
  }

  protected function addFolderToResult(&$files, $relativePath, $item){
    if($this->isModeGalerien()){
      $fname = $item->getFilename();
      $path = $relativePath . $fname . '/';
      $innerBrowsePath = $this->getBasePath() . $path;
      $headImgName = null;
      $count = 0;
      $iterator = new DirectoryIterator($innerBrowsePath);
      foreach($iterator as $innerItem) {
        if(!$this->isIgnoredFileItem($innerItem) && $innerItem->isFile()){
          $innerfname = $innerItem->getFilename();
          $ext = strtolower(pathinfo($innerfname, PATHINFO_EXTENSION));
          $hasThumbnail = $this->checkThumbnail($innerBrowsePath, $innerfname, $ext);
          if($hasThumbnail){
            $count=$count+1;
            if($headImgName == null || strcasecmp($innerfname, $headImgName) < 0){
              $headImgName = $innerfname;
            }
          }
        }
      }
      //compute sort sting; try to find a year
      $sortstring=$fname;
      preg_match('/\b20[0-9]{2}\b/', $sortstring, $matches);
      if($matches && count($matches)>0){
        $sortstring=$matches[0] . $fname;
      }
      $files[$sortstring] = array(
              'path' => $path,
              'name' => $fname,
              'count' => $count,
              'head_img' => $path . $headImgName
      );
    }
  }

  protected function execHandle(){
    $result = null;
    if($this->isModeGalerien()){
      $result = $this->getGalerien();
    }
    else {
      $result = $this->getGalerie();
    }
    $this->returnSuccess($result);
  }
}

$handler = new GalerieHandler();
$handler->handle();
?>