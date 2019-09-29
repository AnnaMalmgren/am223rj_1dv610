<?php

$dbHost = getenv("DB_HOST");
$dbUsername = getenv("DB_USERNAME");
$dbPassword = getenv("DB_PASSWORD");
$dbName = getenv("DB_DATABASE");

$conn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName);

if(!$conn) {
die('Could not connect to db: ' . mysqli_error());
}


