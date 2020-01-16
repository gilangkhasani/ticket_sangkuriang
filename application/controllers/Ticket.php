<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;
class Ticket extends REST_Controller
{

	public function __construct() {
		//header('Access-Control-Allow-Origin: *');
		//header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		parent::__construct();
		$this->load->model('master_model');

	} 
	
	public function index_get(){
        
	}
	
	public function index_put(){
	}

	public function index_post(){
		$table_name = $this->uri->segment(1);
		//$input = json_decode(file_get_contents("php://input"));
		$input = $this->post();
		
		$data = array(
			'no_ticket'	=> $input['no_ticket'],
			'site_id'	=> $input['site_id'],
			'requestor'	=> $input['requestor'],
			'longitude'	=> $input['longitude'],
			'latitude'	=> $input['latitude'],
			'alamat'	=> $input['alamat'],
			'time_request'	=> $input['time_request'],
			'time_accept'	=> $input['time_accept'],
			'time_backup'	=> $input['time_backup'],
			'time_finish'	=> $input['time_finish'],
			'meter_hour_before'	=> $input['meter_hour_before'],
			'meter_hour_after'	=> $input['meter_hour_after'],
			'meter_pln_before'	=> $input['meter_pln_before'],
			'meter_pln_after'	=> $input['meter_pln_after'],
			'photo_meter_hour_before'	=> $this->uploadImage($input['photo_meter_hour_before'], $input['no_ticket'], "photo_meter_hour_before"),
			'photo_meter_hour_after'	=> $this->uploadImage($input['photo_meter_hour_after'], $input['no_ticket'], "photo_meter_hour_after"),
			'photo_meter_pln_before'	=> $this->uploadImage($input['photo_meter_pln_before'], $input['no_ticket'], "photo_meter_pln_before"),
			'photo_meter_pln_after'	=> $this->uploadImage($input['photo_meter_pln_after'], $input['no_ticket'], "photo_meter_pln_after"),
			'status_ticket'	=> $input['status_ticket'],
			'cluster'	=> $input['cluster'],
			'id_mbp'	=> $input['id_mbp'],
		);
		$r = $this->master_model->ticketUpdate($data, $table_name, "no_ticket", $input['no_ticket']);
		$msg = "Updated";
		if($input){
			$message = [
				'msg' => 'Data '.$msg,
				'status' => TRUE,
				'result' => $data,
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
	
	public function index_delete(){
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
	
	public function generateTicketNumber_get(){
		$table_name = $this->uri->segment(1);
		$r = $this->master_model->generateNumber($table_name);
		if($r){
			$message = [
				'msg' => 'ID number ',
				'status' => TRUE,
				'result' => $r,
			];
			$this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
		} else {
			$message = [
				'msg' => 'Failed',
				'status' => FALSE,
				'result' => $r,
			];
			$this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
		}
	}
	
}