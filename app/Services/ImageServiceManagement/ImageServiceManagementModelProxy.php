<?php

namespace App\Services\ImageServiceManagement;

use App\Models\Image;

class ImageServiceManagementModelProxy {
    function getAllWithFilter($page = 1, $limit = 10, $filter = []) {
        $query = Image::query();

        

        if (isset($filter['search'])) {
            $query = $query->where(function($q) use ($filter) {
                $q->where('name', 'like', '%' . $filter['search'] . '%')
                    ->orWhere('suggest', 'like', '%' . $filter['search'] . '%');
            });
        }
        $count = $query->count();
        $results = $query
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->orderBy('created_at', 'desc')
            ->get();
        // select * from images
        return [
            'results' => $results,
            'paginate' => [
                'current' => $page,
                'limit' => $limit,
                'last' => ceil($count / $limit),
                'count' => $count,
            ]
        ];
    }

    function createImage($data) {
        $image = new Image();
        $image->name = $data['name'];
        $image->type = $data['type'];
        $image->size = $data['size'];
        $image->alt = $data['alt'];
        $image->url = $data['url'];
        $image->suggest = $data['suggest'];
        $image->save();
        return $image;
    }

    function getImageById($id) {
        return Image::where('id', $id)->first();
    }

    function updateImage($id, $data) {
        $image = $this->getImageById($id);

        $image->name =  $data['name'] ?? $image->name;
        $image->type = $data['type'] ?? $image->type;
        $image->size = $data['size'] ?? $image->size;
        $image->alt = $data['alt'] ?? $image->alt;
        $image->url = $data['url'] ?? $image->url;
        $image->suggest = $data['suggest'] ?? $image->suggest;
        $image->save();
        return $image;
    }

    function deleteImage($id) {
        $image = $this->getImageById($id);
        $image->delete();
        return $image;
    }
}