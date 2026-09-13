<?php
$file = isset($_GET['file']) ? (string)$_GET['file'] : '';
$is_valid = $file !== ''
    && basename($file) === $file
    && !preg_match('/[\\x00-\\x1F\\x7F\\\\\/]/', $file)
    && preg_match('/\.pdf$/i', $file);

if(!$is_valid){
    http_response_code(400);
    exit('Invalid PDF file.');
}

$escaped_file = htmlspecialchars($file, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$file_url = '../assets/collections/file/' . rawurlencode($file);
$escaped_url = htmlspecialchars($file_url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>File <?php echo $escaped_file; ?></title>
    <style>
      html, body, object { width: 100%; height: 100%; margin: 0; }
      p { box-sizing: border-box; padding: 1rem; font-family: sans-serif; }
    </style>
  </head>
  <body>
    <object data="<?php echo $escaped_url; ?>" type="application/pdf">
      <p>Browser tidak dapat menampilkan PDF.
        <a href="<?php echo $escaped_url; ?>">Unduh file PDF</a>.
      </p>
    </object>
  </body>
</html>
