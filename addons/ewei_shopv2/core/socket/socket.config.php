<?php

/**
 * socket server配置文件，重启后生效
 */

// 开发模式开关
define('SOCKET_SERVER_DEBUG', false);

// 设置服务端IP
define('SOCKET_SERVER_IP', 'localhost');

// 设置服务端端口
define('SOCKET_SERVER_PORT', '9501');

// 设置是否启用SSL
define('SOCKET_SERVER_SSL', true);

// 设置SSL KEY文件路径
define('SOCKET_SERVER_SSL_KEY_FILE', '/usr/local/nginx/conf/ssl/214058193370257.key');

// 设置SSL CERT文件路径
define('SOCKET_SERVER_SSL_CERT_FILE', '/usr/local/nginx/conf/ssl/214058193370257.pem');

// 设置启动的worker进程数
define('SOCKET_SERVER_WORKNUM', 8);

// 设置客户端请求IP
define('SOCKET_CLIENT_IP', 'wx.weizancc.com');