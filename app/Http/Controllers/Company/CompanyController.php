<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\Category;
use App\User;
use Redirect;
use Response;
use Image;
use Illuminate\Support\Facades\Auth;
use File;

class CompanyController extends Controller
{

    public function index()
    {
        $company = Company::where('status', true)->get();
        $cantidad = count($company);
        return view('company/index')
            ->with('company', $company)
            ->with('cantidad', $cantidad);
    }

    public function create()
    {
        $category = Category::where('type', 2)->where('status', true)->get();
        return view('company/create')->with('category', $category);
    }

    public function store(Request $request)
    {
        $data = [
            "company_name" => strtoupper($request->input('company_name')),
            "company_type" => $request->input('company_type'),
            "category_type" => ($request->input('company_type') !== 1) ? $request->input('category_type') : null,
            "comercial_name" => strtoupper($request->input('comercial_name')),
            "ruc" => $request->input('ruc'),
            "legal_representative" => $request->input('legal_representative'),
            "address" => strtoupper($request->input('address')),
            "phone" => $request->input('phone'),
            "email" => $request->input('email'),
            "conexion" => ($request->input('electronica') == 'on') ? true : false,
            "electronica" => ($request->input('conexion') == 'on') ? true : false,
            "domicilio" => ($request->input('company_type') !== 1) ? $request->input('domicilio') : 0,
            "ciudad" => $request->input('ciudad'),
            "pais" => $request->input('pais'),
            "obligar_garante" => $request->input('obligar_garante'),
            "porcentaje_retener_credito" => $request->input('porcentaje_retener_credito'),
            "select_tipo_interes" => $request->input('select_tipo_interes'),
            "numero_dias_interes" => $request->input('numero_dias_interes'),
        ];
        $company = Company::create($data);
        if ($request->file('photo') !== null) {
            $path_archivo_destino = 'public/uploads/companies';
            if (!file_exists($path_archivo_destino)) {
                File::makeDirectory($path_archivo_destino, 0777, true);
            }
            $imagen = $request->file('photo');
            $nombre = time() . '.' . $imagen->getClientOriginalExtension();
            $destino = public_path('uploads/companies');
            $request->photo->move($destino, $nombre);
            $company->photo = $nombre;
            $company->save();
        }
        return redirect('company')->with('mensaje', 'Empresa creada con exito');
    }

