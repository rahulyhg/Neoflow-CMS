<?php
namespace Neoflow\Image;

use Neoflow\Filesystem\File;
use Neoflow\Image\Exception\ImageFileException;

class ImageFile extends File
{

    /**
     * Image resource.
     *
     * @var type
     */
    protected $image;

    /**
     * Load file path and image.
     *
     * @param string $filePath Image file path
     *
     * @return self
     *
     * @throws ImageFileException
     */
    public function load(string $filePath): self
    {
        if (parent::load($filePath)) {
            switch ($this->getType()) {
                case IMAGETYPE_JPEG:
                    $this->setRequiredMemory();
                    $this->image = imagecreatefromjpeg($this->path);
                    break;
                case IMAGETYPE_PNG:
                    $this->image = imagecreatefrompng($this->path);

                    break;
                case IMAGETYPE_GIF:
                    $this->image = imagecreatefromgif($this->path);

                    break;
                case IMAGETYPE_BMP:
                    $this->image = imagecreatefromwbmp($this->path);

                    break;
                default:
                    throw new ImageFileException('Cannot load image file path, because "' . $this->path . '" is not a valid PNG, GIF, BMP or JPEG-based image.', ImageFileException::NOT_SUPPORTED_IMAGE_TYPE);
            }
            $this->fixOrientation();
        }

        return $this;
    }

    /**
     * Get image type.
     *
     * @return int
     */
    public function getType(): int
    {
        return @getimagesize($this->path)[2];
    }

    /**
     * Support method: Set required memory.
     *
     * @return int
     */
    protected function setRequiredMemory(): bool
    {
        $imageInfo = @getimagesize($this->path);
        if (is_array($imageInfo)) {
            $MB = pow(1024, 2);
            $K64 = pow(2, 16);
            $TWEAKFACTOR = 2;
            $memoryNeeded = round(($imageInfo[0] * $imageInfo[1] * $imageInfo['bits'] * $imageInfo['channels'] / 8 + $K64) * $TWEAKFACTOR);
            $memoryUsage = memory_get_usage();
            $memoryLimit = (int) ini_get('memory_limit') * $MB;

            if ($memoryUsage + $memoryNeeded > $memoryLimit) {
                $newMemoryLimit = ($memoryLimit + ceil($memoryUsage + $memoryNeeded - $memoryLimit)) / $MB;

                return (bool) @ini_set('memory_limit', $newMemoryLimit . 'M');
            }
        }

        return false;
    }

    /**
     * Get image width.
     *
     * @return int
     */
    public function getWidth(): int
    {
        return imagesx($this->image);
    }

    /**
     * Get image height.
     *
     * @return int
     */
    public function getHeight(): int
    {
        return imagesy($this->image);
    }

    /**
     * Save image.
     *
     * @param string     $newFilePath New file path
     * @param int|string $imageType   Type or extension of image
     * @param int        $quality     Quality rate from 1 to 100
     *
     * @return self
     *
     * @throws ImageFileException
     */
    public function save(string $newFilePath = '', $imageType = null, int $quality = 90): self
    {
        // Fallback to get current file path
        if (!$newFilePath) {
            $newFilePath = $this->path;
        }

        // Fallback to get image type
        if (!in_array($imageType, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_BMP])) {
            if (is_string($imageType)) {
                $imageType = $this->fileExtensionToImageType($imageType);
            } else {
                $extension = pathinfo($newFilePath, PATHINFO_EXTENSION);
                if (!$extension) {
                    $extension = $this->getExtension();
                }

                $imageType = $this->fileExtensionToImageType($extension);
            }
        }

