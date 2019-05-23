# laravel-page-cache

页面静态文件缓存，加快网站访问速度

## 安装

使用 Composer 安装
```shell
composer require  helingfeng/laravel-page-cache 1.0.5
php artisan vendor:publish
```

## 路由文件中使用

在需要缓存的路由中添加`page_cache`中间件

```php
Route::group(['middleware' => ['page_cache']], function($router){
    $router->get('page-demo', function () {
            return '<h2>this page is cached.</h2>';
    });
});

```

## 缓存示例

访问：https://youdomain.com/page-demo
