<?php

session_start();
$host = "localhost";
$dbname = "uks_smkn1pacitan";
$username = "root";
$password = "";

$db = new PDO("mysql:host=$host;dbname=$dbname",$username,$password);