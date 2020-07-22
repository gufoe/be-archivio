<?php

$data = file_get_contents('./cats.txt');
$data = explode("\n", $data);
$a = null;
$b = null;
$c = null;
$d = null;
$ret = [];
foreach ($data as $line) {
  $line = explode("\t", $line);
  if ($line[0]) { $a = $line[0]; }
  if ($line[1]) { $b = $line[1]; }
  if ($line[2]) { $c = $line[2]; }
  if ($line[4]) {
    $d = $line[4];
    $ret[$a][$b][$c][$d] = [];
  }
}

echo json_encode($ret);
