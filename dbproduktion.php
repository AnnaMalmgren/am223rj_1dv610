<?php

$dbhost = getenv("DB_HOST");
$dbUsername = getenv("DB_USERNAME");
$dbPassword = getenv("DB_PASSWORD");
$dbName = getenv("DB_DATABASE");

$conn = mysqli_connect($dbhost, $dbUsername, $dbPassword, $dbName);

if(! $conn ) {
    die('Could not connect to db: ' . mysqli_error());
 }