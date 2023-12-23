<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AdminAlreadyAuth implements FilterInterface {
  public function before (RequestInterface $request, $arguments = null) {
    if (session()->get('adminId')) {
      return redirect()->to('/admin/home');
    }
  }
  
  public function after (RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}