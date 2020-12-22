<?php
// Default API Domain 默认 API 服务域名
$server = 'http://api.mysubmail.com/';

// SMS 应用ID
$message_configs['appid'] = '59079';

// SMS 应用密匙
$message_configs['appkey'] = 'ad47b92f5bf1f824ddffa11bdf0dca00';

// SMS  验证模式
// md5=md5 签名验证模式（推荐）
// sha1=sha1 签名验证模式（推荐）
// normal=密匙明文验证
$message_configs['sign_type']='normal';

// API 服务器节点配置
$message_configs['server']=$server;






