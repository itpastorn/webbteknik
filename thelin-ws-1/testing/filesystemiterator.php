<?php
/**
 * Demo av FileSystemIterator
 *
 */

$it = new FilesystemIterator(dirname(__FILE__));
foreach ($it as $fileinfo) {
  echo $fileinfo->getFilename() . "\n";
}

