<?php

namespace App\Http\Controllers\Helpers;


use App\Http\Controllers\Controller;

class ImageHelper extends Controller {
    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'JPG', 'JPEG'];
    public const DEFAULT_JPG_QUALITY = 100;
    private $tempImage;

    public function __destruct() {
        imagedestroy($this->tempImage);
    }

    public function convertImage($originalImage, $newImage, $quality = 100) {
        // jpg, png, gif or bmp?
        $exploded = explode('.',$originalImage);
        $ext = $exploded[count($exploded) - 1];

        if (preg_match('/png/i',$ext))
            $this->tempImage = imagecreatefrompng($originalImage);
        else if (preg_match('/gif/i',$ext))
            $this->tempImage = imagecreatefromgif($originalImage);
        else if (preg_match('/bmp/i',$ext))
            $this->tempImage = imagecreatefrombmp($originalImage);
        else
            return 0;

        // quality is a value from 0 (worst) to 100 (best)
        imagejpeg($this->tempImage, $newImage, $quality);

        return TRUE;
    }

    // Function for resizing any jpg, gif, or png image files
    function resizeImage($originalImage, $newImage, $newWidth, $newHeight, $extension) {
        list($originalWidth, $OriginalHeight) = getimagesize($originalImage);
        $scaleRatio = $originalWidth / $OriginalHeight;
        if (($newWidth / $newHeight) > $scaleRatio) {
            $newWidth = $newHeight * $scaleRatio;
        } else {
            $newHeight = $newWidth / $scaleRatio;
        }

        if (preg_match('/png/i',$extension))
            $this->tempImage = imagecreatefrompng($originalImage);
        else if (preg_match('/gif/i',$extension))
            $this->tempImage = imagecreatefromgif($originalImage);
        else if (preg_match('/bmp/i',$extension))
            $this->tempImage = imagecreatefrombmp($originalImage);
        else
            return 0;

        $resizedImageContainer = imagecreatetruecolor($newWidth, $newHeight);
        // imagecopyresampled(dst_img, src_img, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)
        imagecopyresampled($resizedImageContainer, $this->tempImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $OriginalHeight);
        if (preg_match('/png/i',$extension))
            imagegif($resizedImageContainer, $newImage);
        elseif (preg_match('/gif/i',$extension))
            imagepng($resizedImageContainer, $newImage);
        else  if (preg_match('/bmp/i',$extension))
            imagebmp($resizedImageContainer, $newImage);
        else
            imagejpeg($resizedImageContainer, $newImage, DEFAULT_JPG_QUALITY);

        imagedestroy($resizedImageContainer);
    }

    // Function for creating a true thumbnail cropping from any jpg, gif, or png image files
    function createThumbnail($originalImage, $newImage, $width, $height, $extension) {
        list($originalWidth, $originalHeight) = getimagesize($originalImage);

        $src_x = ($originalWidth / 2) - ($width / 2);
        $src_y = ($originalHeight / 2) - ($height / 2);

        if (preg_match('/png/i',$extension))
            $this->tempImage = imagecreatefrompng($originalImage);
        else if (preg_match('/gif/i',$extension))
            $this->tempImage = imagecreatefromgif($originalImage);
        else if (preg_match('/bmp/i',$extension))
            $this->tempImage = imagecreatefrombmp($originalImage);
        else
            return 0;
        $resizedImageContainer = imagecreatetruecolor($width, $height);
        imagecopyresampled($resizedImageContainer, $this->tempImage, 0, 0, $src_x, $src_y, $width, $height, $width, $height);

        if (preg_match('/png/i',$extension))
            imagegif($resizedImageContainer, $newImage);
        elseif (preg_match('/gif/i',$extension))
            imagepng($resizedImageContainer, $newImage);
        else  if (preg_match('/bmp/i',$extension))
            imagebmp($resizedImageContainer, $newImage);
        else
            imagejpeg($resizedImageContainer, $newImage, DEFAULT_JPG_QUALITY);
    }
}
