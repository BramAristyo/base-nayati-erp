<?php

namespace App\Repositories\Legacy\Accounting;

use Illuminate\Support\Facades\DB;

class AccountTypeRepository 
{
   public function getAll()
   {
        $query = DB::table('uacc')
            ->select(
                'uacc.id',
                'uacc.kd6 as code',
                'uacc.ket1 as name'
            )
            ->orderBy('uacc.kd6', 'asc')
            ->get();

        return $query->toArray();
   }
}