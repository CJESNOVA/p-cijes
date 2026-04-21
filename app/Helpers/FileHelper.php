<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class FileHelper
{
    public static function sanitizeFileName(string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $ext  = pathinfo($filename, PATHINFO_EXTENSION);

        $name = Str::ascii($name);
        $name = preg_replace('/[^A-Za-z0-9\-_]/', '_', $name);
        $name = preg_replace('/_+/', '_', $name);
        $name = trim($name, '_');

        return strtolower($name . '.' . $ext);
    }
}
