<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Files extends CI_Controller {
  function __construct(){
    parent::__construct();
    if (!$this->migration->current())
      show_error($this->migration->error_string());
    $this->load->helper('download');
  }

  public function detail() {
    if($this->current_user()===FALSE)return ;
    $filename = $_POST['file'];
    if(file_exists($this->realpath($filename))  === FALSE) return;
    $tmp = array(
      'src' => $this->realpath($filename),
      'name' => $filename,
      'ext' => pathinfo($this->realpath($filename),PATHINFO_EXTENSION)
      );
    if(is_image($tmp['ext']))
      $tmp['base64'] = base64_encode(file_get_contents($tmp['src']));
    else {
      
    }
    $this->data['file'] = $tmp;
    $this->load->view('detail', $this->data);
  }

  public function get() {
    if($this->current_user()===FALSE)return ;
    $filename = $_GET['file']; 
    $data = file_get_contents($this->realpath($filename));
    if($data === FALSE) return;
    force_download($filename, $data);
  }

  public function delete() {
    if($this->current_user()===FALSE)return ;
    $filename = $this->realpath($_POST['file']);
    $this->data['message'] = $this->do_delete($filename);
    $this->load->view('ajax',$this->data);
  }

  public function upload() {
    //if($this->current_user()===FALSE)return ;
    $this->data['message'] = $this->do_upload();
    $this->load->view('ajax',$this->data);
  }

  /************************ private ***********************/
  private function realpath($filename) {// security issue: file=../../../etc/init.d
    return "/home/root/sync/".$filename; 
  }
  private function do_delete($filename) {
    $pid = intval(shell_exec("ps -A | grep 'server.rb' | sed -nr 's/.([^ ]+).*/\\1/p'"));
    if($pid==0)return 'error';
    if(file_exists($filename) === FALSE)return 'error';
    unlink($filename);
    return $this->send_change('delete');
  }

  private function do_upload() {
    $pid = intval(shell_exec("ps -A | grep 'server.rb' | sed -nr 's/.([^ ]+).*/\\1/p'"));
    if($pid==0)return 'error: server dead';

    if($this->upload->do_upload('file')===FALSE)
      return 'error:'.$this->upload->display_errors();
    /*if($this->create_thumb($this->upload->data())===FALSE)
      return 'error:'.(var_dump(gd_info())).$this->image_lib->display_errors();
    $this->image_lib->clear();*/
    return $this->send_change('update');
  }

  private function create_thumb($image_data) {
    $this->load->library('image_lib');
    $file_path = $image_data['full_path'];
    $img_cfg=array();
    $img_cfg['image_library'] = 'gd2';
    $img_cfg['source_image'] = $file_path;
    $img_cfg['maintain_ratio'] = TRUE;
    $img_cfg['create_thumb'] = TRUE;
    $img_cfg['new_image'] = $file_path;
    $img_cfg['width'] = 100;
    $img_cfg['height'] = 100;

    $this->image_lib->initialize($img_cfg);
    $ret= $this->image_lib->resize();
    return $ret;
  }

  private function send_change($type) {
    $pid = intval(shell_exec("ps -A | grep 'server.rb' | sed -nr 's/.([^ ]+).*/\\1/p'"));
    if($pid==0)return 'error';
    $map=array('delete'=>SIGUSR1, 'update'=>SIGUSR2);
    posix_kill($pid, $map[$type]);
    return 'success';
  }

  private function current_user() {
    $user = $this->session->userdata('username');
    if($user===FALSE)$this->load->view('login');
    return $user;
  }
}
