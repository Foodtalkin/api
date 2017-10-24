<?php

namespace App\Http\Controllers\Privilege;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Privilege\Experiences;
use App\Models\Privilege\ExpData;

class ExperiencesController extends Controller {

	// gets a user with id
	public function get(Request $request, $id, $with = false) {
		$exp = Experiences::find ( $id );
		$exp->data;
		$exp->city;
		
		return $this->sendResponse ( $exp);
	}
	
	public function getAll(Request $request) {
		
		$pageSize = Experiences::PAGE_SIZE;
		
		if (isset ($_SESSION ['admin_id'] ))
		{	
			$pageSize = 20;
			
			if(isset($_GET['is_active']))
				$where['is_active'] = $_GET['is_active'];
			
			if(isset($_GET['is_disabled']))
					$where['is_disabled'] = $_GET['is_disabled'];
			else
				$where['is_disabled'] = '0';
		}else {
			$where['is_active'] = '1';
			$where['is_disabled'] = '0';
		}
		
		$exp = Experiences::where( $where )->paginate($pageSize);
		
		
		return $this->sendResponse ( $exp );
	}
	
	public function create(Request $request) {
		
		$attributes =	$request->getRawPost(true);
		unset($attributes['is_active']);
		unset($attributes['is_disabled']);
		$result = Experiences::create ( $attributes );
		
		return $this->sendResponse ( $result );
	}
	
	public function update(Request $request, $id) {
		
		$attributes = $request->getRawPost(true);
		unset($attributes['is_active']);
		unset($attributes['is_disabled']);
		$exp = Experiences::find ( $id );
		$exp->update ( $attributes );
		
		return $this->sendResponse ( $exp);
	}

	public function delete($id) {
		$exp = Experiences::find ( $id );
		
		if ($exp) {
			$exp->is_active = 0;
			$exp->is_disabled = 1;
			$exp->save();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'Experiences Disabled' );
		} else {
			return $this->sendResponse ( null );
		}
	}
	
	public function activate($id){
		
		$exp = Experiences::find ( $id );
		
		if ($exp) {
			$exp->is_active = 1;
			$exp->is_disabled = 0;
			$exp->save();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'Experiences activated' );
		} else {
			return $this->sendResponse ( null );
		}
	}
	
	public function deactivate($id){
		
		$exp = Experiences::find ( $id );
		
		if ($exp) {
			$exp->is_active = 0;
			$exp->is_disabled = 0;
			$exp->save();
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'Experiences deactivated' );
		} else {
			return $this->sendResponse ( null );
		}
	}
	
	
	public function addData(Request $request, $id) {
		
		$attributes =	$request->getRawPost(true);
		$attributes['exp_id'] = $id;
		$result = ExpData::create ( $attributes );
		
		return $this->sendResponse ( $result );
	}
	
	
	public function updateData(Request $request, $id) {
		
		$attributes =	$request->getRawPost(true);
		unset($attributes['exp_id']);
		$result = ExpData::find ( $id );
		if($result)
			$result->update ( $attributes );
			
		return $this->sendResponse ( $result );
	}
	
	public function deleteData(Request $request, $id) {
		
		$result = ExpData::find ( $id );
		if($result){
			$result->delete;
			return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'deleted' );
		}
		return $this->sendResponse ( false );
	}
	
	
	public function sortData(Request $request, $id) {
		$attributes = $request->getRawPost(true);
		
		foreach ($attributes as $order => $data ){
			$exp_data = ExpData::where(['id'=>$data, 'exp_id'=>$id])->first();
			if($exp_data){
				$exp_data->sort_order = $order;
				$exp_data->save();
			}
		}
		return $this->sendResponse ( true, self::REQUEST_ACCEPTED, 'order updated' );
	}

	
}
?>