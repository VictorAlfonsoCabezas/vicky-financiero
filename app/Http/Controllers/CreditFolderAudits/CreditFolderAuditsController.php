<?php

namespace App\Http\Controllers\CreditFolderAudits;

use App\Http\Controllers\Controller;

class CreditFolderAuditsController extends Controller
{
    public function index()
    {
        return view('credit-folder-audits.index');
    }
}
