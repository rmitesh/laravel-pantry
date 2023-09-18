# Laravel Pantry

You may know and worked with repository pattern in the Laravel.

Now, here I am going to introduce **Laravel Pantry** where you get all things in one place same as Food Pantry Shop.

![laravel-pantry](https://github.com/rmitesh/laravel-pantry/assets/48554454/8b2976b7-04e6-495b-bb6c-a957bbfe0339)

<p align="center">
<a href="https://packagist.org/packages/rmitesh/laravel-pantry"><img alt="Latest Stable Version" src="https://img.shields.io/packagist/v/rmitesh/laravel-pantry.svg?style=for-the-badge"></a>
<a href="https://packagist.org/packages/rmitesh/laravel-pantry"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/rmitesh/laravel-pantry.svg?style=for-the-badge"></a>
<a href="https://laravel.com"><img alt="Laravel v10.x" src="https://img.shields.io/badge/Laravel-v10.x-FF2D20?style=for-the-badge&logo=laravel"></a>
<a href="https://php.net"><img alt="PHP 8.1" src="https://img.shields.io/badge/PHP-8.1-777BB4?style=for-the-badge&logo=php"></a>
</p>

## Installation

You can install the package via composer:

```bash
composer require rmitesh/laravel-pantry
```

### Create a new Pantry

To create a new Pantry class
```bash
php artisan make:pantry FoodPantry Food
```

It will create `FoodPantry` class inside the `app/Pantries` directory.
and `Food` is model class name.

So, final directory structure will look like this.

```
|── app
    └── Http
        └── Controllers
            └── FoodController.php
    └── Models
        └── Food.php
    └── Pantries
        └── FoodPantry.php
```

## How to use

In your `FoodController` add  `__construct` function.

```php
use App\Pantries\FoodPantry;

class FoodController extends Controller
{
	public function __construct(
	    protected FoodPantry $foodPantry
	) {}
}
```

OR

```php
use App\Pantries\FoodPantry;

class FoodController extends Controller
{
	/**
	 * @var FoodPantry
	 */
	protected $foodPantry;

	public function __construct(FoodPantry $foodPantry) {
		$this->foodPantry = $foodPantry;
	}
}
```

## Available Methods

### get()

To fetch single record.

```php
$food = $this->foodPantry->get($record);
```

| Argument    	 | Value 																	 			 |
| -------------- | ------------------------------------------------------------------------------------- |
| $record  	 	 | It accepts modal ID ( `Illuminate\Database\Eloquent\Model`, `string`, `id` )			 |
| $relationships | It's Optional, You can pass relationship arguments in array. see <a href="#fetch-data-with-relationships">relationships</a> examples  			 |

> It will also works with Route Model Binding.

It will return eloquent class on record found, else it will throw `404 | Not found`.

### getAll()

To fetch multiple records.

```php
$foods = $this->foodPantry->getAll([
	// your column name, default '*'
]);
```

With relationships, it's an optional parameter.
More details, check <a href="#fetch-data-with-relationships">Fetch data with relationships</a>.

```php
$foods = $this->foodPantry->getAll([
	'id', 'name', 'created_at'
], [
	'with' => 'relationshipName',
]);
```

| Argument    	 | Value 																	 			   |
| -------------- | --------------------------------------------------------------------------------------- |
| $columns  	 | Column names in array format, by default it will be `['*']` 				  			   |
| $relationships | It's Optional, You can pass relationship arguments in array. see <a href="#fetch-data-with-relationships">relationships</a> examples |

As a return it will give collection.

### paginate()

To fetch records with pagination.

To use `paginate()` method, you have to use `Rmitesh\LaravelPantry\Pantries\Concerns\HasPagination`.

```php
use Rmitesh\LaravelPantry\Pantries\Concerns\HasPagination;

class FoodPantry extends Pantry
{
	use HasPagination; // add this in your Pantry class
}

```

For change per page records limit, you can override the `$perPageRecordLenght` in your Pantry class.

```php
protected static ?int $perPageRecordLenght = 20;
```
> By default `$perPageRecordLenght` value set is `20` and that is enough for per page records.

```php
$foods = $this->foodPantry->paginate([
		// your column name, default '*'
]);
```

Or with relationships

```php
$foods = $this->foodPantry->paginate([
		// your column name, default '*'
], [
    'with' => 'ingredients',
]);
```

As a return it will give `Illuminate\Pagination\LengthAwarePaginator` collection.

### store()

To store data in table.

You need to just pass the array data.

```php
$food = $this->foodPantry->store($request->validated());
```

| Argument   	 | Value 				|
| -------------- | -------------------- |
| $data  	 	 | Array key-value pair |

As a return it will give created model instance.

### update()

To update data in table.

```php
$food = $this->foodPantry->update($key, $request->validated());
```

| Argument   	 | Value 														   			 	 |
| -------------- | ----------------------------------------------------------------------------- |
| $key  	 	 | It accepts record ID ( `Illuminate\Database\Eloquent\Model`, `string`, `id` ) |
| $data  	 	 | Array key-value pair 										   			 	 |

As a return it will give updated model instance.

### destroy()

To delete single record.

```php
$food = $this->foodPantry->destroy($key);
```

| Argument   	 | Value 														   				 |
| -------------- | ----------------------------------------------------------------------------- |
| $key  	 	 | It accepts record ID ( `Illuminate\Database\Eloquent\Model`, `string`, `id` ) |
| $data  	 	 | Array key-value pair 										   				 |

As a return true on record deleted, false on failure.

### destroyAll()

To delete multiple records at once.

```php
$food = $this->foodPantry->destroyAll($ids);
```

| Argument   	 | Value 														   				 |
| -------------- | ----------------------------------------------------------------------------- |
| $ids  	 	 | It accepts record ID in array or `Illuminate\Support\Collection` format		 |

As a return true on record deleted, false on failure.

### Fetch data with relationships.

To add relationships, you can pass in array format.

Let's take an example,
One `Food` has many `ingredients`. So, to fetch `Food` records along with their `Ingredients`.

```php
$foods = $this->foodPantry->getAll(
    relationships: [
        'with' => 'ingredients',
    ]
);
```

> For relationships, as key it should be relationship name like `with`, `withWhereHas`, `whereHas`, `withCount`, `whereBelongsTo` and so on.

Moreover, if you want to add `Closure` function then

```php
use Illuminate\Database\Eloquent\Builder;

$foods = $this->foodPantry->getAll(
    relationships: [
        'with' => [
        	'ingredients',
        	function ( Builder $query ) {
	        	// your conditions
	        }
        ],
    ]
);
```

Or if you have multiple relationships then,

```php
use Illuminate\Database\Eloquent\Builder;

$foods = $this->foodPantry->getAll(
    relationships: [
        'with' => [
        	[
        		'ingredients' => function ( Builder $query ) {
		        	// your conditions
		        },
        	],
        	[
        		// second relationship ...
        	],
        ],
    ]
);
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Buy me a Coffee
<a href="https://www.buymeacoffee.com/rmitesh" target="_blank">
	<img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" alt="Buy Me A Coffee" style="height: 60px !important;width: 217px !important;" >
</a>

## Credits

- [Mitesh Rathod](https://github.com/rmitesh)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
