<?php
namespace App\Models\Privilege\Base;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
	
	protected $connection = 'ft_privilege';
	
	
	const PAGE_SIZE = 10;
	const MAX_PAGE_SIZE = 50;
	
	
	public static function create(array $attributes = [])
	{
	
		if(isset($_SESSION['admin_id'])){
			$attributes['created_by'] = $_SESSION['admin_id'];
		}
		
		if(isset($attributes['metadata']))
			$attributes['metadata'] = json_encode($attributes['metadata']);
		
		return parent::create($attributes);
	}
	
	
	public function update(array $attributes = [])
	{
		unset($attributes['created_by']);
// 		unset($attributes['created_at']);
		
		if(isset($attributes['metadata']))
			$attributes['metadata'] = json_encode($attributes['metadata']);
		
		return parent::update($attributes);
	}
	
	
}