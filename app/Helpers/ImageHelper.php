<?php

namespace App\Helpers;

use App\Exceptions\ResizeImageException;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;

class ImageHelper
{
    public static function resizeImage($file)
    {
        $manager = new ImageManager(
            new Driver()
        );
        $fileFolder = '/storage';
        $desktopFolder = '/storage/desktop';
        $tabletFolder = '/storage/tablet';
        $mobileFolder = '/storage/mobile';
        if (!File::exists($fileFolder)) {
            File::makeDirectory(public_path($fileFolder), 0777, true, true);
        }
        if (!File::exists($desktopFolder)) {
            File::makeDirectory(public_path($desktopFolder), 0777, true, true);
        }
        if (!File::exists($tabletFolder)) {
            File::makeDirectory(public_path($tabletFolder), 0777, true, true);
        }
        if (!File::exists($mobileFolder)) {
            File::makeDirectory(public_path($mobileFolder), 0777, true, true);
        }
        
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path($fileFolder), $fileName);
        $image = $manager->read(public_path($fileFolder) . '/' . $fileName);

        $originalWidth = $image->width();
        $originalHeight = $image->height();
        $ratio = $originalHeight / $originalWidth;
        // Desktop
        $imageDesktop = $image->resize(1024, 1024 * $ratio, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $imageDesktop->save(public_path($desktopFolder) . '/' . $fileName);

        // Tablet
        $imageTablet = $image->resize(768, 768 * $ratio, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $imageTablet->save(public_path($tabletFolder) . '/' . $fileName);

        // Mobile
        $imageMobile = $image->resize(600, (600 * $ratio), function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $imageMobile->save(public_path($mobileFolder) . '/' . $fileName);

        $result = [
            'original' => $fileName,
            'desktop' => $desktopFolder . '/' . $fileName,
            'tablet' => $tabletFolder . '/' . $fileName,
            'mobile' => $mobileFolder . '/' . $fileName,
        ];

        return $result;
    }
    public static function removeImage($fileName) {
        $fileFolder = '/storage';
        $desktopFolder = '/storage/desktop';
        $tabletFolder = '/storage/tablet';
        $mobileFolder = '/storage/mobile';
        // /storage/1706437939_book_reading_inspiration_113238_3840x2160.jpg  
        
        if (File::exists(public_path($fileFolder) . '/' . $fileName)) {
            File::delete(public_path($fileFolder) . '/' . $fileName);
        }
        if (File::exists(public_path($desktopFolder) . '/' . $fileName)) {
            File::delete(public_path($desktopFolder) . '/' . $fileName);
        }
        if (File::exists(public_path($tabletFolder) . '/' . $fileName)) {
            File::delete(public_path($tabletFolder) . '/' . $fileName);
        }
        if (File::exists(public_path($mobileFolder) . '/' . $fileName)) {
            File::delete(public_path($mobileFolder) . '/' . $fileName);
        }
    }
}