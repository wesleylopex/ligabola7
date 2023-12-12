<?php

function setFileName (string $clientFileName): string {
  $fileInfo = pathinfo($clientFileName);
  $extension = $fileInfo['extension'];
  $fileName = $fileInfo['filename'];
  
  return slugify($fileName). '-' . date('dmYHi') . '.' . $extension;
}

function deleteFileFromFolder (string $path): bool {
  if (!file_exists($path) || !is_file($path)) {
    return false;
  }

  return unlink($path);
}
