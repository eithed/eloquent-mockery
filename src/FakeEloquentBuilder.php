<?php

namespace Imanghafoori\EloquentMockery;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FakeEloquentBuilder extends Builder
{
    public function __construct($query, Model $modelObj)
    {
        $this->query = $query;
        $this->model = $modelObj;
    }

    public function addSelect($columns = ['*'])
    {
        $columns = is_array($columns) ? $columns : func_get_args();
        $this->query->columns = array_merge($this->query->columns ?? [], $columns);

        return $this;
    }

    public function delete()
    {
        try {
            return $count = parent::delete();
        } finally {
            if (is_int($count) && $count > 0) {
                FakeDB::setChangedModel('deleted', $this->model);
            }
        }
    }

    public function forceDelete()
    {
        try {
            return $count = parent::forceDelete();
        } finally {
            if ($count !== 0) {
                FakeDB::setChangedModel('deleted', $this->model);
            }
        }
    }

    public function update(array $values)
    {
        $this->model->getAttributes() && FakeDB::setChangedModel('updated', $this->model);

        return parent::update($values);
    }
}
