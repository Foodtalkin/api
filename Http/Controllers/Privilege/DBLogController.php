<?php

namespace App\Http\Controllers\Privilege;

// use DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Privilege\DBLog;
// use Illuminate\Http\JsonResponse;

class DBLogController extends Controller {

	
	
	
	
	// gets a user with id
	public function get(Request $request, $entity, $id = false) {
		
		$where['entity_table'] = $entity;
		
		if($id)
			$where['entity_id'] = $id;
		
		$result =  DBLog::where($where)
		->paginate(DBLog::PAGE_SIZE);		
		
		return $this->sendResponse ( $result );
	}

}
?>