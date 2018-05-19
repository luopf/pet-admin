<?php



//配置文件
    $root = __DIR__.'/public/';
return [
    'view_replace_str'  => [
        '__PUBLIC__' => $root,
        '__JS__' => $root.'/static/js',
        '__CSS__' =>$root.'/static/css',
        '__IMG__' => $root.'/static/images',
        '__FONT__'=>$root.'/static/images/fonts',
        '__LOD__' =>$root.'/upload',
        '__TEMPLETE__' => $root.'templete/',
    ],
];