    public function edit(Company $company)
    {
        $category = Category::where('type', 2)->where('status', true)->get();
        return view('company/edit')
            ->with('company', $company)
            ->with('category', $category);
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'fecha_inicio_contable' => $request->input('contabilidad') != null ? 'required|date' : 'nullable|date',
        ], [
            'fecha_inicio_contable.required' => 'La fecha de inicio contable es obligatoria cuando la contabilidad esta activa.',
            'fecha_inicio_contable.date' => 'La fecha de inicio contable no es valida.',
        ]);

        //dd($request->all());
        $company->company_name = strtoupper($request->input('company_name'));
        $company->comercial_name = strtoupper($request->input('comercial_name'));
        $company->ruc = $request->input('ruc');
        $company->porcentaje_mora = $request->input('mora');
        $company->legal_representative = strtoupper($request->input('legal_representative'));
        $company->address = strtoupper($request->input('address'));
        $company->phone = $request->input('phone');
        $company->email = $request->input('email');
        $company->porcentaje_desgravament = $request->input('desgravament');
        $company->porcentaje_mora = $request->input('mora');
        $company->ciudad = $request->input('ciudad');
        $company->pais = $request->input('pais');
        $company->obligar_garante = $request->input('obligar_garante');
        $company->porcentaje_retener_credito = $request->input('porcentaje_retener_credito');
        $company->select_tipo_interes = $request->input('select_tipo_interes');
        $company->numero_dias_interes = $request->input('numero_dias_interes');
        $company->numero_decimales = $request->input('numero_decimales');
        $company->dias_inicio_cobro = $request->input('dias_inicio_cobro');
        $company->dias_gracia = $request->input('dias_gracia');
        $company->letra_cambio = ($request->input('letra_cambio') != null) ? 1 : 0;
        $company->pagare = ($request->input('pagare') != null) ? 1 : 0;
        $company->time_cron = ($request->input('time_cron') != null && $request->input('time_cron')  != '') ? ($request->input('active_cron') != null) ? $request->input('time_cron') : null : null;
        $company->color_texto = $request->input('color_texto');
        $company->color_tabla = $request->input('color_tabla');
        $company->margen_top = $request->input('margen_top');
        $company->margen_dow = $request->input('margen_dow');
        $company->margen_right = $request->input('margen_right');
        $company->margen_left = $request->input('margen_left');
        $company->active_cron = ($request->input('active_cron') != null) ? 1 : 0;
        $company->contabilidad = ($request->input('contabilidad') != null) ? 1 : 0;
        $company->enviar_mails = ($request->input('enviar_mails') != null) ? 1 : 0;
        $company->cartola_a = $request->input('cartola_a');
        $company->cartola_b = $request->input('cartola_b');
        $company->genera_gastos_cobranza = ($request->input('genera_gastos_cobranza') != null) ? 1 : 0;
        $company->penalidad_plazo_fijo = $request->input('penalidad_plazo_fijo');
        $company->fecha_inicio_contable = ($request->input('contabilidad') != null) ? $request->input('fecha_inicio_contable') : null;
        $company->nombre_primer_gasto_credito = strtoupper($request->input('nombre_primer_gasto_credito'));
        $company->nombre_segundo_gasto_credito = strtoupper($request->input('nombre_segundo_gasto_credito'));
        $company->nombre_tercer_gasto_credito = strtoupper($request->input('nombre_tercer_gasto_credito'));
        $company->interes_fijo_parametrizado = ($request->input('interes_fijo_parametrizado') != null) ? 1 : 0;
        $company->reporte_cartera_unido = ($request->input('reporte_cartera_unido') != null) ? 1 : 0;
        $company->caja_boveda = ($request->input('caja_boveda') != null) ? 1 : 0;
        if ($request->input('genera_gastos_cobranza') != null) {
            $company->valor_notificado =  $request->input('valor_notificado');
        } else {
            $company->valor_notificado = 0;
        }
        $company->save();
        if ($request->file('photo') !== null) {


            $path_archivo_destino = 'public/uploads/companies';
            if (!file_exists($path_archivo_destino)) {
                File::makeDirectory($path_archivo_destino, 0777, true);
            }
            $imagen = $request->file('photo');
            if ($company->photo !== null) {
                $nombre = $company->photo;
                $borrar = public_path() . '/uploads/companies/' . $company->photo;
                if (file_exists($borrar)) {
                    unlink($borrar);
                }
            } else {
                $nombre = time() . '.' . $imagen->getClientOriginalExtension();
            }
            $destino = public_path('uploads/companies');
            $request->photo->move($destino, $nombre);
            $company->photo = $nombre;
            $company->save();
        }
        return redirect("company")->with('mensaje', 'Empresa Editada');
    }

    public function destroy($id)
    {
        $company = Company::find($id);
        $company->status = false;
        $company->save();
        return redirect('company')
            ->with('mensaje', 'Compania Eliminada Satisfactoriamente...');
    }

    public function changeCompany($id)
    {
        $user = User::find(Auth::user()->id);
        $allowed = array_filter(explode(',', (string) $user->company_varias));
        $allowed[] = (string) $user->company_id;
        abort_unless(in_array((string) $id, array_map('trim', $allowed), true), 403);
        Company::where('status', true)->findOrFail($id);
        $user->company_id = $id;
        $user->save();
        return Response::json(true);
    }

    public function conexionCompanies()
    {
        return Response::json(Company::select('id', 'conexion')->where('status', true)->where('principal', false)->get());
    }

    public function desactivarCompany($id)
    {
        $company = Company::findOrFail($id);
        $company->status = !$company->status;
        $company->save();
        return Response::json($company);
    }
}
