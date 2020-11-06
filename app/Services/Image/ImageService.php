<?php

namespace App\Services\Image;

use App\Services\BaseService;
use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImageService extends BaseService
{
    /**
     * Directory for save files
     */
    const DIRECTORY = 'images';

    /**
     * @param string $image
     * @param int    $userId
     *
     * @return int
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function uploadImage(string $image, int $userId)
    {
        $path = self::getFullPath($image);

        Storage::disk(config('filesystems.default'))->put($path, file_get_contents($image));

        /** @var Image $image */
        $image = app('ImageRepo')->create([
            'url'       => '/storage/'.$path,
            'user_id'   => $userId
        ]);

        return $image->id;
    }

    /**
     * @param string $image
     *
     * @return string
     */
    public static function getImageExtension(string $image)
    {
        return image_type_to_extension(exif_imagetype($image));
    }

    /**
     * @param string $image
     *
     * @return string
     */
    public function getFullPath(string $image)
    {
        return self::DIRECTORY.'/'.str_random(32).self::getImageExtension($image);
    }
}
