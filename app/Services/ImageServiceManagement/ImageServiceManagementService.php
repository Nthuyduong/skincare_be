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
        $results = [];
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
                $image = $this->imageServiceManagementModelProxy->createImage($payload);
                array_push($results, $image);
            }
        }
        return $results;
    }

    function getImageById($id)
    {
        return $this->imageServiceManagementModelProxy->getImageById($id);
    }

    function updateImage($ids, $data)
    {
        foreach ($ids as $id) {
            $this->imageServiceManagementModelProxy->updateImage($id, $data);
        }
        return [];
    }

    function deleteImage($ids)
    {
        foreach ($ids as $id) {
            $image = $this->imageServiceManagementModelProxy->getImageById($id);
            if ($image) {
                ImageHelper::removeImage($image->url);
            }
            $this->imageServiceManagementModelProxy->deleteImage($id);
        }
        return [];
    }
}