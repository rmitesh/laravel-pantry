<?php

namespace Rmitesh\LaravelPantry\Pantries\Concerns;

use Illuminate\Pagination\LengthAwarePaginator;

trait HasPagination
{
    protected static ?int $perPageRecordLenght = 20;

    protected static function getPerPageRecordLenght(): ?int
    {
        return static::$perPageRecordLenght;
    }

    public function paginate( array $columns = ['*'], array $relationships = [] ): LengthAwarePaginator
    {
    	$records = $this->getEloquentQuery()
    		->select($columns)
    		->tap(fn ($records) =>
                $this->resolveRelationship($records, $relationships)
            );

        return $records->latest()
    		->paginate($this->getPerPageRecordLenght());
    }
}
