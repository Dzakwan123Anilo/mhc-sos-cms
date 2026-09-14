<?php
$safe_file = basename((string) $file);
$file_url = base_url().'assets/collections/file/'.rawurlencode($safe_file);
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>File <?php echo htmlspecialchars($safe_file, ENT_QUOTES, 'UTF-8');?></title>
    <style>html,body,iframe{border:0;height:100%;margin:0;padding:0;width:100%}p{font-family:sans-serif;padding:1rem}</style>
  </head>
  <body>
    <iframe src="<?php echo htmlspecialchars($file_url, ENT_QUOTES, 'UTF-8');?>" title="PDF <?php echo htmlspecialchars($safe_file, ENT_QUOTES, 'UTF-8');?>">
      <p>Browser tidak mendukung pratinjau PDF. <a href="<?php echo htmlspecialchars($file_url, ENT_QUOTES, 'UTF-8');?>">Unduh PDF</a>.</p>
    </iframe>
  </body>
</html>
