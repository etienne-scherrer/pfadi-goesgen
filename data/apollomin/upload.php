<?php

require_once('../library/AbstractFileHandler.php');

class UploadHandler extends AbstractFileHandler
{

    /*
     * Simple method to recieve file from FormDataUploader (multipart upload)
    */
    function saveFilesMultipart($folder)
    {
        $fname    = '';
        $mimeType = '';
        $fileSize = 0;

        if (empty($_FILES)) {
            throw new Exception("Keine Dateien erhalten. Möglicherweise ist die Datei zu gross. Maximale Grösse: " . ini_get('upload_max_filesize'));
        }

        foreach ($_FILES as $fname => $fileData) {
            if ($fileData['error'] !== 0) {
                throw new Exception(sprintf("Upload Fehler '%d'", $fileData['error']));
            }

            $fname    = htmlspecialchars($fileData['name']);
            $mimeType = $fileData['type'];
            $fileSize = $fileData['size'];

            $targetFile = $this->getBasePath() . $folder . $fname;
            if (!move_uploaded_file($fileData['tmp_name'], $targetFile)) {
                throw new Exception('Fehler bei Speichern der hochgeladenen Datei.');
            }
        }

        $this->logger->info(sprintf("[multipart] Uploaded %s, %s, %d byte(s)", $fname, $mimeType, $fileSize));
        $this->returnSuccess('OK');
    }

    /*
     * Processing of raw PUT/POST uploaded files.
    */
    function saveFilesRawUpload($folder)
    {
        $mimeType = htmlspecialchars($_SERVER['HTTP_X_FILE_TYPE']);
        $size     = intval($_SERVER['HTTP_X_FILE_SIZE']);
        $fileName = htmlspecialchars($_SERVER['HTTP_X_FILE_NAME']);

        $inputStream = fopen('php://input', 'r');
        $targetFile  = $this->getBasePath() . $folder . $fileName;
        $realSize    = 0;
        $data        = '';

        if ($inputStream) {
            $outputStream = fopen($targetFile, 'w');
            if (!$outputStream) {
                throw new Exception('Fehler beim Erstellen der lokalen Datei ' . $folder . $fileName);
            }

            while (!feof($inputStream)) {
                $bytesWritten = 0;
                $data         = fread($inputStream, 1024);
                $bytesWritten = fwrite($outputStream, $data);

                if (false === $bytesWritten) {
                    fclose($outputStream);
                    throw new Exception('Fehler beim Schreiben von Daten in die Datei' . $folder . $fileName);
                }
                $realSize += $bytesWritten;
            }
            fclose($outputStream);

        } else {
            throw new Exception('Fehler beim Lesen des Inputs');
        }

        if ($realSize != $size) {
            throw new Exception('Die tatsächliche Grösse der Datei entspricht nicht der in den Headern deklarierten Grösse');
        }

        $this->logger->info(sprintf("[raw] Uploaded %s, %s, %d byte(s)", $fileName, $mimeType, $realSize));
        $this->returnSuccess('OK');
    }

    protected function execHandle()
    {
        parent::execHandle();
        switch ($_SERVER["REQUEST_METHOD"]) {
            case "POST": {
                $folder = self::UPLOAD_DIR;
                if (isset($_REQUEST["folder"])) {
                    $tmp = $_REQUEST["folder"];
                    //ensure valid input and starts with 'upload'
                    if ($tmp && strlen($tmp) > 0 && strpos($tmp, self::UPLOAD_DIR) === 0) {
                        $folder = $tmp;
                    }
                }
                //$this->saveFilesMultipart($folder);
                $this->saveFilesRawUpload($folder);
                break;
            }
        }
    }
}

$handler = new UploadHandler();
$handler->handle();