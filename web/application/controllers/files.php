<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Files extends CI_Controller {
  function __construct(){
    parent::__construct();
    if (!$this->migration->current())
      show_error($this->migration->error_string());
    $this->load->helper('download');
  }

  public function get() {
    if($this->current_user()===FALSE)return ;
    $filename = $_GET['file']; 
    $data = file_get_contents("/home/root/sync/".$filename);// security issue: file=../../../etc/passwd
    if($data === FALSE) return;
    force_download($filename, $data);
  }

  public function delete() {
    if($this->current_user()===FALSE)return ;
    $filename = "/home/root/sync/".$_POST['file']; // security issue: file=../../../etc/init.d
    $this->data['message'] = $this->do_delete($filename);
    $this->load->view('ajax',$this->data);
  }

  private function do_delete($filename) {
    $pid = intval(shell_exec("ps -A | grep 'server.rb' | sed -nr 's/.([^ ]+).*/\\1/p'"));
    if($pid==0)return 'error';
    if(file_exists($filename) === FALSE)return 'error';
    unlink($filename);
    return $this->send_change();
  }
  private function send_change() {
    $pid = intval(shell_exec("ps -A | grep 'server.rb' | sed -nr 's/.([^ ]+).*/\\1/p'"));
    if($pid==0)return 'error';
    posix_kill($pid, SIGUSR1);
    return 'success';
  }

  private function current_user() {
    $user = $this->session->userdata('username');
    if($user===FALSE)$this->load->view('login');
    return $user;
  }
}
