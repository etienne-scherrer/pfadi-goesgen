<?php

require_once('../library/AbstractFileHandler.php');

class FilesHandler extends AbstractFileHandler
{

    function updateFileOrFolder($request)
    {
        $requestedPath = $request->data['path'];
        $parentPath    = $request->data['parentId'];
        $oldName       = basename($requestedPath);
        $newName       = basename($request->data['text']);
        if ($oldName !== $newName) {
            //a rename request
            $this->renameFileOrFolder($request, $requestedPath, $oldName, $newName);
        } else if ($requestedPath === $parentPath . $oldName . '/') {
            //extjs informs also the parent nodes of an update. These requests can be ignored
            $this->returnSuccess(null, "nop");
        } else {
            //a move request
            $this->moveFile(false, $requestedPath, $parentPath);
        }
    }

    function renameFileOrFolder($request, $requestedPath, $oldName, $newName)
    {
        $filePath    = $this->getBasePath() . $requestedPath;
        $newFilePath = dirname($filePath) . '/' . $newName;
        $isFile      = is_file($filePath);
        $isDir       = is_dir($filePath);
        if (!file_exists($filePath)) {
            throw new Exception("Die Datei oder der Ordner '" . htmlspecialchars($oldName) . "' existiert nicht");
        }
        if (!$isFile && !$isDir) {
            throw new Exception("'" . htmlspecialchars($oldName) . "' weder eine Datei noch ein Ordner");
        }
        if (!is_writable($filePath)) {
            $msgPart = "die Datei '";
            if ($isDir) {
                $msgPart = "der Ordner '";
            }
            throw new Exception("Sie habe nicht die Berechtigung " . $msgPart . basename($filePath) . "' zu ändern");
        }
        if (file_exists($newFilePath)) {
            throw new Exception("'" . htmlspecialchars($oldName) . "' kann nicht umbennant werden. Eine Datei oder ein Ordner mit diesem Namen existiert bereits");
        }
        if (!@rename($filePath, $newFilePath)) {
            throw new Exception("'" . basename($filePath) . "' konnte aus unbekannten Gründen nicht umbenannt werden");
        }
        //update path variable
        $request->data['path'] = dirname($requestedPath) . '/' . $newName;
        if ($isDir) {
            $request->data['path'] = $request->data['path'] . '/';
        }
        $this->returnSuccess($request->data, "Datei oder Ordner umbenannt");
    }

    function downloadFile($request)
    {
        $downloadPath = $this->getBasePath() . $request->data['path'];

        try {
            if (!file_exists($downloadPath)) {
                throw new Exception("Die Datei '" . basename($downloadPath) . "' existiert nicht");
            }
            if (!is_file($downloadPath)) {
                throw new Exception("'" . basename($downloadPath) . "' ist keine Datei");
            }
            if (!is_readable($downloadPath)) {
                throw new Exception("Sie habe keine Berechtigung die Datei '" . basename($downloadPath) . "' herunterzuladen", 1);
            }

            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Description: File Transfer');
            header('Content-Type: application/force-download');
            header('Content-Disposition: attachment; filename="' . basename($downloadPath) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($downloadPath));

            readfile($downloadPath);
        } catch (Exception $e) {
            if ($e->getCode() === 1) {
                header('HTTP/1.1 403 Forbidden');
            } else {
                header('HTTP/1.1 404 Not Found');
            }
            echo $e->getMessage();
        }
    }

    function createFolder($request)
    {
        $requestedPath = $request->data['parentId'] . $request->data['text'];
        $parentPath    = $this->getBasePath() . $request->data['parentId'];
        $createPath    = $this->getBasePath() . $requestedPath;

        if (!file_exists($parentPath)) {
            throw new Exception("Konnte '" . htmlspecialchars($requestedPath) . "' nicht erstellen, der übergeordnete Ordner existiert nicht");
        }
        if (!is_dir($parentPath)) {
            throw new Exception("Konnte '" . htmlspecialchars($requestedPath) . "' nicht erstellen, der übergeordnete Ordner ist kein Verzeichnis");
        }
        if (!is_writable($parentPath)) {
            throw new Exception("Sie haben keine Berechtigung '" . htmlspecialchars($createPath) . "' zu erstellen");
        }
        if (file_exists($createPath)) {
            throw new Exception("Konnte '" . htmlspecialchars($createPath) . "' nicht erstellen. Eine Datei oder ein Ordner mit diesem Namen existiert bereits");
        }
        if (!@mkdir($createPath)) {
            throw new Exception("Konnte '" . htmlspecialchars($requestedPath) . "' aus unbekannten Gründen nicht erstellen");
        }
        //update path variable
        $request->data['path'] = $requestedPath . '/';
        $this->returnSuccess($request->data, "Ordner erstellt");
    }

