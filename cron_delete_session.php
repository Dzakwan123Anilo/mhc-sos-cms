<?php

echo "Running Delete CI Session Script...\n";

$directory = '/tmp';
$filenames = glob($directory . '/ci_session*');

echo "=> Total files is " . count($filenames) . "\n";
echo "=> Clearing CI Session files...\n";

foreach ($filenames as $filename) {
  unlink($filename);
}

echo "=> CI Session files successfuly cleared!\n";

?>
