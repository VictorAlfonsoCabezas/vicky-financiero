<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerSearchController extends Controller
{
    public function index(Request $request)
    {
        $request->merge(['q' => is_string($request->input('q')) ? trim($request->input('q')) : $request->input('q')]);
        $request->validate(['q' => 'required|string|min:2|max:120', 'page' => 'sometimes|integer|min:1']);
        abort_unless($request->user()->company_id, 403);

        $query = Customer::where('company_id', $request->user()->company_id);
        foreach (preg_split('/\s+/u', $request->input('q'), -1, PREG_SPLIT_NO_EMPTY) as $word) {
            $query->where(function ($match) use ($word) {
                $match->where('numero_documento', 'like', '%' . $word . '%')
                    ->orWhere('code', 'like', '%' . $word . '%')
                    ->orWhere('nombres', 'like', '%' . $word . '%')
                    ->orWhere('apellidos', 'like', '%' . $word . '%');
            });
        }
        $customers = $query->select('id', 'code', 'nombres', 'apellidos', 'numero_documento')
            ->orderBy('apellidos')->orderBy('nombres')->orderBy('id')->paginate(20);

        return response()->json([
            'customers' => $customers->map(function ($customer) {
                return [
                    'code' => $customer->code,
                    'name' => trim($customer->nombres . ' ' . $customer->apellidos),
                    'document' => $customer->numero_documento,
                    'credit_url' => url('creditos/' . $customer->id),
                    'savings_url' => url('cuentas/' . $customer->id),
                    'customer_url' => url('clientes/' . $customer->id),
                ];
            })->values(),
            'total' => $customers->total(),
            'page' => $customers->currentPage(),
            'last_page' => $customers->lastPage(),
        ])->header('Cache-Control', 'no-store, private');
    }
}
