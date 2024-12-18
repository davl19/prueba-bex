<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Traits\ResponseTrait;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use ResponseTrait;
    use ValidatesRequests;

    public $auth = null;

    public function __construct()
    {
        
    }
}
