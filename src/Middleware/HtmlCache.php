<?php

namespace PageCache\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class HtmlCache
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->ajax()) {
            $fileName = $this->getRealFileNameByPath($request->getRequestUri());
            $override = $request->input('override', 0);
            if (is_file($fileName) && !$override) {
                return response(file_get_contents($fileName));
            }
            $response = $next($request);

            $this->addHtmlFile($response, $fileName, $override);
            return $response;
        } else {
            return $next($request);
        }
    }

    public function getRealFileNameByPath($path)
    {
        $prefix = storage_path('framework/cache/static_page');
        $key = app('config')->get('page_cache.file_directory_key', 'static_html_file_directory');
        $expires = app('config')->get('page_cache.file_expires', 5);
        if(Cache::has($key)){
            $directory = Cache::get($key, date('YmdHis'));
        }else{
            $directory = date('YmdHis');
            Cache::put($key, $directory, $expires);
        }

        $terminal = app('agent')->isMobile() ? 'mobile' : 'desktop';
        $fullPath = $prefix . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . $terminal;
        if (!is_dir($fullPath)) {
            try {
                mkdir($fullPath, 0775, true);
            } catch (\Exception $e) {
                // ...
            }
        }
        $fileName = md5($path);
        return $fullPath . DIRECTORY_SEPARATOR . $fileName;
    }

    public function addHtmlFile(Response $response, $fileName, $override)
    {
        if ($response->getStatusCode() == 200) {
            $content = $response->getContent();
            try {
                if (!is_file($fileName) || $override) {
                    file_put_contents($fileName, $content, LOCK_EX);
                }
            } catch (\Exception $e) {
                // ...
            }
        }
    }
}
