<?php

namespace Rmitesh\LaravelPantry\Pantries\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;

trait InteractsWithRecord
{
    protected function resolveRecord( Model | int | string $key): Model
    {
    	if ( $key instanceof Model ) {
    		$record = $key;
    	} else {
    		$record = static::resolveRecordRouteBinding($key);

	        if ($record === null) {
	            throw (new ModelNotFoundException())->setModel(static::getModel(), [$key]);
	        }
    	}

        return $record;
    }

    public static function resolveRecordRouteBinding(int | string $key): ?Model
    {
        return app(static::getModel())
            ->resolveRouteBindingQuery(static::getEloquentQuery(), $key)
            ->first();
    }

    protected function resolveRelationship(Builder $query, array $relationships): Builder
    {
        if ( $relationships ) {
            foreach ( $relationships as $functionName => $argument ) {
                if (
                    is_string($argument) ||
                    ( is_array($argument) && Arr::isAssoc($argument) )
                ) {
                    $query->{$functionName}($argument);
                } else {
                    $query->{$functionName}(...$argument);
                }
            }
        }
        return $query;
    }
}