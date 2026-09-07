<?php

namespace App\Http\Controllers\Prestamos;

use App\Http\Controllers\Controller;
use App\Models\Prestamos;
use App\Models\RecurrenciaPrestamos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrestamosController extends Controller {

    public function index() {
        return view('prestamos.index');
       /*
        $prestamos = Prestamos::where('status', 'A')->where('company_id', Auth::user()->company_id)->get();
        $recurrencia = RecurrenciaPrestamos::where('company_id', Auth::user()->company_id)->get();
        return view('prestamos/index')
                ->with('recurrencia', $recurrencia)
                ->with('prestamos', $prestamos);
                /*/

    }

    public function create() {
        //
    }

    public function store(Request $request) {
        //
    }

    public function show($id) {
        //
    }

    public function edit($id) {
        //
    }

    public function update(Request $request, $id) {
        //
    }

    public function destroy($id) {
        $prestamo = Prestamos::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $prestamo->status = 'E';
        $prestamo->save();
        return redirect('prestamos')->with('mensaje', 'Prestamo Eliminado exitosamente');
    }

}
