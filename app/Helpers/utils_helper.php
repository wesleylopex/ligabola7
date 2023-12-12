<?php

function icon (string $icon, ?string $extension = 'svg') {
  return file_get_contents(FCPATH . "icons/{$icon}.{$extension}");
}

function svg (string $name, ?string $extension = 'svg') {
  return file_get_contents(FCPATH . "images/{$name}.{$extension}");
}
