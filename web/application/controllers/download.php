<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Download extends CI_Controller {
  function __construct(){
    parent::__construct();
    if (!$this->migration->current())
      show_error($this->migration->error_string());
    $this->load->helper('download');
  }

  public function get() {
    if($this->current_user()===FALSE)return ;
    $filename = $_GET['file']; 
    $data = file_get_contents("/home/root/sync/".$filename);
    if($data === FALSE) return;
    force_download($filename, $data);
  }

  private function current_user() {
    $user = $this->session->userdata('username');
    if($user===FALSE)$this->load->view('login');
    return $user;
  }
}
