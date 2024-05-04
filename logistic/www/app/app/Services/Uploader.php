<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class Uploader
{
    public function upload($files)
    {
        $paths = [];

        $optimizerChain = OptimizerChainFactory::create();

        foreach ($files as $file) {
            $newFilename = Str::random() . '.' . $file->getClientOriginalExtension();

            $_image = $file->move(public_path('files'), $newFilename);

            $optimizerChain->optimize(public_path("files/$newFilename"));

            $paths[] = $_image->getFilename();
        }

        return $paths;
    }

    public function uploadAvatar($base64)
    {
        $fileData = base64_decode($base64);

        $fileName = Str::uuid() . '.jpg';

        Storage::put("public/avatars/$fileName", $fileData);

        return "/storage/avatars/$fileName";
    }
}
