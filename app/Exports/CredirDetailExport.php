<?php

namespace App\Exports;

use App\Models\CreditFolderDetail;
use Maatwebsite\Excel\Concerns\FromCollection;

class CredirDetailExport implements FromCollection {

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection() {
        return CreditFolderDetail::all();
    }

}
