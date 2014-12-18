<!DOCTYPE html>
<html>
<title>rbox</title>
<body style="background-color:#ffffff">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?link_tag(css_url().'bootstrap2.3.2min.css');?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
<?=link_tag(css_url().'layout.css');?>
<?=link_tag(css_url().'login.css');?>
<?=link_tag(css_url().'wire.css');?>

<script src="http://code.jquery.com/jquery.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
<script src="<?= js_url() ?>jquery.serialize-hash.js"></script>
<? if(isset($user)) {?>
<nav class="navbar navbar-inverse navbar-static-top" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">rbox</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
        <li><a href="#">Link</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
            <li class="divider"></li>
            <li><a href="#">One more separated link</a></li>
          </ul>
        </li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
          <li class="">
            <a><?= $user?></a>
          </li>
          <li class="">
            <a data-target="#change-password-modal" data-toggle='modal'>修改密碼</a>
          </li>
        </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<!--
<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="brand" href="<?= base_url() ?>">BeagleBoard</a>
      <div class="nav-collapse collapse">
        <ul class="nav">
          <li class="">
            <a href="<?= base_url() ?>">Home</a>
          </li>
          <li class="dropdown">
            <a id="drop1" href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">網路設定 <b class="caret"></b></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="drop1">
              <li role="presentation"><a role="menuitem" tabindex="-1" href="<?= site_url() ?>/system/wire/">網路資訊</a></li>
              <li role="presentation"><a role="menuitem" tabindex="-1" href="<?= site_url() ?>/system/wireless">無線網路</a></li>
              <li role="presentation" class="divider"></li>
              <li role="presentation"><a role="menuitem" target="_blank" tabindex="-1" href="http://www.youtube.com/watch?v=vieVk_n2ECQ">喵喵叫</a></li>
            </ul>
          </li>
          <li class="dropdown">
            <a id="drop1" href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">資料整理 <b class="caret"></b></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="drop1">
              <li role="presentation"><a role="menuitem" tabindex="-1" data-toggle='modal' data-target="#import-modal">資料匯入</a></li>
              <li role="presentation"><a role="menuitem" tabindex="-1" href="<?= base_url() ?>database/sys">資料匯出</a></li>
            </ul>
          </li>
          <li class="dropdown">
            <a id="drop1" href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">系統管理 <b class="caret"></b></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="drop1">
              <li role="presentation"><a role="menuitem" tabindex="-1" ></a></li>
              <li role="presentation"><a role="menuitem" tabindex="-1" href=""></a></li>
            </ul>
          </li>
        </ul>
        <ul class="nav pull-right">
          <li class="">
            <a><?= $user?></a>
          </li>
          <li class="">
            <a href="#change-password-modal" data-toggle='modal'>修改密碼</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
-->
<!-- modals -->
<div id='import-modal' class="modal fade hide" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">資料匯入</h4>
      </div>
      <div class="modal-body">
        <form method="post" action="<?= site_url() ?>/system/import" enctype="multipart/form-data">
    <input type="file" name="userfile" size="20" />
    <br/><br/>
    <input class='btn btn-info' type="submit" value="upload"/>
    </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div id='change-password-modal' class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">修改密碼</h4>
      </div>
      <div class="modal-body">
        <form id='change-password-form' role='form' method='post' action="<?=site_url()?>/system/change_password" data-remote="true">
          <div class="form-group">
          <label for="old_password">Old Password</label>
          <input name='old_password' type="password" class="form-control" id="old_password">
      </div>
        <div class="form-group">
          <label for="new_password">New Password</label>
          <input name='new_password' type="password" class="form-control" id="new_password">
      </div>
      <div class="form-group">
          <label for="confirm_password">Confirm Password</label>
          <input name='confirm_password' type="password" class="form-control" id="confirm_password">
      </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="$('#change-password-form').submit();">Submit</button>
      </div>
    </div>
  </div>
</div>
<script>
  $(function(){
      $('#change-password-form').submit(function(evnt){
        if($('#new_password').val()!=$('#confirm_password').val())
          alert('Password not match');
          $.post($(this).attr('action'),
            $(this).serializeHash(),
            function (data) {
              if(data=='error')return alert('password incorrect');
              location.reload();
            });
          return false;
      });
  });
</script>
<? } ?>
<div class='layout'>
