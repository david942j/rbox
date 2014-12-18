<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class System extends CI_Controller {
	function __construct(){
		parent::__construct();
		if (!$this->migration->current())
			show_error($this->migration->error_string());
	}

	public function index() {
		if($this->current_user()===FALSE)return ;
		$this->data['user'] = $this->current_user();
		/*$db = $this->db->get('ap_list');
		$this->data['db']=$db;
		$this->data['tables'] = $this->db->list_tables();*/
		$this->data['cpuinfo'] = file_get_contents('/proc/cpuinfo');
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
			redirect('/system/index', 'refresh');
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

	public function wire() {
		if($this->current_user()===FALSE)return ;
		$this->data['user'] = $this->current_user();

		$this->data['network'] = $this->db->get('network');
		$this->load->view('wire', $this->data);
	}

	public function change_network() {
		if($this->current_user()===FALSE)return ;
		$this->data['user'] = $this->current_user();

		$data = array(
			'device' => $_POST['device'],
			'dynamic_flag' => $_POST['dhcp']=='Yes',
			'ip_addr' => $_POST['ip-addr'],
			'subnet_mask' => $_POST['mask'],
			'gateway' => $_POST['gateway'],
			'dns' => $_POST['dns']
		);
		$this->db->where('device',$_POST['old_device']);
		$this->db->update('network', $data);
	}

	public function delete_network() {
		if($this->current_user()===FALSE)return ;
		$this->data['user'] = $this->current_user();

		$this->db->delete('network', array('device'=>$_POST['old_device']));
	}

	public function new_network() {
		$this->data['message'] = '';
		$data = array(
			'device' => $_POST['device'],
			'dynamic_flag' => isset($_POST['dhcp']),
			'ip_addr' => $_POST['ip-addr'],
			'subnet_mask' => $_POST['mask'],
			'gateway' => $_POST['gateway'],
			'dns' => $_POST['dns']
		);
		$this->db->insert('network', $data);
		$this->load->view('ajax', $this->data);
	}
	public function do_network_setting() {
		if($this->current_user()===FALSE)return ;
		$this->data['user'] = $this->current_user();

		$device = $_POST['device'];
		$data = $this->db->get_where('network',array('device'=>$device))->first_row();
		$this->data['message']=shell_exec("ifconfig ".$data->device." ".$data->ip_addr);
		$this->load->view('ajax', $this->data);
	}

	public function wireless() {
		if($this->current_user()===FALSE)return ;
		$this->data['user'] = $this->current_user();
		$this->data['wpa_conf']=$this->db->get('ap_list');
		$this->load->view('wireless', $this->data);
	}

	public function change_ap() {
		if($this->current_user()===FALSE)return ;
		$this->data['user'] = $this->current_user();

		$data = array(
			'ssid' => $_POST['ssid'],
			'type' => $_POST['type'],
			'psk' => $_POST['psk'],
			'priority' => intval($_POST['priority'])
		);
		$this->db->where('ssid',$_POST['old_ssid']);
		$this->db->update('ap_list', $data);
	}

	public function delete_ap() {
		if($this->current_user()===FALSE)return ;
		$this->data['user'] = $this->current_user();

		$this->db->delete('ap_list', array('ssid'=>$_POST['old_ssid']));
	}

	public function new_ap() {
		$this->data['message'] = '';
		$data = array(
			'ssid' => $_POST['ssid'],
			'type' => $_POST['type'],
			'psk' => $_POST['psk'],
			'priority' => intval($_POST['priority'])
		);
		$this->db->insert('ap_list', $data);
		$this->load->view('ajax', $this->data);
	}

	public function do_ap_setting() {
		if($this->current_user()===FALSE)return ;
		$this->data['user'] = $this->current_user();

		$file = fopen("/etc/wpa1.conf","w");
		$arr = $this->get_ap_data();
		/*
			network={
				ssid="esys305-Dlink"
				psk="305305abcd"
				priority=7
			}
		*/
		foreach($arr as $row) {
			fprintf($file,"network={\n\tssid=\"%s\"\n\tpsk=\"%s\"\n\tpriority=%d\n}\n",$row['ssid'], $row['psk'], $row['priority']);
		}
		fclose($file);
	}

	public function import() {
		if($this->current_user()===FALSE)return ;
		$this->data['user'] = $this->current_user();

		$config['upload_path'] = './database/';
		$config['allowed_types'] = '*';
		$config['overwrite'] = TRUE;
		$config['max_size']	= '10000';

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload()) {
			$error = array('error' => $this->upload->display_errors());
			show_error($this->upload->display_errors());
		}
		else {
			$data = array('upload_data' => $this->upload->data());
			$this->db->reconnect();
			$this->index();
		}
	}

	private function current_user() {
		$this->check_data_created();
		$user = $this->session->userdata('username');
		if($user===FALSE)$this->load->view('login');
		return $user;
	}

	private function check_data_created() {
		$rows = $this->db->get_where('users', array('username' => 'david942j'));
		if($rows->num_rows()==0) {
			$this->db->insert('users', array(
					'username' => 'david942j',
					'password' => '48f7eb077038eb4b3addaf5982a1caef'
				));
		}

		$rows = $this->db->get('ap_list');
		if($rows->num_rows()==0) {
			$data = $this->parse_wpa();
			foreach($data as $row) {
				$this->db->insert('ap_list',$row);
			}
		}

		$rows = $this->db->get('network');
		if($rows->num_rows()==0) {
			$data = $this->get_network();
			foreach($data as $row) {
				$this->db->insert('network',$row);
			}
		}
	}

	private function encryption($str) {
		$salt = 'j&!*3dsaio';
		return md5($salt.$str);
	}

	private function parse_wpa() {
		$str = file_get_contents('/etc/wpa1.conf');
		$str = preg_replace('!\s+!', ' ', str_replace(array("{","}","=","\"")," ",$str));
		$arr = explode(' ', $str);
		$ret = array();
		$count=0;
		for($i=0;$i<count($arr);$i++) {
			if($arr[$i]=='network') {
				$ret[$count] = array(
					'ssid'=> $arr[$i+2],
					'type'=>'wpa',
					'psk'=> $arr[$i+4], 
					'priority'=> intval($arr[$i+6])
				);
				$count+=1;
				$i+=6;
			}
			else if($arr[$i]!='')show_error('error when parsing /etc/wpa1.conf'.$arr[$i]);
		}
		return $ret;
	}

	private function get_network() {
		$devices = explode("\n",shell_exec("ifconfig -a | sed 's/[ \t].*//;/^\(lo\|\)$/d'"));
		while(count($devices)>0 && end($devices)=="")array_pop($devices);
		$ret = array();
		foreach($devices as $dev) {
			$flag = shell_exec("grep -E '$dev.*dhcp' /etc/network/interfaces | grep -v '#'");
			$ip = shell_exec("ip addr show dev $dev | sed -nr 's/.*inet ([^ /]+).*/\\1/p'");
			$mask = shell_exec("ifconfig $dev | sed -nr 's/.*Mask:([^ ]+).*/\\1/p'");
			$gateway = '192.168.1.1';
			$dns = shell_exec("cat /etc/resolv.conf | sed -nr 's/.*nameserver ([^ ]+).*/\\1/p'");
			$ret[] = array(
				'device' => $dev,
				'dynamic_flag' => strlen($flag)>0,
				'ip_addr' => $ip,
				'subnet_mask' => $mask,
				'gateway' => $gateway,
				'dns' => $dns
			);
		}
		return $ret;
	}

	private function get_ap_data() {
		$data = $this->db->get('ap_list');
		$ret = array();
		foreach($data->result() as $row) {
			$ret[] = array(
				'ssid'=> $row->ssid,
				'psk'=> $row->psk, 
				'priority'=> $row->priority
			);
		}
		return $ret;
	}
}
