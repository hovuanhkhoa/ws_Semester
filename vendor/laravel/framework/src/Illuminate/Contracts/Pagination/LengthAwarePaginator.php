<?php

namespace Illuminate\Contracts\Pagination;

interface LengthAwarePaginator extends Paginator
{
    /**
     * Determine the total number of items in the data store.
     *
     * @return int
     */
    public function total();

    /**
     * Get the pages number of the last available pages.
     *
     * @return int
     */
    public function lastPage();
}
