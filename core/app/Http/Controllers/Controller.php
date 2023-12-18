<?php

namespace App\Http\Controllers;

use App\Models\EnumType;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public $responseOutput = [ 'success' => false, 'message' => 'An error occured.' ];

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
