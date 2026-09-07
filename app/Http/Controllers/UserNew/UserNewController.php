<?php

namespace App\Http\Controllers\UserNew;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserNewController extends Controller
{
    public function index()
    {
        return view('user-new.index');
    }

    public function show($id)
    {
        return view('user-new-edit.edit')->with('id', $id);
    }
}
