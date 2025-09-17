<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CompressResponse
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        if ($this->shouldCompress($request, $response)) {
            $buffer = $response->getContent();
            
            if (str_contains($request->header('Accept-Encoding'), 'gzip')) {
                $buffer = gzencode($buffer, 9);
                $response->setContent($buffer);
                $response->headers->set('Content-Encoding', 'gzip');
                $response->headers->set('Content-Length', strlen($buffer));
                $response->headers->set('Vary', 'Accept-Encoding');
            }
        }
        
        return $response;
    }
    
    private function shouldCompress($request, $response)
    {
        if (!$response->isSuccessful()) {
            return false;
        }
        
        $contentType = $response->headers->get('Content-Type');
        
        if ($contentType && (
            str_contains($contentType, 'text/html') ||
            str_contains($contentType, 'text/css') ||
            str_contains($contentType, 'application/javascript') ||
            str_contains($contentType, 'application/json')
        )) {
            return true;
        }
        
        return false;
    }
}