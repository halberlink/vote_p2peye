<?php

$redis = new Redis();
$redis->connect('127.0.0.1', 16379);
$redis->set("poll_status","1");
?>