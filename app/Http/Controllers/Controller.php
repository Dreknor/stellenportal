<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

abstract class Controller
{
    /**
     * Store an uploaded image in the media collection of the same name.
     *
     * The file name is generated server-side from the detected MIME type, so a
     * client-supplied extension (e.g. ".html") never ends up on the public disk.
     */
    protected function storeUploadedImage(HasMedia $model, Request $request, string $collection): void
    {
        $file = $request->file($collection);

        $model->addMedia($file)
            ->usingFileName(Str::uuid() . '.' . $file->guessExtension())
            ->toMediaCollection($collection);
    }
}
