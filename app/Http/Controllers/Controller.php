<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function __invoke(Request $request,$name=null)
    {
        if(empty($name)) {
            $name = 'index';
        }
        if( method_exists($this,$name) ) {
            return $this->{$name}($request);
        } else {
            return ['code' => 2004,'msg'=> "{$name} 404"];
        }
    }
}
