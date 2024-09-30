<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Log;

class LocaleHelper
{
    CONST LOCALES = ['vi'];
    public static function convertBlogsToLocale($blogs, $locale = 'en')
    {
        if ($locale == 'en') {
            return $blogs;
        }
        $newBlogs = [];
        foreach ($blogs as $blog) {
            $newBlogs[] = self::convertBlogToLocale($blog, $locale);
        }
        return $newBlogs;
    }
    
    public static function convertBlogToLocale($blog, $locale = 'en') {
        if (!$locale || $locale == 'en') {
            return $blog;
        } 
        $newBlog = $blog;
        if (!isset($blog[$locale])) {
            $tranBlog = $blog;
        } else {
            $tranBlog = $blog[$locale];
        }
        if (isset($newBlog['title'])) {
            $newBlog['title'] = $tranBlog['title'] ?? $newBlog['title'];
        }
        if (isset($newBlog['summary'])) {
            $newBlog['summary'] = $tranBlog['summary'] ?? $newBlog['summary'];
        }
        if (isset($newBlog['tag'])) {
            $newBlog['tag'] = $tranBlog['tag'] ?? $newBlog['tag'];
        }
        if (isset($newBlog['slug'])) {
            $newBlog['slug'] = $tranBlog['slug'] ?? $newBlog['slug'];
        }
        if (isset($newBlog['meta_title'])) {
            $newBlog['meta_title'] = $tranBlog['meta_title'] ?? $newBlog['meta_title'];
        }
        if (isset($newBlog['meta_description'])) {
            $newBlog['meta_description'] = $tranBlog['meta_description'] ?? $newBlog['meta_description'];
        }
        if (isset($newBlog['excerpt'])) {
            $newBlog['excerpt'] = $tranBlog['excerpt'] ?? $newBlog['excerpt'];
        }
        if (isset($newBlog['suggest'])) {
            $newBlog['suggest'] = $tranBlog['suggest'] ?? $newBlog['suggest'];
        }
        if (isset($newBlog['detail'])) {
            if (isset($tranBlog['content'])) {
                $oldDetail = $newBlog['detail'];
                $oldDetail['content'] = $tranBlog['content'] ?? $oldDetail['content'];
                $oldDetail['content_draft'] = $tranBlog['content_draft'] ?? $oldDetail['content_draft'];
                $newBlog['detail'] = $oldDetail;
            }
        }
        if (isset($newBlog['estimate_time'])) {
            $newBlog['estimate_time'] = $tranBlog['estimate_time'] ?? $newBlog['estimate_time'];
        }
        if (isset($newBlog['categories'])) {
            $newBlog['categories'] = self::convertCategoriesToLocale($newBlog['categories'], $locale);
        }
        unset($newBlog[$locale]);

        return $newBlog;
    }

    public static function convertCategoriesToLocale($categories, $locale = 'en')
    {
        if (!$locale || $locale == 'en') {
            return $categories;
        }

        $newCategories = [];
        foreach ($categories as $category) {
            $newCategories[] = self::convertCategoryToLocale($category, $locale);
        }
        return $newCategories;
    }

    public static function convertCategoryToLocale($category, $locale = 'en')
    {
        if (!$locale || $locale == 'en') {
            return $category;
        }

        $newCategory = $category;
        if (!isset($category[$locale])) {
            $tranCategory = $category;
        } else {
            $tranCategory = $category[$locale];
        }

        if (isset($newCategory['name'])) {
            $newCategory['name'] = $tranCategory['name'] ?? $newCategory['name'];
        }
        if (isset($newCategory['slug'])) {
            $newCategory['slug'] = $tranCategory['slug'] ?? $newCategory['slug'];
        }
        if (isset($newCategory['meta_title'])) {
            $newCategory['meta_title'] = $tranCategory['meta_title'] ?? $newCategory['meta_title'];
        }
        if (isset($newCategory['meta_description'])) {
            $newCategory['meta_description'] = $tranCategory['meta_description'] ?? $newCategory['meta_description'];
        }
        if (isset($newCategory['description'])) {
            $newCategory['description'] = $tranCategory['description'] ?? $newCategory['description'];
        }
        if (isset($newCategory['parent'])) {
            $newCategory['parent'] = self::convertCategoryToLocale($newCategory['parent'], $locale);
        }
        if (isset($newCategory['childrens'])) {
            $newCategory['childrens'] = self::convertCategoriesToLocale($newCategory['childrens'], $locale);
        }
        unset($newCategory[$locale]);

        return $newCategory;
    }
}