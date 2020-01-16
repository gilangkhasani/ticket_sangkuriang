<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;
class Auth extends REST_Controller
{

	public function __construct() {
		//header('Access-Control-Allow-Origin: *');
		//header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		parent::__construct();
		$this->load->model('auth_model');

	} 
	
	public function index_get(){}

	public function logout_post(){
		//$input = json_decode(file_get_contents("php://input"));
		$input = $this->post();
		$data = array(
			'token'	=> $input->token,
			'status_token'	=> '1',
			'logout_date'	=> date("Y-m-d H:i:s"),
		);
		$table_name = "token_auth";
		$r = $this->auth_model->logout($data, $table_name);
		
		if($r){
			$message = [
				'msg' => "Thanks For Logout",
				'status' => TRUE,
				'result' => $r
			];
			$this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
		} else {
			$message = [
				'msg' => 'Logout Failed',
				'status' => FALSE,
				'result' => $r,
			];
			$this->response($message, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
		}
	}
	
	public function login_post(){
		$data = array();
		//$input = json_decode(file_get_contents("php://input"));
		$input = $this->post();
		foreach ($input as $key => $result) {
			if($key != 'token' && $key != 'field_name' && $key != 'id' && $key != 'check_update'){
				$data[$key] = $result;
			}
		}
		$table_name = "users_view";
		$msg = "Login";
		$r = $this->auth_model->login($data, $table_name);
		if($r){
			$token = $r->username . "|" . uniqid() . uniqid("itbs|jabar") . uniqid();
			$message = [
				'msg' => $msg. " Completed",
				'token' => $token,
				'status' => TRUE,
				'result' => $r
			];
			$this->set_response($message, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		
		} else {
			$message = [
				'msg' => $msg.' Failed',
				'status' => FALSE,
				'result' => FALSE,
			];
			$this->response($message, REST_Controller::HTTP_SERVICE_UNAVAILABLE); // HTTP_SERVICE_UNAVAILABLE (503) being the HTTP response code
		}
	}
	
	public function register_post(){
		$this->load->model('master_model');
		$data = array();
		//$input = json_decode(file_get_contents("php://input"));
		$input = $this->post();
		foreach ($input as $key => $result) {
			if($key != 'token' && $key != 'field_name' && $key != 'id' && $key != 'check_update'){
				if($key == 'password'){
					$data[$key] = md5($result);
				} else {
					$data[$key] = $result;
				}
			}
		}
		$table_name = "users";
		$msg = "Register";
		$r = $this->master_model->insert($data, "users");
		if($r == 0){
			$message = [
				'msg' => $msg. " Completed",
				'status' => TRUE,
				'result' => $r
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
}