<?php

namespace App\Transactions;

use Illuminate\Support\Facades\DB;
use Costa\DomainPackage\UseCase\Interfaces\DatabaseTransactionInterface;

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
