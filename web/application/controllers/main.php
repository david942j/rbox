<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {
	function __construct(){
		parent::__construct();
		if (!$this->migration->current())
			show_error($this->migration->error_string());
	}

	public function index() {
		if($this->current_user()===FALSE)return ;
		$this->data['user'] = $this->current_user();
		$this->data['files'] = $this->parse_files('/home/root/sync/');

		$this->load->view('index', $this->data);
	}

	public function login() {
		$username = $_POST['username'];
		$password = $this->encryption($_POST['password']);
		$rows = $this->db->get_where('users', array('username' => $username,'password'=>$password));
		if($rows->num_rows()==0) {
			show_error('login error.');
			return ;
		}
		else {
			$this->session->set_userdata(array('username'=>$username));
			redirect('http://192.168.1.22/rbox', 'refresh');
		}
	}

	public function change_password() {
		if($this->current_user()===FALSE)return ;
		$this->data['user'] = $this->current_user();
		if($this->db->get_where('users', array(
				'username' => $this->data['user'],
				'password' => $this->encryption($_POST['old_password'])
			))->num_rows()==0) {
			$this->load->view('ajax', array('message'=>'error'));
		}
		else {
			$this->db->where('username', $this->data['user']);
			$this->db->update('users', array('password'=>$this->encryption($_POST['new_password'])));
		}
	}

	private function current_user() {
		$user = $this->session->userdata('username');
		if($user===FALSE)$this->load->view('login');
		return $user;
	}

	private function encryption($str) {
		$salt = 'j&!*3dsaio';
		return md5($salt.$str);
	}

	private function parse_files($directory) {
		$files = array_diff(scandir($directory), array('..', '.'));
	  $ret = array();
	  foreach($files as $file) {
	  	$tmp = array();
	  	$tmp['src'] = $directory.$file;
	    $tmp['name'] = $file;
	    $tmp['ext'] = pathinfo($tmp['src'],PATHINFO_EXTENSION);
	    $tmp['modify_time'] = date ("Y/m/d H:i:s",filemtime($tmp['src'])+8*3600);
	    if(is_image($tmp['ext'])) {
	    	$tmp['base64'] = base64_encode(file_get_contents($tmp['src']));
	    }
	    else {

	    }
	    $ret[] = $tmp;
	  }
	  return $ret;
	}
}
