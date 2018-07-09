<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;

// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';

// 加载公共配置文件
require __DIR__ . '/../extend/common.php';
require __DIR__ . '/../extend/chromephp.php';
require __DIR__ . '/../extend/UtilImage.php';
// 支持事先使用静态方法设置Request对象和Config对象



// 应用入口文件
define('APP_PATH', __DIR__ . '/app/');
define('CSS', __DIR__ . '/../public/static');

// 后台配置文件
define('ADMIN_PATH', __DIR__ . '/../application/admin/');

define('ADMIN_TEMPLATE_PATH', __DIR__ . '/../application/admin/config/common/page/');

define('ADMIN_CSS_PATH', __DIR__ . '/../application/admin/config/common/css/');

define('ADMIN_JS_PATH', __DIR__ . '/../application/admin/config/common/css/');

define('ADMIN_IMAGES_PATH', __DIR__ . '/../application/admin/config/common/images/');

define('__PUBLIC__', __DIR__ . '/../public');

define('__HOST__','https://cwly.mengyayuer.com');

define('APP_AUTO_BUILD',true); //开启自动生成

define('APP_DEBUG', true);// 开启调试模式

define('MODULE','module');



// 执行应用并响应
Container::get('app')->run()->send();