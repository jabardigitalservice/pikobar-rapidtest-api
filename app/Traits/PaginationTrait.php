<?php

namespace App\Traits;

trait PaginationTrait
{
    protected function getValidParams($request)
    {
        return $request->except(['per_page', 'sort_by', 'sort_order',  'search', 'page']);
    }

    protected function getValidSortOders($sortOrder, $defaultOrder = 'desc')
    {
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = $defaultOrder;
        }

        return $sortOrder;
    }

    protected function getValidPerpage($perpage)
    {
        if (!in_array($perpage, [15, 50, 100, 500])) {
            $perpage = 15;
        }

        return $perpage;
    }

    protected function getRecords($records, $perpage)
    {
        if ($perpage == 'ALL') {
            return $records->get();
        }

        return $records->paginate($this->getValidPerpage($perpage));
    }

    protected function getValidOrderBy($orderBy, $defaultOrderBy = 'created_at')
    {
        if (!in_array($orderBy, $this->sort)) {
            $orderBy = $defaultOrderBy;
        }

        return $orderBy;
    }
}