        if ($this->createImageFile($newFilePath, $imageType, $quality)) {
            return new static($newFilePath);
        }
        throw new ImageFileException('Saving image file to file path "' . $newFilePath . '" failed', ImageFileException::NOT_WRITEABLE);
    }

    /**
     * Resize image to height.
     *
     * @param int $height
     *
     * @return self
     */
    public function resizeToHeight(int $height): self
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);

        return $this;
    }

    /**
     * Resize image to width.
     *
     * @param int $width Image resize
     *
     * @return self
     */
    public function resizeToWidth(int $width): self
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getHeight() * $ratio;
        $this->resize($width, $height);

        return $this;
    }

    /**
     * Scale image.
     *
     * @param int $scale Scale size
     *
     * @return self
     */
    public function scale(int $scale): self
    {
        $newWidth = $this->getWidth() * $scale / 100;
        $newHeight = $this->getHeight() * $scale / 100;

        return $this->resize($newWidth, $newHeight);
    }

    /**
     * Resize image to height and width.
     *
     * @param int $newWidth  New image width
     * @param int $newHeight New image height
     *
     * @return self
     */
    public function resize(int $newWidth, int $newHeight): self
    {
        $newImage = $this->createNewImage($newWidth, $newHeight);

        imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $this->getWidth(), $this->getHeight());

        $this->image = $newImage;

        return $this;
    }

    /**
     * Create new image.
     *
     * @param int $newWidth  New image width
     * @param int $newHeight New image height
     *
     * @return resource
     */
    protected function createNewImage(int $newWidth, int $newHeight): resource
    {
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        return $this->preserveTransparency($newImage);
    }

    /**
     * Resize image to best fitting width and height (proportional).
     *
     * @param int $newWidth  New image width
     * @param int $newHeight New image height
     *
     * @return self
     */
    public function resizeBestFit($newWidth, $newHeight)
    {
        $ratio = min($newWidth / $this->getWidth(), $newHeight / $this->getHeight());

        return $this->resize($this->getWidth() * $ratio, $this->getHeight() * $ratio);
    }

    /**
     * Crop image to exact height and width (proportional).
     *
     * @param int $newWidth  New image width
     * @param int $newHeight New image height
     *
     * @return self
     */
    public function crop($newWidth, $newHeight)
    {
        $ratio = $this->getWidth() / $this->getHeight();
        $newRatio = $newWidth / $newHeight;

        if ($ratio >= $newRatio) {
            $height = $this->getHeight();
            $width = ceil(($height * $newWidth) / $newHeight);
            $xCoordinate = ceil(($this->getWidth() - $width) / 2);
            $yCoordinate = 0;
        } else {
            $width = $this->getWidth();
            $height = ceil(($width * $newHeight) / $newWidth);
            $yCoordinate = ceil(($this->getHeight() - $height) / 2);
            $xCoordinate = 0;
        }

        $newImage = $this->createNewImage($newWidth, $newHeight);
        imagecopyresampled($newImage, $this->image, 0, 0, $xCoordinate, $yCoordinate, $newWidth, $newHeight, $width, $height);

        $this->image = $newImage;

        return $this;
    }

    /**
     * Support method: Create image file.
     *
     * @param string $imageFilePath File path of image
     * @param int    $imageType     Type of image
     * @param int    $compression   Quality rate from 1 to 100
     * @param bool   $overwrite     Set FALSE to prevent overwriting, when the a file with the image file name already exist
     *
     * @return bool
     *
     * @throws ImageFileException
     */
    protected function createImageFile($imageFilePath, $imageType, $compression = 90, $overwrite = true)
    {
        if ($overwrite || !is_file($imageFilePath)) {
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    imagejpeg($this->image, $imageFilePath, $compression);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($this->image, $imageFilePath, round(9 / 100 * $compression));
                    break;
                case IMAGETYPE_GIF:
                    imagegif($this->image, $imageFilePath);
                    break;
                case IMAGETYPE_BMP:
                    image2wbmp($this->image, $imageFilePath, round(255 / 100 * $compression));
                    break;
                default:
                    throw new ImageFileException('Image type "' . $imageType . '" is not supported.', ImageFileException::NOT_SUPPORTED_IMAGE_TYPE);
            }

            return true;
        }
        throw new ImageFileException('Cannot create image file, because the image file path "' . $imageFilePath . '" already exist.', ImageFileException::ALREADY_EXIST);
    }

    /**
     * Support method: Convert file extension to image type.
     *
     * @param string $fileExtension
     *
     * @return string
     *
     * @throws ImageFileException
     */
    protected function fileExtensionToImageType($fileExtension)
    {
        switch (mb_strtolower($fileExtension)) {
            case 'jpeg':
            case 'jpg':
                return IMAGETYPE_JPEG;
            case 'png':
                return IMAGETYPE_PNG;
            case 'gif':
                return IMAGETYPE_GIF;
            case 'bmp':
                return IMAGETYPE_BMP;
            default:
                throw new ImageFileException('File extension "' . $fileExtension . '" is not supported as image type.', ImageFileException::NOT_SUPPORTED_IMAGE_TYPE);
        }
    }

    /**
     * Support method: Preserve transparency of new image resource.
     *
     * @param resource $newImage New image resource
     *
     * @return resource
     */
    protected function preserveTransparency($newImage)
    {
        switch ($this->getType()) {
            case IMAGETYPE_GIF:
                $transparentIndex = imagecolortransparent($this->image);
                $palletsize = imagecolorstotal($this->image);
                if ($transparentIndex >= 0 && $transparentIndex < $palletsize) {
                    $transparentColor = imagecolorsforindex($this->image, $transparentIndex);
                    $transparentIndex = imagecolorallocate($newImage, $transparentColor['red'], $transparentColor['green '], $transparentColor['blue']);
                    imagefill($newImage, 0, 0, $transparentIndex);
                    imagecolortransparent($newImage, $transparentColor);
                }

            // no break
            case IMAGETYPE_PNG:
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
        }

        return $newImage;
    }

    /**
     * Support method: Fix image orientation.
     *
     * @return bool
     */
    public function fixOrientation(): bool
    {
        // Correct image rotation
        if (function_exists('exif_read_data')) {
            $exif = @exif_read_data($this->path);
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 8:
                        $this->image = imagerotate($this->image, 90, 0);
                        break;
                    case 3:
                        $this->image = imagerotate($this->image, 180, 0);
                        break;
                    case 6:
                        $this->image = imagerotate($this->image, -90, 0);
                        break;
                }
            }

            return true;
        }

        return false;
    }
}
