<?php

namespace App\Http\Controllers\Codev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CodevController extends Controller {

    public function index() {
        return view('codev.index');
    }

}