<?php if (isset($ident)): ?>
<div class="alert alert-success">
  <p><strong>Shortened URL created!</strong> Your new URL is: <?=SITE_ROOT?><?=$ident?></p>
</div>
<?php endif; ?>

<form method="post" action="index.php" class="form-horizontal" role="form" name="form">
    <div class="form-group">
        <label for="url" class="col-sm-2 control-label">URL</label>
        <div class="col-sm-10">
            <input type="text" name="url" id="url" class="form-control" required />
        </div>
    </div>
    <div class="col-sm-10 col-sm-offset-2">
        <button type="submit" class="btn btn-primary">Create Short URL</button>
    </div>

</form>