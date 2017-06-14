<?php

require_once('../library/AbstractFileHandler.php');

class UploadHandler extends AbstractFileHandler
{

    /*
     * Simple method to recieve file from FormDataUploader (multipart upload)
    */
    function saveFilesMultipart($folder)
    {
        $fileName = '';
        $mimeType = '';
        $fileSize = 0;

        if (empty($_FILES)) {
            throw new Exception("Keine Dateien erhalten. Möglicherweise ist die Datei zu gross. Maximale Grösse: " . ini_get('upload_max_filesize'));
        }

        foreach ($_FILES as $fileName => $fileData) {
            if ($fileData['error'] !== 0) {
                throw new Exception(sprintf("Upload Fehler '%d'", $fileData['error']));
            }

            $fileName = self::_sanitizeFilename($fileData['name']);
            $mimeType = $fileData['type'];
            $fileSize = $fileData['size'];

            $targetFile = $this->getBasePath() . $folder . $fileName;
            if (!move_uploaded_file($fileData['tmp_name'], $targetFile)) {
                throw new Exception('Fehler bei Speichern der hochgeladenen Datei.');
            }
        }

        $this->logger->info(sprintf("[multipart] Uploaded %s, %s, %d byte(s)", $fileName, $mimeType, $fileSize));
        $this->returnSuccess('OK');
    }

    /*
     * Processing of raw PUT/POST uploaded files.
    */
    function saveFilesRawUpload($folder)
    {
        $mimeType = htmlspecialchars($_SERVER['HTTP_X_FILE_TYPE']);
        $size     = intval($_SERVER['HTTP_X_FILE_SIZE']);
        $fileName = self::_sanitizeFilename($_SERVER['HTTP_X_FILE_NAME']);

        $inputStream = fopen('php://input', 'r');
        $targetFile  = $this->getBasePath() . $folder . $fileName;
        $realSize    = 0;

        if ($inputStream) {
            $outputStream = fopen($targetFile, 'w');
            if (!$outputStream) {
                throw new Exception('Fehler beim Erstellen der lokalen Datei ' . $folder . $fileName);
            }

            while (!feof($inputStream)) {
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

    /**
     * @param  string $filename
     * @param  bool   $toLower
     * @return string
     */
    private static function _sanitizeFilename($filename, $toLower = false)
    {
        if ($toLower) {
            $filename = strtolower($filename);
        }

        // Convert space to underscore, remove single- and double- quotes
        $filename = str_replace([' ', '\'', '"'], ['_', '', ''], $filename);

        $filename = strtr($filename, [
            'Š' => 'S', 'Ð' => 'DJ', 'Ž' => 'Z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Å' => 'A', 'Æ' => 'A',
            'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U',
            'Ý' => 'Y', 'Þ' => 'B', 'Ÿ' => 'Y', 'Ƒ' => 'F',

            'š' => 's', 'ð' => 'dj', 'ž' => 'z', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a', 'æ' => 'a',
            'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u',
            'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f',

            'Ä' => 'Ae', 'Ö' => 'Oe', 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'Ü' => 'Ue',

            'ß' => 'ss'
        ]);

        // Convert & to "and", @ to "at", and # to "number"
        $filename = str_replace(['&', '@', '#'], ['_und_', '_at_', '_nummer_'], $filename);

        // Remove non-word chars (leaving hyphens, periods and underscores)
        $filename = preg_replace('/[^\w\-\.\_]+/', '', $filename);

        // Convert groups of underscores into one
        $filename = preg_replace('/[\_]+/', '_', $filename);

        return static::_reduceCharRepetitions($filename, ['.', '_', '-']);
    }

    /**
     * Reduce all repetitions of the given character(s) inside the given string to a single occurrence
     *
     * @param  string       $string
     * @param  string|array $characters
     * @return string
     */
    private static function _reduceCharRepetitions($string, $characters)
    {
        if (is_array($characters)) {
            foreach ($characters as $currentCharacter) {
                $string = static::_reduceCharRepetitions($string, $currentCharacter);
            }
        } else {
            $double = $characters . $characters;
            while (strpos($string, $double) !== false) {
                $string = str_replace($double, $characters, $string);
            }
        }

        return $string;
    }
}

$handler = new UploadHandler();
$handler->handle();