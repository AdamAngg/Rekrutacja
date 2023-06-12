<?php 
$url = "mysql://h0vtr8hvmv68opym:lq5jpgngn1e96ed1@c8u4r7fp8i8qaniw.chr7pe7iynqr.eu-west-1.rds.amazonaws.com:3306/stzxlcbiu7tjcih7";

$dbUrl = parse_url($url);

$config = [
    'host' => $dbUrl['host'],
    'username' => $dbUrl['user'],
    'password' => $dbUrl['pass'],
    'database' => substr($dbUrl['path'], 1),
];
// $config = [
//     'host' => 'localhost',
//     'username' => 'root',
//     'password' => '',
//     'database' => 'adrespect',
// ]
?>