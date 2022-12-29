<?php

namespace App\Transactions;

use Costa\DomainPackage\UseCase\Interfaces\DatabaseTransactionInterface;
use Illuminate\Support\Facades\DB;

class DatabaseTransaction implements DatabaseTransactionInterface
{
    public function __construct()
    {
        DB::beginTransaction();
    }

    public function commit()
    {
        DB::commit();
    }

    public function rollback()
    {
        DB::rollback();
    }
}
