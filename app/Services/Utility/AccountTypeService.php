<?php

namespace App\Services\Utility;

use App\Repositories\Legacy\Accounting\AccountTypeRepository;

class AccountTypeService
{
    /**
     * AccountTypeService constructor.
     */
    public function __construct(
        protected AccountTypeRepository $repository
    ) {}

    /**
     * Get all account types.
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->repository->getAll();
    }
}
