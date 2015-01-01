<?php include 'header.php'; ?>
<form id="login" action='<?=site_url()?>/main/login' method="post">
    <h1>Log In</h1>
    <fieldset id="inputs">
        <input name="username" id="username" type="text" placeholder="Username" autofocus required>   
        <input name="password" id="password" type="password" placeholder="Password" required>
    </fieldset>
    <fieldset id="actions">
        <input type="submit" id="submit" value="Log in">
    </fieldset>
</form>

<?php include 'footer.php'; ?>