    function deleteFileOrFolder($request)
    {
        $filePath = $this->getBasePath() . $request->data['path'];
        $isFile   = is_file($filePath);
        $isDir    = is_dir($filePath);
        if (!file_exists($filePath)) {
            throw new Exception("Die Datei oder der Ordner '" . basename($filePath) . "' existiert nicht");
        }
        if (!$isFile && !$isDir) {
            throw new Exception("'" . htmlspecialchars($filePath) . "' weder eine Datei noch ein Ordner");
        }
        if (!is_writable($filePath)) {
            $msgPart = "die Datei '";
            if ($isDir) {
                $msgPart = "der Ordner '";
            }
            throw new Exception("Sie habe nicht die Berechtigung " . $msgPart . basename($filePath) . "' zu löschen");
        }
        if ($isFile) {
            if (!@unlink($filePath)) {
                throw new Exception("Die Datei '" . basename($filePath) . "' konnte aus unbekannten Gründen nicht gelöscht werden");
            }
        } else {
            if (!$this->isEmptyDir($filePath)) {
                //try first to purge thumbnails
                $this->purgeOldThumbs($filePath);
                if (!$this->isEmptyDir($filePath)) {
                    throw new Exception("Der Ordner '" . basename($filePath) . "' ist nicht leer");
                }
            }
            if (!@rmdir($filePath)) {
                throw new Exception("Der Ordner '" . basename($filePath) . "' konnte aus unbekannten Gründen nicht gelöscht werden");
            }
        }
        $this->returnSuccess(null, "Datei oder Ordner gelöscht.");
    }

    function moveFiles($destinationFolder, $request)
    {
        $files     = $request->data;
        $overwrite = false;

        foreach ($files as $file) {
            $from = $file['path'];
            $this->moveFile($overwrite, $from, $destinationFolder);
        }

        $this->returnSuccess(null, "Dateien verschoben");
    }

    protected function moveFile($overwrite, $from, $destinationFolder)
    {
        $destinationFolder = $this->getBasePath() . $destinationFolder;
        $from              = $this->getBasePath() . $from;
        $to                = $destinationFolder . '/' . basename($from);
        if (file_exists($from) == false) {
            throw new Exception("Die Datei '" . $from . "' existiert nicht");
        }
        if (!is_file($from)) {
            throw new Exception("'" . $from . "' ist keine Datei");
        }
        if (!is_readable($from)) {
            throw new Exception("'" . $from . "' kann nicht gelesen werden");
        }
        if (!file_exists($destinationFolder)) {
            throw new Exception("Der Zielordner '" . $destinationFolder . "' existiert nicht");
        }
        if (!is_dir($destinationFolder)) {
            throw new Exception("Der Zielordner '" . $destinationFolder . "' ist kein Verzeichnis");
        }
        if (!is_writable($destinationFolder)) {
            throw new Exception("Sie haben keine Berechtigung Dateien nach '" . $destinationFolder . "' zu verschieben");
        }

        if ($overwrite === true) {
            if (file_exists($to) && !is_writable($to)) {
                throw new Exception("Sie haben keine Berechtigung die Datei '" . $to . "' zu überschreiben");
            }
        } else {
            if (file_exists($to)) {
                throw new Exception("Die Datei '" . $from . "' kann nicht verschoben werden. Eine Datei mit dem selben Namen existiert in dem Zielordner", 1);
            }
        }

        if (!@rename($from, $to)) {
            throw new Exception("Datei '" . $from . "' konnte aus unbekannten Gründen nicht verschoben werden");
        }

        $this->returnSuccess(null, "Datei verschoben");
    }

    protected function execHandle()
    {
        parent::execHandle();
        switch ($_SERVER["REQUEST_METHOD"]) {
            case "GET": {
                $this->returnSuccess($this->getFiles($_GET['node'], isset($_GET['only_images'])));
                break;
            }
            case "PUT": {
                $this->createFolder(new Request());
                break;
            }
            case "POST": {
                $this->updateFileOrFolder(new Request());
                break;
            }
            case "DELETE": {
                $this->deleteFileOrFolder(new Request());
                break;
            }
        }
    }
}

$handler = new FilesHandler();
$handler->handle();
?>