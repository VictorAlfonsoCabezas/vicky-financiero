<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Company;
use App\Models\Category;
use Illuminate\Http\Request;
use Redirect;
use Response;
use Illuminate\Support\Facades\Auth;
use Image;

class ProductController extends Controller
{

    public function index()
    {
        return view('product/index');
    }

    public function saveCateLayapa(Request $request)
    {
        $request->validate(['parametros.categoria' => 'required|string|max:255']);
        $category = new Category();
        $category->company_id = Auth::user()->company_id;
        $category->title = strtoupper($request->input('parametros.categoria'));
        $category->type = 4;
        $category->type_name = 'CATEGORIAS DE PRODUCTOS LAYAPA';
        $category->status = true;
        $category->save();
        return Response::json(Category::where('company_id', Auth::user()->company_id)->where('type', 4)->get());
    }
}
