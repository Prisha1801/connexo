<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XssSanitization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        
        // Only sanitize string values, preserve arrays and other types
        array_walk_recursive($input, function (&$value, $key) {
            if (is_string($value)) {
                // Allow safe HTML tags, strip dangerous ones
                $value = strip_tags($value, '<p><br><strong><em><u><ul><ol><li><a><h1><h2><h3><h4><h5><h6>');
                // Remove javascript: and data: protocols
                $value = preg_replace('/(javascript|data|vbscript):/i', '', $value);
                // Remove on* event handlers
                $value = preg_replace('/on\w+\s*=/i', '', $value);
            }
        });
        
        $request->merge($input);

        return $next($request);
    }
}
