<?php
use app\ExceptionHandle;
use app\Request;

// 容器Provider定义文件
return [
    // 不可预知的错误文件
    'think\exception\Handle' => 'app\\api\\exception\\Http',
];
