<?php
$pdf_file = isset($file) ? (string)$file : '';
$is_valid = $pdf_file !== ''
    && basename($pdf_file) === $pdf_file
    && !preg_match('/[\\x00-\\x1F\\x7F\\\\\/]/', $pdf_file)
    && preg_match('/\.pdf$/i', $pdf_file);

if(!$is_valid){
    show_error('Invalid PDF file.', 400);
}

$escaped_file = htmlspecialchars($pdf_file, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$pdf_url = rtrim(base_url(), '/') . '/assets/collections/file/' . rawurlencode($pdf_file);
$escaped_url = htmlspecialchars($pdf_url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
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
