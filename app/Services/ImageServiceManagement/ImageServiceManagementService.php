<?php

namespace App\Services\ImageServiceManagement;

use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\Log;

class ImageServiceManagementService
{
    protected $imageServiceManagementModelProxy;

    public function __construct(ImageServiceManagementModelProxy $imageServiceManagementModelProxy)
    {
        $this->imageServiceManagementModelProxy = $imageServiceManagementModelProxy;
    }

    function getAllWithFilter($page = 1, $limit = 10, $filter = [])
    {
        $images = $this->imageServiceManagementModelProxy->getAllWithFilter($page, $limit, $filter);
        return $images;
    }

    function uploadImage($data)
    {
        if (isset($data['files'])) {
            foreach ($data['files'] as $file) {
                $payload = [];
                $payload['name'] = $file->getClientOriginalName();
                $payload['type'] = $file->getClientMimeType();
                $payload['size'] = $file->getSize();
                $image = ImageHelper::resizeImage($file);
                $payload['url'] = $image['original'];
                $payload['alt'] = $data['alt'] ?? '';
                $payload['suggest'] = $data['suggest'] ?? '';
                $this->imageServiceManagementModelProxy->createImage($payload);
            }
        }
        return null;
    }

    function getImageById($id)
    {
        return $this->imageServiceManagementModelProxy->getImageById($id);
    }

    function updateImage($id, $data)
    {
        return $this->imageServiceManagementModelProxy->updateImage($id, $data);
    }

    function deleteImage($id)
    {
        $image = $this->imageServiceManagementModelProxy->getImageById($id);
        if ($image) {
            ImageHelper::removeImage($image->url);
        }
        return $this->imageServiceManagementModelProxy->deleteImage($id);
    }
}