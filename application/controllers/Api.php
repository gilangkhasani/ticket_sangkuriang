<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;
class Api extends REST_Controller
{

	public function __construct() {
		//header('Access-Control-Allow-Origin: *');
		//header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		parent::__construct();
		$this->load->model('master_model');

	} 
	
	public function master_get(){
		$table_name = $this->uri->segment(3);
		$search = json_decode($this->get('search'), TRUE);
		
		$start = ($this->get('start') == "" ? 0 : $this->get('start'));
		$limitData = ($this->get('limitData') == "" ? 10 : $this->get('limitData'));
		//$limit = "LIMIT ".$start.", ".$limitData."";
		$limit = "";
		$order_by = "";
		if($this->get('order_field')){
			$order_by = "ORDER BY ".$this->get('order_field')." ".$this->get('order_sorted');
		}
		$r = $this->master_model->read($table_name, $search, $order_by, $limit);
		// Check if the users data store contains users (in case the database result returns NULL)
		if ($r) {
			// Set the response and exit
			//$this->response($r, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
			$this->response([
				'status' => TRUE,
				'message' => 'Data Found',
				'result' => $r,
			], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		} else {
			// Set the response and exit
			$this->response([
				'status' => FALSE,
				'message' => 'No Data were found'
			], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
		}
        
	}
	
	public function master_put(){
		$table_name = $this->uri->segment(3);
		
		$data = array();
		foreach ($this->get() as $key => $result) {
			if($key != 'token'){
				$data[$key] = $result;
			}
		}
		$field_name = $data['field_name'];
		$id = $data['id'];
		if( strpos( $id, ',' ) !== false ){
			$status_in = true;
		} else {
			$status_in = false;
		}
		$r = $this->master_model->update($data, $table_name, $id, $field_name, $status_in);
		if($r){
			$message = [
				'msg' => $r,
				'status' => TRUE,
				'result' => $r,
			];
			$this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
		} else {
			$message = [
				'msg' => 'Update Failed',
				'status' => FALSE,
				'result' => $r,
			];
			$this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
		}
	}

	public function master_post(){
		$table_name = $this->uri->segment(3);
		$data = array();
		//$input = json_decode(file_get_contents("php://input"));
		$input = $this->post();
		
		foreach ($input as $key => $result) {
			if($key != 'token' && $key != 'field_name' && $key != 'id' && $key != 'check_update' ){
				$data[$key] = $result;
			}
		}
		if(!empty($input->check_update)){
			$data_where = $input->id;
			
			$r = $this->master_model->update($data, $table_name, $data_where);
			$msg = "Updated";
		} else {
			$msg = "Inserted";
			$r = $this->master_model->insert($data, $table_name);
		}
		if($r){
			$message = [
				'msg' => 'Data '.$msg,
				'status' => TRUE,
				'result' => $r,
				'new_id' => $r
			];
			$this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
		} else {
			$message = [
				'msg' => $msg.' Failed',
				'status' => FALSE,
				'result' => $r,
			];
			$this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
		}
	}
	
	public function master_delete(){
		$table_name = $this->uri->segment(3);
		
		//$data_where = json_decode($this->input->get('id'));
		$input = $this->get();
		$data_where = $input['id'];
		
		$r = $this->master_model->delete($table_name, $data_where);
		if($r){
			$message = [
				'msg' => 'Data Deleted',
				'status' => TRUE,
				'result' => $r
			];

			$this->response($message, REST_Controller::HTTP_CREATED); // NO_CONTENT (204) being the HTTP response code
		} else {
			$message = [
				'msg' => 'Delete Failed',
				'status' => FALSE,
				'result' => $r
			];
			$this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
		}
	} 
	
	public function uploadImage($base64_file, $no_ticket, $title){
		if(!empty($base64_file)){
			list($type, $myimage2) = explode(';', $base64_file);
			list(, $myimage3) = explode(',', $myimage2);
			$decode_file = base64_decode($myimage3);
			$file_name = date('Ymd').'_'.date('His').'_'.$no_ticket.'_'.$title.'.png';
			$url = 'assets/images/ticket/'.$file_name;
			
			file_put_contents($url, $decode_file);
			return $file_name;
		}
	}
	
}