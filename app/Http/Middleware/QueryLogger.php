<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueryLogger
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Enable query logging
        DB::enableQueryLog();
        
        $response = $next($request);
        
        // Log all queries
        $queries = DB::getQueryLog();
        
        foreach ($queries as $query) {
            Log::info('SQL Query', [
                'sql' => $query['query'],
                'bindings' => $query['bindings'],
                'time' => $query['time'] . 'ms',
                'url' => $request->fullUrl(),
                'method' => $request->method()
            ]);
        }
        
        return $response;
    }
}
