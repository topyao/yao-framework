<?php
declare(strict_types=1);

namespace Yao;

/**
 * @method File ext(array $ext)
 * @method File size(int $size)
 * @method File extExcept(array $ext)
 * Class File
 * @package Yao
 */
class File
{
    /**
     * 文件信息
     * @var array
     */
    protected array $file = [];

    protected array $validate = [];

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
        } else {
            throw new \Exception('没有文件上传', 404);
        }
    }


    /**
     * @param array $path 移动到的路径
     * @param ?string $name 新文件名，可以为空
     * @return array
     */
    public function move($path, string $name = null)
    {
        $this->_setNewName($name);
        $this->_mkdir($path);
        if (false == move_uploaded_file($this->file['tmp_name'], $path . $name)) {
            throw new \Exception('文件移动失败！');
        }
        return ['path' => $path . DIRECTORY_SEPARATOR . $name, 'filename' => $this->file['name']];
    }

    private function _mkdir($path)
    {
        $paths = env('public_path') . trim($path, '/\\') . DIRECTORY_SEPARATOR;
        if (!file_exists($paths) || !is_dir($paths)) {
            if (!mkdir($path, 0777, 1)) {
                throw new \Exception('目录创建失败！');
            }
        }
    }

    private function _setNewName(&$name)
    {
        if (isset($name)) {
            $name .= strrchr($this->file['name'], '.');
        } else {
            $name = $this->file['name'];
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

    private function _flush()
    {
        $this->validate = [];
        $this->file = [];
    }
}
