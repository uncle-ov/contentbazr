<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
	protected $guarded = ['tags'];
	public $timestamps = false;

	public function images()
	{
		return $this->hasMany(Images::class)->where('status','active');
	}

}
