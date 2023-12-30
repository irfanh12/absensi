<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\EnumType;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public $responseOutput = [ 'success' => false, 'message' => 'An error occured.' ];

    public function dateDay($dateDay) {
        $carbonDate = Carbon::parse($dateDay);
        $carbonDate->locale('id');

        return $carbonDate->isoFormat('dddd');
    }

    public function storeImage($image) {
        // Decode the base64-encoded string
        list($type, $data) = explode(';', $image);
        list(, $data) = explode(',', $data);
        $imageDataDecoded = base64_decode($data);

        $extension = explode('/', mime_content_type($image))[1];

        // Generate a unique filename for the image
        $filename = uniqid() . '.' . $extension;

        // Store the image file
        Storage::disk('public')->put($filename, $imageDataDecoded);

        return asset("/storage/$filename");
    }

    public function enumType($user_type) {
        $index = ['user_type'];
        $user_type = collect($index)->push(
            EnumType::getConstant(
                strtoupper($user_type)
            )
        );
        return [ $user_type->implode(':') ];
    }
}
