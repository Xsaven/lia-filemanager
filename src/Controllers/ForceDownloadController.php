<?php

namespace Lia\Filemanager\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ForceDownloadController extends Controller
{

    public function index(Request $request)
    {
        dd($request->all());
    }

}