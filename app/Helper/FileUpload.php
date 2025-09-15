<?php

namespace App\Helper;

use Illuminate\Support\Facades\Storage;

class FileUpload
{
    public static function publicStore($file, string $path = 'file_uploads'): string
    {
        return Storage::disk("public")->put($path, $file);
    }

    /**
     * Handles the upload of a product image file and updates the provided data array.
     *
     * This method checks if the specified key exists in the data array and if it is a valid file.
     * If so, it stores the file in the "products_images_uploads" directory using the publicStore method,
     * updates the data array with the new file path, and adds a hash of the file's contents.
     *
     * @param array  $data The data array passed by reference, expected to contain the file under the specified key.
     * @param string $key  The key in the data array that holds the file to be uploaded. Defaults to "image".
     *
     * @return array The updated data array with the uploaded file path and its hash.
     */
    public static function uploadProductImage(array &$data, string $key = "image"): array
    {
        if (isset($data[$key]) && is_file($data[$key])) {
            $data[$key] = self::publicStore($data[$key], "products_images_uploads");
            $data["{$key}_hash"] = md5_file(file_get_contents($data[$key]->getRealPath()));
        }

        return $data;
    }
}
