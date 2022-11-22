<?php
namespace TM;

class FileCtl
{
    private $file_path = null;

    private $file_key = null;

    private $file_name = null;

    private $permit_num = 0755;

    private $chmod_command = false;

    private $file_size = 0;

    private $file_type = [];

    private $make_file_name = null;

    public function setFileKey($value)
    {
        $this->file_key = $value;
    }

    public function setFilePath($value)
    {
        $this->file_path = $value;
    }

    public function setFileName($value)
    {
        $this->file_name = $value;
    }

    public function setMakeFileName($value)
    {
        $this->make_file_name = $value;
    }

    public function setFileSize($value)
    {
        $this->file_size = $value;
    }

    public function setFileType($value)
    {
        $this->file_type = $value;
    }

    public function setPermit($value)
    {
        $this->permit_num = $value;
    }

    public function setChmod($value)
    {
        $this->chmod_command = $value;
    }

    public function openDir($path, $sort)
    {
        $result = [];
        $sort = $sort ? $sort:'k';
        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while (($file_list[] = readdir($dh)) !== false);
                closedir($dh);
                if ($sort == 'k') {
                    sort($file_list);
                }
                if ($sort == 'a') {
                    rsort($file_list);
                }
                foreach ($file_list as $file_name) {
                    if ($file_name != '.htaccess' && $file_name != '.htpasswd' && $file_name != '.' && $file_name != '..' && $file_name != '') {
                        $result[] =
                        [
                            'filename' => mb_convert_encoding($file_name, 'UTF-8', 'AUTO'),
                            'time' => filemtime($path."/" . $file_name),
                            'extension' => pathinfo($file_name, PATHINFO_EXTENSION)
                        ];
                    }
                }
            }
        }

        return $result;
    }

    public function fileExist()
    {
        $result = [];
        if ($this->file_path) {
            if ($handle = @opendir($this->file_path)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != '.' && $file != '..') {
                        if ($this->file_name) {
                            if (preg_match('/^'.$this->file_name.'/', $file)) {
                                $result[] = $file;
                            }
                        } else {
                            $result[] = $file;
                        }
                    }
                }
                closedir($handle);
            }
        }
        return $result;
    }

    public function fileUploadCheck()
    {
        $result = false;
        if ($this->file_key) {
            if (!is_uploaded_file($_FILES[$this->file_key]['tmp_name'])) {
                $result = true;
            } else {
                if ($_FILES[$this->file_key]['size'] > 0 && $_FILES[$this->file_key]['size'] < $this->file_size && $_FILES[$this->file_key]['error'] == 0) {
                    $result = true;
                }
                if (!in_array($_FILES[$this->file_key]['type'], $this->file_type)) {
                    $result = false;
                }
            }
        }
        return $result;
    }

    public function newFileName()
    {
        $result = false;
        if ($this->file_key) {
            $make_name = !empty($this->make_file_name) ? $this->make_file_name:microtime();
            $ext = substr(strrchr($_FILES[$this->file_key]['name'], '.'), 1);
            $result = $make_name.".".$ext;
        }
        return $result;
    }

    public function moveFile($new_file_name)
    {
        $result = false;
        if ($this->file_key && $this->file_path && is_uploaded_file($_FILES[$this->file_key]['tmp_name'])) {
            $new_file_name = $new_file_name ? $new_file_name:$_FILES[$this->file_key]['name'];
            $str = $this->file_path.mb_convert_encoding($new_file_name, 'SJIS', 'UTF8');
            if (move_uploaded_file($_FILES[$this->file_key]['tmp_name'], $str)) {
                if ($this->chmod_command && $this->permit_num) {
                    $permit = ltrim($this->permit_num, '0');
                    exec('chmod '.$permit.' '.$str);
                }
                $result = true;
            }
        } else {
            $result = true;
        }
        return $result;
    }

    public function deleteFile()
    {
        $result = false;
        if ($this->file_path) {
            $result = unlink($this->file_path);
        }
        return $result;
    }

    public function deleteDir()
    {
        $result = false;
        if ($this->file_path && file_exists($this->file_path)) {
            $str_dir = @opendir($this->file_path);
            while ($str_file = @readdir($str_dir)) {
                //ディレクトリでない場合のみ
                if ($str_file != '.' && $str_file != '..') {
                    @unlink($this->file_path.'/'.$str_file);
                }
            }
            $result = rmdir($this->file_path);
        }
        return $result;
    }

    public function unlinkRecursive($this_dir=false)
    {
        $result = false;
        if ($this->file_path) {
            if (!$dh = @opendir($this->file_path)) {
                return;
            }
            while (false !== ($obj = readdir($dh))) {
                if ($obj == '.' || $obj == '..') {
                    continue;
                }
                if (!@unlink($this->file_path . '/' . $obj)) {
                    $this->unlinkRecursive($this->file_path.'/'.$obj, true);
                }
            }
            closedir($dh);
            $result = true;
            if ($this_dir) {
                $result = rmdir($this->file_path);
            }
        }
        return $result;
    }

    public function dirFileName($file_type)
    {
        $result = '';
        if ($this->file_path) {
            if ($handle = opendir($this->file_path)) {
                $result .= '<ul class="dir">';
                while (false !== ($file = readdir($handle))) {
                    $p = pathinfo($file);
                    if ($file_type && $p['extension'] == $file_type) {
                        $result .= '<li><a href="./?file='.$file.'">'.$file.'</li>';
                    } else {
                        $result .= '<li>'.$file.'</li>';
                    }
                }
                closedir($handle);
            }
        }
        return $result;
    }

    public function logFile($log)
    {
        if (!file_exists($this->file_path)) {
            touch($this->file_path);
            chmod($this->file_path, 0755);
        }
        $fp = fopen($this->file_path, "a");
        $result = fwrite($fp, $log);
        fclose($fp);
        return $this->file_path;
    }

    public function zipExec(array $files, $download=true, $delete=false)
    {
        if ($this->file_path && $this->file_name && $files) {
            chdir($this->file_path);
            exec('zip '.$this->file_name.' '.implode(' ', $files));
            if ($download) {
                $this->downloadExec();
            }
            if ($delete) {
                $this->deleteFilesAndZip($files);
            }
        }
    }

    public function deleteFilesAndZip(array $files)
    {
        if ($this->file_path && $this->file_name) {
            foreach ($files as $f) {
                unlink($this->file_path.$f);
            }
            unlink($this->file_path.$this->file_name);
        }
    }

    public function downloadExec()
    {
        if ($this->file_path && $this->file_name) {
            if (!file_exists($this->file_path)) {
                throw new \Exception("Error: File(".$this->file_path.$this->file_name.") does not exist");
            }
            if (!($fp = fopen($this->file_path.$this->file_name, "r"))) {
                throw new \Exception("Error: Cannot open the file(".$this->file_path.$this->file_name.")");
            }
            fclose($fp);
            if (($content_length = filesize($this->file_path.$this->file_name)) == 0) {
                throw new \Exception("Error: File size is 0.(".$this->file_path.$this->file_name.")");
            }
            $mime_type = (new \finfo(FILEINFO_MIME_TYPE))->file($this->file_path.$this->file_name);
            if (!preg_match('/\A\S+?\/\S+/', $mime_type)) {
                $mime_type = 'application/octet-stream';
            }
            header('Content-Disposition: attachment; filename="'.$this->file_name.'"');
            header('Content-Length: '.$content_length);
            header('Content-Type: ' . $mime_type);
            readfile($this->file_path.$this->file_name);
            return $content_length;
        }
    }

    public function downloadStExec(array $data)
    {
        if ($data && $this->file_name) {
            header("Content-Type: application/octet-stream");
        }
        header("Content-Disposition: attachment; filename={$this->file_name}");
        $stream = fopen('php://output', 'w');
        foreach ($data as $row) {
            fputcsv($stream, $row);
        }
        fclose($stream);
        return true;
    }

    private function mkDir($saveDir)
    {
        $result = false;
        if ($saveDir) {
            if (!file_exists($saveDir)) {
                $result = mkdir(mb_convert_encoding($saveDir, 'UTF8'), $this->permit_num, true);
            } else {
                $result = true;
            }
        }
        return $result;
    }

    public function uploadMulti($saveDir, $fileKeyName, $number, $newFileName, $fileMaxSize=0)
    {
        $result = [0, ''];
        if ($fileKeyName && $saveDir && $fileMaxSize > 0 && $_FILES[$fileKeyName]) {
            if (is_uploaded_file($_FILES[$fileKeyName]['tmp_name'][$number])) {
                if ($this->mkDir($saveDir)) {
                    if ($_FILES[$fileKeyName]['size'][$number] > 0 && $_FILES[$fileKeyName]['size'][$number] < $fileMaxSize && $_FILES[$fileKeyName]['error'][$number] == 0) {
                        $result[0] = 1;
                        $result[1] = $this->moveFileMulti($saveDir, $fileKeyName, $number, $newFileName);
                        $ext = $_FILES[$fileKeyName]['name'][$number];
                        $ext = explode('.', $ext);
                        $c = count($ext);
                        $result[2] = $ext[$c-1];
                    } else {
                        $result[0] = 2;
                        $result[1] = 'ファイルサイズを確認して下さい';
                    }
                }
            }
        }
        return $result;
    }

    private function moveFileMulti($saveDir, $fileKeyName, $number, $newFileName)
    {
        $result = false;
        if ($saveDir && $fileKeyName) {
            if ($newFileName) {
                $ext = pathinfo($newFileName, PATHINFO_EXTENSION);
                $ori = pathinfo($_FILES[$fileKeyName]['name'][$number], PATHINFO_EXTENSION);
                $newFileName = empty($ext) ? $newFileName.'.'.$ori:$newFileName;
            }
            $newFileName = $newFileName ? $newFileName:$_FILES[$fileKeyName]['name'][$number];
            if (move_uploaded_file($_FILES[$fileKeyName]['tmp_name'][$number], $saveDir.$newFileName)) {
                if ($this->permit_num) {
                    $permit = ltrim($this->permit_num, '0');
                    exec('chmod '.$permit.' '.$saveDir.$newFileName);
                }
                $result = $newFileName;
            }
        }
        return $result;
    }
}
