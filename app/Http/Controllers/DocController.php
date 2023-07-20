<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Doc;

class DocController extends Controller
{
    public function index(){
        $array = ["error" => ""];

        $docs = Doc::all();
        $array["list"] = [];

        forEach($docs as $doc){
            $doc["file_url"] = asset("storage/".$doc["fileurl"]);
            $array["list"][] = $doc;
        };


        return $array;
    }
}
