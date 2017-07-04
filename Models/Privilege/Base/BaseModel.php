<?php
namespace App\Models\Privilege\Base;

use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege\DBLog;

class BaseModel extends Model
{
	
	protected $connection = 'ft_privilege';
// 	protected $hidden = [];
	protected $hidden = array('disable_reason', 'updated_at', 'created_by', 
// 	'pivot'
	);
	protected $ignoreTables = array('db_log', 'otp', 'instamojo_request', 'instamojo_payment', 'instamojo_log', 'session', 'transaction', 'migrations', 'aa_templet');
	
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
	
	public function save(array $options = []){
		$doLog = false;

		if(!in_array($this->table, $this->ignoreTables)){
			$doLog = true;

			if($this->exists){
				$beforeSave = $this->original;
				$action = 'update';
			}
			else{
				$beforeSave = false;
				$action = 'insert';
			}
		}
		$saved = parent::save($options);

		if($doLog && isset($_SESSION['admin_id'])){

			$DBlog = new DBLog();
			$DBlog->entity_table = $this->table;
			$DBlog->entity_id = $this->id;
			$DBlog->action = $action;
			if($beforeSave)
				$DBlog->before_save = json_encode($beforeSave);

			$DBlog->after_save = json_encode($this->attributes);
			$DBlog->created_by = $_SESSION['admin_id'];
			$DBlog->created_user_name = $_SESSION['email'];
			$DBlog->save();
		}
		return $saved;
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