<?php

namespace Rmitesh\LaravelPantry\Pantries\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface Pantry
{
	/**
     * Gets the name of the Eloquent model.
     *
     * @return string
     */
	public static function getModel(): string;

	/**
	 * Gets the Eloquent query builder.
	 * 
	 * @return Illuminate\Database\Eloquent\Builder
	 */
	public static function getEloquentQuery(): Builder;

	/**
	 * To get single record from the model.
	 * 
	 * @param  \Illuminate\Database\Eloquent\Model|array|int|string  $record
	 * @param  array  	$relationships
	 * @param  array  	$columns
	 * 
	 * @return \Illuminate\Database\Eloquent\Model|null
	 */
	public function get( Model | array | int | string $record, array $columns = ['*'], array $relationships = [] ): ?Model;

	/**
	 * To get all the records from the model.
	 * 
	 * @param  array<int, string>  $columns
	 * @param  array  $relationships
	 * 
	 * @return Illuminate\Support\Collection
	 */
	public function getAll( array $columns = ['*'], array $relationships = [] ): Collection;

	/**
	 * Save a new model and return the instance.
	 * 
	 * @param  array  $data
	 * 
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function store( array $data ): Model;

	/**
	 * Update records in the model.
	 * 
	 * @param  \Illuminate\Database\Eloquent\Model|int|string  $key
	 * @param  array  $data
	 * 
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function update( Model | int | string $key, array $data ): Model;

	/**
	 * Delete record from the model.
	 * 
	 * @param  \Illuminate\Database\Eloquent\Model|int|string  $key
	 * 
	 * @return bool
	 */
	public function destroy( Model | int | string $key ): bool;

	/**
	 * Bluk delete records from the model.
	 * 
	 * @param  Illuminate\Support\Collection|array  $ids
	 * 
	 * @return bool
	 */
	public function destroyAll( Collection | array $ids ): bool;
}
