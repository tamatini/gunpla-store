<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureService
{
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    /**
     * @throws Exception
     */
    public function addImage(UploadedFile $picture, ?string $folder = '', ?int $width = 250, ?int $height = 250): string
    {
        $file = md5(uniqid(rand(), true)) . '.png';
        $picture_infos = getimagesize($picture);
        if ($picture_infos === false) {
            throw new Exception('Format d\'image incorrect');
        }

        $picture_source = match ($picture_infos['mime']) {
            'image/png' => imagecreatefrompng($picture),
            'image/jpeg' => imagecreatefromjpeg($picture),
            'image/webp' => imagecreatefromwebp($picture),
            default => throw new Exception('Format d\'image incorrect'),
        };

        $pictureWidth = $picture_infos[0];
        $pictureHeight = $picture_infos[1];

        switch ($pictureWidth <=> $pictureHeight) {
            case -1:
                $squareSize = $pictureWidth;
                $src_x = 0;
                $src_y = ($pictureHeight - $squareSize) / 2;
                break;
            case 0:
                $squareSize = $pictureWidth;
                $src_x = 0;
                $src_y = 0;
                break;
            case 1:
                $squareSize = $pictureHeight;
                $src_x = ($pictureWidth - $squareSize) / 2;
                $src_y = 0;
                break;
        }

        $resizedPicture = imagecreatetruecolor($width, $height);
        imagecopyresampled($resizedPicture, $picture_source, 0, 0, $src_x, $src_y, $width, $height, $squareSize, $squareSize);

        $path = $this->params->get('images_directory') . $folder;

        if (!file_exists($path . '/mini/')) {
            mkdir($path . '/mini/', 0755, true);
        }

        imagepng($resizedPicture, $path . '/mini/' . $width . 'x' . $height . '-' . $file);
        $picture->move($path . '/', $file);

        return $file;
    }

    public function deleteImage(string $file, ?string $folder = '', ?int $width = 250, ?int $height = 250): bool
    {
        if ($file !== 'default.png') {
            $success = false;
            $path = $this->params->get('images_directory') . $folder;
            $mini = $path . '/mini/' . $width . 'x' . $height . '-' . $file;

            if (file_exists($mini)) {
                unlink($mini);
                $success = true;
            }

            $original = $path . '/' . $file;
            if (file_exists($original)) {
                unlink($mini);
                $success = true;
            }
            return $success;
        }
        return false;
    }
}