<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerTipoAhorros;
use App\Models\CreditFolderHeader;
use App\Support\CustomerPortal;
use Illuminate\Http\Request;

class CustomerPortalController extends Controller
{
    public function index(Request $request)
    {
        $customer = $request->attributes->get('portal_customer') ?: CustomerPortal::customer($request->user());
        abort_unless($customer && $customer->status, 403);
        $accounts = CustomerTipoAhorros::with('tipoAhorros')
            ->where('company_id', $customer->company_id)->where('customer_id', $customer->id)
            ->orderByDesc('id')->paginate(10, ['*'], 'cuentas_page');
        $credits = CreditFolderHeader::where('company_id', $customer->company_id)->where('customer_id', $customer->id)
            ->orderByDesc('id')->paginate(10, ['code', 'valor_solicitado', 'status', 'date_created'], 'creditos_page');
        return view('portal.index', compact('customer', 'accounts', 'credits'));
    }
}
