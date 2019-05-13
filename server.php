<?php
require_once 'jsonRPCServer.php';//PHP服务端RPC server 
// member 为测试类
// require_once 'member.php';
include 'member.php';
// 服务端调用
$myExample = new member();
// 注入实例
jsonRPCServer::handle($myExample);
?>