<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AlreadyAuth implements FilterInterface {
    public function before (RequestInterface $request, $arguments = null) {
      if (session()->get('teamId')) {
        return redirect()->to('/manager/home');
      }
    }
    
    public function after (RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}