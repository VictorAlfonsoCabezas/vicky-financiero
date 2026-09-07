<?php

namespace App\Support;

use App\Models\Customer;

class CustomerPortal
{
    public static function customer($user)
    {
        // Only persisted users can have a linked customer record.
        if (!$user || !$user->exists || !$user->company_id) return null;
        return Customer::where('company_id', $user->company_id)->where('user_id', $user->id)->first();
    }
}
