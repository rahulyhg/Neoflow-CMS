<?php

namespace Neoflow\Image;

use Neoflow\Filesystem\AbstractObject;
use Neoflow\Filesystem\Exception\FileException;
use Neoflow\Filesystem\File;
use Neoflow\Image\Exception\ImageFileException;

class ImageFile extends File {

    /**
     * Image resource.
     *
     * @var resource
     */
    protected $image;

    /**
     * Load image file
     *
     * @param string $path Image file path
     *
     * @return static
     */
    public static function load(string $path): AbstractObject
    {
        $imageFile = new static($path);
        if ($imageFile) {
            $imageFile->loadImageResource();
        }

        return $imageFile;
    }

    /**
     * Get image type.
     *
     * @return int
     */
    public function getImageType(): int
    {
        $info = $this->getInfo();
        return (int) $info[2];
    }

    /**
     * Get image info (based on getimagesize function)
     * @return array
     */
    public function getInfo(): array
    {
        return getimagesize($this->path);
    }

    /**
     * Get image width.
     *
     * @return int
     */
    public function getWidth(): int
    {
        $info = $this->getInfo();
        return (int) $info[0];
    }

    /**
     * Get image height.
     *
     * @return int
     */
    public function getHeight(): int
    {
        $info = $this->getInfo();
        return (int) $info[1];
    }

    /**
     * Load image resource from image file path
     * @return self
     * @throws ImageFileException
     */
    public function loadImageResource(): self
    {
        switch ($this->getImageType()) {
            case IMAGETYPE_JPEG:
                $this->setRequiredMemory();
                $this->image = imagecreatefromjpeg($this->path);
                break;
            case IMAGETYPE_PNG:
                $this->setRequiredMemory();
                $this->image = imagecreatefrompng($this->path);
                break;
            case IMAGETYPE_GIF:
                $this->image = imagecreatefromgif($this->path);
                break;
            case IMAGETYPE_BMP:
                $this->setRequiredMemory();
                $this->image = imagecreatefromwbmp($this->path);

                break;
            default:
                throw new ImageFileException('Cannot load image file path, because "' . $this->path . '" is not a valid PNG, GIF, BMP or JPEG-based image.', ImageFileException::NOT_SUPPORTED_IMAGE_TYPE);
        }
        $this->fixOrientation();

        return $this;
    }

    /**
     * Calculate and set required memory.
     *
     * @return bool
     */
    protected function setRequiredMemory(): bool
    {
        $imageInfo = $this->getInfo();
        if (is_array($imageInfo)) {
            $MB = pow(1024, 2);
            $K64 = pow(2, 16);
            $tweakFactor = 2;
            $memoryNeeded = round(($imageInfo[0] * $imageInfo[1] * $imageInfo['bits'] * $imageInfo['channels'] / 8 + $K64) * $tweakFactor);
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
     * Save image.
     *
     * @param string $newFilePath New file path
     * @param int|string $imageType Type or extension of image
     * @param int $quality Quality rate from 1 to 100
     *
     * @return self
     *
     * @throws ImageFileException
     * @throws FileException
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
     * @param int $height New height
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
     * @param int $width New width
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
     * @param int $scale New scale size
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
     * @param int $width  New width
     * @param int $height New height
     *
     * @return self
     */
    public function resize(int $width, int $height): self
    {
        $image = $this->createNewImage($width, $height, $this->getImageType());

        imagecopyresampled($image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());

        $this->image = $image;

        return $this;
    }

    /**
     * Resize image to best fitting width and height (proportional).
     *
     * @param int $width  New width
     * @param int $height New height
     *
     * @return self
     */
    public function resizeBestFit(int $width, int $height): self
    {
        $ratio = min($width / $this->getWidth(), $height / $this->getHeight());

        return $this->resize($this->getWidth() * $ratio, $this->getHeight() * $ratio);
    }

    /**
     * Crop image to exact height and width (proportional).
     *
     * @param int $width  New width
     * @param int $height New height
     *
     * @return self
     */
    public function crop($width, $height)
    {
        $srcRatio = $this->getWidth() / $this->getHeight();
        $ratio = $width / $height;

        if ($srcRatio >= $ratio) {
            $srcHeight = $this->getHeight();
            $srcWidth = ceil(($srcHeight * $width) / $height);
            $xCoordinate = ceil(($this->getWidth() - $srcWidth) / 2);
            $yCoordinate = 0;
        } else {
            $srcWidth = $this->getWidth();
            $srcHeight = ceil(($srcWidth * $height) / $width);
            $yCoordinate = ceil(($this->getHeight() - $srcHeight) / 2);
            $xCoordinate = 0;
        }

        $image = $this->createNewImage($width, $height, $this->getImageType());
        imagecopyresampled($image, $this->image, 0, 0, $xCoordinate, $yCoordinate, $width, $height, $srcWidth, $srcHeight);

        $this->image = $image;

        return $this;
    }

    /**
     * Create image file.
     *
     * @param string $path Image file path
     * @param int    $type    Image type
     * @param int    $compression   Quality rate from 1 to 100
     * @param bool   $overwrite     Set FALSE to prevent overwriting, when the a file with the image file name already exist
     *
     * @return bool
     *
     * @throws ImageFileException
     */
    protected function createImageFile($path, $type, $compression = 90, $overwrite = true)
    {
        if ($overwrite || !is_file($path)) {
            switch ($type) {
                case IMAGETYPE_JPEG:
                    imagejpeg($this->image, $path, $compression);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($this->image, $path, round(9 / 100 * $compression));
                    break;
                case IMAGETYPE_GIF:
                    imagegif($this->image, $path);
                    break;
                case IMAGETYPE_BMP:
                    image2wbmp($this->image, $path, round(255 / 100 * $compression));
                    break;
                default:
                    throw new ImageFileException('Image type "' . $type . '" is not supported.', ImageFileException::NOT_SUPPORTED_IMAGE_TYPE);
            }

            return true;
        }
        throw new ImageFileException('Cannot create image file, because the image file path "' . $path . '" already exist.', ImageFileException::ALREADY_EXIST);
    }

    /**
     * Create new image resource.
     *
     * @param int $width  Image width
     * @param int $height Image height
     * @param int $type Image type
     *
     * @return resource
     */
    protected function createNewImage(int $width, int $height, int $type = null)
    {
        $image = imagecreatetruecolor($width, $height);

        if ($type === IMAGETYPE_PNG) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
        } elseif ($type === IMAGETYPE_GIF) {
            $transparentIndex = imagecolortransparent($this->image);
            $palletSize = imagecolorstotal($this->image);
            if ($transparentIndex >= 0 && $transparentIndex < $palletSize) {
                $transparentColor = imagecolorsforindex($this->image, $transparentIndex);
                $transparentIndex = imagecolorallocate($image, $transparentColor['red'], $transparentColor['green '], $transparentColor['blue']);
                imagefill($image, 0, 0, $transparentIndex);
                imagecolortransparent($image, $transparentColor);
            }
        }

        return $image;
    }

    /**
     * Convert file extension to image type.
     *
     * @param string $fileExtension
     *
     * @return int
     *
     * @throws ImageFileException
     */
    protected function fileExtensionToImageType(string $fileExtension): int
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
     * Fix image orientation.
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
