<?php
declare(strict_types=1);

namespace Max\FileSystem;

/**
 * @method File ext(array $ext)
 * @method File size(int $size)
 * @method File extExcept(array $ext)
 * Class File
 * @package Max
 */
class File
{
    /**
     * 文件信息
     * @var array
     */
    protected $file = [];

    protected $validate = [];

    public function data(array $file)
    {
        if (!empty($file['name'])) {
            if ($file['error'] > 0) {
                $this->_getError($file['error']);
            }
            if (!is_uploaded_file($file['tmp_name'])) {
                throw new \Exception('确定在上传文件？', 403);
            }
            $this->file = $file;
            return $this;
        }
        throw new \Exception('没有文件上传', 404);
    }


    /**
     * @param string $path 移动到的路径
     * @param string $name 新文件名，可以为空
     * @return array
     */
    public function move($path, string $name = null)
    {
        $this->_setNewName($name);
        $path = env('public_path') . trim($path, '/\\') . DIRECTORY_SEPARATOR;
        \Max\Tools\File::mkdir($path);
        if (false == move_uploaded_file($this->file['tmp_name'], $path . $name)) {
            throw new \Exception('文件移动失败！');
        }
        return ['path' => $path . $name, 'filename' => $name];
    }

    private function _setNewName(&$name)
    {
        $type = strrchr($this->file['name'], '.');
        if (isset($name)) {
            $name .= $type;
        } else {
            $name = md5_file($this->file['tmp_name']) . $type;
        }
    }

    /**
     * 使用方法(new File($file))->validate(['ext' => ['jpg','png','jpeg'],'ext' => 100])
     * @param array $validate 验证规则
     * @return $this
     * @throws Exception
     */
    public function validate(array $validate = [])
    {
        $this->validate += $validate;
        foreach ($this->validate as $type => $value) {
            $funcName = '_check' . ucfirst($type);
            if (method_exists($this, $funcName)) {
                if (true !== ($info = $this->$funcName($value))) {
                    throw new \Exception($info, 403);
                }
            }
        }
        return $this;
    }

    public function __call($method, $args)
    {
        $this->validate[strtolower($method)] = $args;
        return $this;
    }

    /**
     * 文件下载
     * @param string $filename 文件名
     * @param string $path 文件路径
     */
    public function download(string $filename, string $path)
    {
        if (!file_exists($path)) {
            throw new \Exception("文件{$filename}不存在", 404);
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        $filesize = filesize($path);
        header("Content-Type:application/{$mime}");
        header("Content-Disposition:attachment;filename={$filename}");
        header("Content-Transfer-Encoding:binary");
        header("Content-Length:{$filesize}");
        return readfile($path);
    }

    /**
     * 获取错误信息
     */
    private function _getError($code)
    {
        switch ($code) {
            case 4:
                throw new \Exception('没有文件被上传！', $code);
            case 3:
                throw new \Exception('文件被部分上传！', $code);
            case 2:
                throw new \Exception('文件大小超过HTLML限制的MAX_FILE_SIZE！', $code);
            case 1:
                throw new \Exception('文件大小超过PHP限制的upload_max_filesize！', $code);
            case -1:
                throw new \Exception('文件格式不支持上传！', $code);
            case -2:
                throw new \Exception('文件大小超过限制！', $code);
            default:
                throw new \Exception('未知错误！', $code);
        }
    }

    private function _checkSize(int $value)
    {
        return $this->file['size'] <= $value ? true : "文件大小{$this->file['size']}超过了{$value}字节!";
    }

    private function _checkExt(array $value)
    {
        $type = ltrim(strrchr($this->file['name'], '.'), '.');
        if (in_array($type, $value)) {
            return true;
        }
        return "文件后缀.{$type}不支持上传！";
    }

    private function _checkExtExcept(array $value)
    {
        $type = ltrim(strrchr($this->file['name'], '.'), '.');
        if (in_array($type, $value)) {
            return "文件后缀.{$type}不支持上传！";;
        }
        return true;
    }

}
