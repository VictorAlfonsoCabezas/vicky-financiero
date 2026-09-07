<?php

namespace App\Exports\Customer;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\Customer;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class CustomerExport implements FromView
{
   
    public function __construct() {}
    public function view(): View
    {
   
        $customer = Customer::all();
        $company = Company::find(Auth::user()->company_id);
        return view('reportes.clientes_export')
        ->with('company', $company)
            ->with('customer', $customer);
    }
}
