<?php
namespace Neoflow\Image;

use Neoflow\Filesystem\AbstractObject;
use Neoflow\Filesystem\File;
use Neoflow\Image\Exception\ImageFileException;

class ImageFile extends File
{

    /**
     * Image resource.
     *
     * @var resource
     */
    protected $image;

    /**
     * Constructor.
     *
     * @param string $path Image file path
     */
    public function __construct(string $path)
    {
        parent::__construct($path);

        $this->loadImageResource();
    }

    /**
     * Get image type.
     *
     * @return int
     */
    public function getType(): int
    {
        return exif_imagetype($this->path);
    }

    /**
     * Get image info (based on getimagesize function).
     *
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
        return (int) imagesx($this->image);
    }

    /**
     * Get image height.
     *
     * @return int
     */
    public function getHeight(): int
    {
        return (int) imagesy($this->image);
    }

    /**
     * Reload image resource from current file path
     *
     * @param bool $fixOrientation Set FALSE to prevent fixing image orientation
     *
     * @return self
     */
    protected function reloadImageResource(bool $fixOrientation = true): self
    {
        return $this->loadImageResource($fixOrientation);
    }

    /**
     * Load image resource from file path.
     *
     * @param bool $fixOrientation Set FALSE to prevent fixing image orientation
     *
     * @return self
     *
     * @throws ImageFileException
     */
    protected function loadImageResource(bool $fixOrientation = true): self
    {
        // Get current memory limit
        $memoryLimit = ini_get('memory_limit');

        // Disable memory limit
        ini_set('memory_limit', '-1');

        switch ($this->getType()) {
            case IMAGETYPE_JPEG:
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

        // Reset memory limit
        ini_set('memory_limit', $memoryLimit);

        if ($fixOrientation) {
            $this->fixOrientation();
        }

        return $this;
    }

    /**
     * Save image.
     *
     * @param string $newFilePath  New file path
     * @param int    $newImageType New Image tpye
     * @param int    $compression  Quality compression rate from 1 to 100
     *
     * @return self
     *
     * @throws ImageFileException
     */
    public function save(string $newFilePath = '', int $newImageType = null, int $compression = 90): self
    {
        // Fallback to get current file path
        if (empty($newFilePath)) {
            $newFilePath = $this->path;
        }

        if (empty($newImageType)) {
            $newImageType = exif_imagetype($this->path);
        }

        if ($this->createNewFile($newFilePath, $newImageType, $compression)) {
            $this->path = $newFilePath;

            return $this;
        }
        throw new ImageFileException('Saving image file to file path "' . $newFilePath . '" failed', ImageFileException::NOT_WRITEABLE);
    }

    /**
     * Resize image to height.
     *
     * @param int $newHeight New height
     *
     * @return self
     */
    public function resizeToHeight(int $newHeight): self
    {
        $ratio = $newHeight / $this->getHeight();
        $newWidth = $this->getWidth() * $ratio;
        $this->resize($newWidth, $newHeight);

        return $this;
    }

    /**
     * Resize image to new width.
     *
     * @param int $newWidth New width
     *
     * @return self
     */
    public function resizeToWidth(int $newWidth): self
    {
        $ratio = $newWidth / $this->getWidth();
        $newHeight = $this->getHeight() * $ratio;
        $this->resize($newWidth, $newHeight);

        return $this;
    }

    /**
     * Scale image to new size.
     *
     * @param float $newSize New scale size
     *
     * @return self
     */
    public function scale(float $newSize): self
    {
        $newWidth = $this->getWidth() * $newSize / 100;
        $newHeight = $this->getHeight() * $newSize / 100;

        return $this->resize($newWidth, $newHeight);
    }

    /**
     * Resize image to height and width.
     *
     * @param int $newWidth  New width
     * @param int $newHeight New height
     *
     * @return self
     */
    public function resize(int $newWidth, int $newHeight): self
    {
        $this->image = $this->createNewImage($newWidth, $newHeight);

        return $this;
    }

    /**
     * Resize image to best fitting width and height (proportional).
     *
     * @param int $newWidth  New width
     * @param int $newHeight New height
     *
     * @return self
     */
    public function resizeBestFit(int $newWidth, int $newHeight): self
    {
        $newRatio = min($newWidth / $this->getWidth(), $newHeight / $this->getHeight());

        return $this->resize($this->getWidth() * $newRatio, $this->getHeight() * $newRatio);
    }

    /**
     * Crop image to exact height and width (proportional).
     *
     * @param int $newWidth  New width
     * @param int $newHeight New height
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
            $coordinateX = ceil(($this->getWidth() - $width) / 2);
            $coordinateY = 0;
        } else {
            $width = $this->getWidth();
            $height = ceil(($width * $newHeight) / $newWidth);
            $coordinateY = ceil(($this->getHeight() - $height) / 2);
            $coordinateX = 0;
        }

        $this->image = $this->createNewImage($newWidth, $newHeight, $width, $height, 0, 0, $coordinateX, $coordinateY);

        return $this;
    }

    /**
     * Get image resource
     * @return resource
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Create new image file.
     *
     * @param string $newFilePath New image file path
     * @param int    $compression Quality compression rate from 1 to 100
     * @param bool   $overwrite   Set FALSE to prevent overwriting, when the a file with the image file name already exist
     *
     * @return bool
     *
     * @throws ImageFileException
     */
    protected function createNewFile(string $newFilePath, int $newImageType, int $compression = 90, bool $overwrite = true): bool
    {
        if ($overwrite || !is_file($newFilePath)) {
            switch ($newImageType) {
                case IMAGETYPE_JPEG:
                    imagejpeg($this->image, $newFilePath, $compression);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($this->image, $newFilePath, round(9 / 100 * $compression));
                    break;
                case IMAGETYPE_GIF:
                    imagegif($this->image, $newFilePath);
                    break;
                case IMAGETYPE_BMP:
                    image2wbmp($this->image, $newFilePath, round(255 / 100 * $compression));
                    break;
                default:
                    throw new ImageFileException('Image type "' . $newImageType . '" is not supported.', ImageFileException::NOT_SUPPORTED_IMAGE_TYPE);
            }

            return true;
        }
        throw new ImageFileException('Cannot create image file, because the image file path "' . $newFilePath . '" already exist.', ImageFileException::ALREADY_EXIST);
    }

    /**
     * Create new image resource.
     *
     * @param int $newWidth       New image width
     * @param int $newHeight      New image height
     * @param int $width          Image width
     * @param int $height         Image height
     * @param int $newCoordinateX New X coordinate pointer
     * @param int $newCoordinateY New Y coordinate pointer
     * @param int $coordinateX    X coordinate pointer
     * @param int $coordinateY    Y coordinate pointer
     *
     * @return resource
     */
    protected function createNewImage(int $newWidth, int $newHeight, int $width = null, int $height = null, int $newCoordinateX = 0, int $newCoordinateY = 0, int $coordinateX = 0, int $coordinateY = 0)
    {
        if (empty($width)) {
            $width = $this->getWidth();
        }

        if (empty($height)) {
            $height = $this->getHeight();
        }

        $image = imagecreatetruecolor($newWidth, $newHeight);

        if (IMAGETYPE_PNG === $this->getType()) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
        } elseif (IMAGETYPE_GIF === $this->getType()) {
            $transparentIndex = imagecolortransparent($this->image);
            $palletSize = imagecolorstotal($this->image);
            if ($transparentIndex >= 0 && $transparentIndex < $palletSize) {
                $transparentColor = imagecolorsforindex($this->image, $transparentIndex);
                $transparentIndex = imagecolorallocate($image, $transparentColor['red'], $transparentColor['green '], $transparentColor['blue']);
                imagefill($image, 0, 0, $transparentIndex);
                imagecolortransparent($image, $transparentColor);
            }
        }

        imagecopyresampled($image, $this->image, $newCoordinateX, $newCoordinateY, $coordinateX, $coordinateY, $newWidth, $newHeight, $width, $height);

        return $image;
    }

    /**
     * Convert file extension to image type.
     *
     * @param string $path             Image file path
     * @param int    $defaultImageType Default image type
     *
     * @return int
     *
     * @throws ImageFileException
     */
    protected function fetchImageTypeFromPath(string $path, int $defaultImageType = IMAGETYPE_JPEG): int
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        if (!$extension) {
            switch (mb_strtolower($extension)) {
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

        return $defaultImageType;
    }

    /**
     * Fix image orientation.
     *
     * @return bool
     */
    protected function fixOrientation(): bool
    {
        $exif = exif_read_data($this->path);
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
}
