<?php

namespace App\Models\Entities;

use App\Models\Entities\Behavior\Dated;

class ApiRequest
{
    use Dated;

	public $created_at;
	public $method;
	public $model;
	public $property;
	public $id;
	public $profileid;
}
