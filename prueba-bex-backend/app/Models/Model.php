<?php

namespace App\Models;

use App\Traits\PaginatorTrait;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
	use PaginatorTrait;

	public $orderingColumns = [];
	public static function orderingColumns(): array
	{
		return [];
	}
}
