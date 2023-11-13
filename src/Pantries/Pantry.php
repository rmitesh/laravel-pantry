<?php

namespace Rmitesh\LaravelPantry\Pantries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class Pantry implements Contracts\Pantry
{
	use Concerns\InteractsWithRecord;

	/**
     * The model being queried.
     *
     * @var \Illuminate\Database\Eloquent\Model|null
     */
    protected static ?string $model = null;

    /**
     * Gets the name of the Eloquent model.
     *
     * @return string
     */
    public static function getModel(): string
    {
    	return static::$model;
    }

    /**
	 * Gets the Eloquent query builder.
	 * 
	 * @return Illuminate\Database\Eloquent\Builder
	 */
    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query();
    }

    /**
	 * To get single record from the model.
	 * 
	 * @param  \Illuminate\Database\Eloquent\Model|array|int|string  $record
	 * @param  array  	$relationships
	 * @param  array  	$columns
	 * @return \Illuminate\Database\Eloquent\Model|null
	 */

    public function get( Model | array | int | string $record, array $columns = ['*'], array $relationships = [] ): ?Model
    {
        if ( $record instanceof Model ) {
        	return $record;
        }

    	$query = static::getEloquentQuery()->select($columns);
    	if ( is_array($record) ) {
    		$query->where($record);
    	} else {
    		$routeKeyName = app(static::getModel())->getRouteKeyName();

    		$query->where($routeKeyName, $record);
    	}
		$this->resolveRelationship($query, $relationships);

        return $query->first();
    }

    /**
	 * To get all the records from the model.
	 * 
	 * @param  array<int, string>  $columns
	 * @param  array  $relationships
	 * @return Illuminate\Support\Collection
	 */
    public function getAll( array $columns = ['*'], array $conditions = [], array $relationships = [] ): Collection
    {
    	return static::getEloquentQuery()
    		->select($columns)
            ->tap(fn ($records) =>
                $this->resolveRelationship($records, $relationships)
            )
            ->tap(fn ($records) =>
                $this->resolveWhereCondition($records, $conditions)
            )
            ->latest()
            ->get();
    }

    /**
	 * Save a new model and return the instance.
	 * 
	 * @param  array  $data
	 * @return \Illuminate\Database\Eloquent\Model
	 */
    public function store( array $data ): Model
    {
    	return static::getModel()::create($data);
    }

    /**
	 * Update records in the model.
	 * 
	 * @param  \Illuminate\Database\Eloquent\Model|int|string  $key
	 * @param  array  $data
	 * @return \Illuminate\Database\Eloquent\Model
	 */
    public function update( Model | int | string $key, array $data ): Model
    {
    	return tap(static::resolveRecord($key), function ($instance) use ($data) {
			$instance->update($data);
		});
    }

    /**
	 * Delete record from the model.
	 * 
	 * @param  \Illuminate\Database\Eloquent\Model|int|string  $key
	 * @return bool
	 */
    public function destroy( Model | int | string $key ): bool
    {
    	return static::resolveRecord($key)->delete();
    }

    /**
	 * Bluk delete records from the model.
	 * 
	 * @param  Illuminate\Support\Collection|array  $ids
	 * @return bool
	 */
    public function destroyAll( Collection | array $ids ): bool
    {
    	$records = static::getEloquentQuery()
    		->whereIn('id', $ids)
    		->get();

    	$result = $records->each->delete();

    	return $result->count() !== 0;
    }
}
