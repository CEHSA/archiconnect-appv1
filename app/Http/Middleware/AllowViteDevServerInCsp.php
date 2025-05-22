<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowViteDevServerInCsp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only modify CSP if we're in local development - be very strict about this check
        if (app()->environment('local') && !app()->environment('production')) {
            $currentCsp = $response->headers->get('Content-Security-Policy');
            // Add all variations of the Vite dev server URL
            $viteDevUrls = [
                'http://[::1]:5173',
                'http://localhost:5173', 
                'http://127.0.0.1:5173'
            ];
            
            if ($currentCsp) {
                // Attempt to append to existing script-src and style-src
                $directives = explode(';', $currentCsp);
                $newDirectives = [];
                $scriptSrcModified = false;
                $styleSrcModified = false;
                $defaultSrcModified = false;
                
                foreach ($directives as $directive) {
                    $directive = trim($directive);
                    
                    if (stripos($directive, 'script-src') === 0) {
                        // Check if the directive already contains the Vite URLs
                        foreach ($viteDevUrls as $url) {
                            if (strpos($directive, $url) === false) {
                                $directive .= " " . $url;
                            }
                        }
                        $scriptSrcModified = true;
                    } 
                    elseif (stripos($directive, 'style-src') === 0) {
                        // Check if the directive already contains the Vite URLs
                        foreach ($viteDevUrls as $url) {
                            if (strpos($directive, $url) === false) {
                                $directive .= " " . $url;
                            }
                        }
                        $styleSrcModified = true;
                    }
                    elseif (stripos($directive, 'default-src') === 0) {
                        // Check if the directive already contains the Vite URLs
                        foreach ($viteDevUrls as $url) {
                            if (strpos($directive, $url) === false) {
                                $directive .= " " . $url;
                            }
                        }
                        $defaultSrcModified = true;
                    }
                    
                    $newDirectives[] = $directive;
                }

                if (!$scriptSrcModified) {
                    $newDirectives[] = "script-src 'self' 'unsafe-inline' 'unsafe-eval' " . implode(' ', $viteDevUrls);
                }
                if (!$styleSrcModified) {
                    $newDirectives[] = "style-src 'self' 'unsafe-inline' " . implode(' ', $viteDevUrls);
                }
                if (!$defaultSrcModified) {
                    $newDirectives[] = "default-src 'self' " . implode(' ', $viteDevUrls);
                }
                
                $response->headers->set('Content-Security-Policy', implode('; ', $newDirectives));
            } else {
                // If no CSP header exists, set a basic one allowing Vite
                $viteUrlsString = implode(' ', $viteDevUrls);
                $response->headers->set('Content-Security-Policy', 
                    "default-src 'self' {$viteUrlsString}; " .
                    "script-src 'self' 'unsafe-inline' 'unsafe-eval' {$viteUrlsString}; " .
                    "style-src 'self' 'unsafe-inline' {$viteUrlsString}; " .
                    "font-src 'self'; img-src 'self' data:; object-src 'none'; base-uri 'self';"
                );
            }
        }

        return $response;
    }
}
