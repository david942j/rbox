<div class="drop_upload">
  <article class="row page">
    <div>
      <header class="header">
      <h1>Upload files</h1>
      </header>
      <form action="#">
        <div class="dropped"></div>
        <div class="filelists">
          <h5>Complete</h5>
          <ol class="filelist complete"></ol>
          <h5>Queued</h5>
          <ol class="filelist queue"></ol>
        </div>
      </form>
    </div>
  </article>
</div>
<script>
  $(function() {
    new DropUploader($('.drop_upload'), "<?= site_url().'/files/upload' ?>");
  });
</script>