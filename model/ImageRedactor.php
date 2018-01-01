<?php
/**
 * Created by PhpStorm.
 * User: oklymeno
 * Date: 11/19/17
 * Time: 2:03 PM
 */

namespace model;


class ImageRedactor
{
    protected $imageResourse;

    protected $resizedResourse;

    protected $imageName;

    protected $width;

    protected $height;

    protected $new_width;

    protected $new_height;

    protected $result_width = 835;

    protected $result_height = 635;

    protected $type;

    protected $path;

    public $newImageName;

    public function __construct($image, $type, $pathToSave = null)
    {
        $this->type = $type;
        $this->imageName = $image;
        $this->path = $pathToSave;
    }

    public function splitImage()
    {
        $this->imageResourse = $this->createImageResource($this->imageName);
        $this->getImageSize();
        $this->resizeImage();
        $this->resizedResourse = imagecreate($this->result_width, $this->result_height);
        $white = imagecolorallocate($this->resizedResourse,255, 255, 255);
        imagefilledrectangle($this->resizedResourse, 0, 0, $this->new_width, $this->new_height, $white);
        if ($this->width < $this->new_width) {
            imagecopyresized($this->resizedResourse, $this->imageResourse, ($this->result_width - $this->new_width) / 2, 0, 0,0, $this->new_width, $this->new_height, $this->width, $this->height);
        } else {
            imagecopyresized($this->resizedResourse, $this->imageResourse, 0, ($this->result_height - $this->new_height) / 2, 0,0, $this->new_width, $this->new_height , $this->width, $this->height);
        }
    }

    private function resizeImage()
    {
        $this->new_width = $this->width;
        $this->new_height = $this->height;
        while ($this->new_height < $this->result_height && $this->new_width < $this->result_width) {
            $this->new_width++;
            $this->new_height++;
        }
        while ($this->new_height > $this->result_height && $this->new_width > $this->result_width) {
            $this->new_width--;
            $this->new_height--;
        }
    }

    private function getImageSize()
    {
        $size = getimagesize($this->imageName);
        if (isset($size[0]) && isset($size[1])) {
            $this->width = $size[0];
            $this->height = $size[1];
        }
    }

    private function createImageResource($imageName)
    {
        switch ($this->type){
            case "jpg":
                return imagecreatefromjpeg($imageName);
            case "jpeg":
                return imagecreatefromjpeg($imageName);
            case "png":
                return imagecreatefrompng($imageName);
            default:
                break ;
        }
    }

    private function getNewImageName()
    {
        $this->newImageName = date("YmdHis") . '.jpg';
    }

    public function saveResizedImage()
    {
        $this->getNewImageName();
        imagejpeg($this->resizedResourse, $this->path . $this->newImageName);
    }
}