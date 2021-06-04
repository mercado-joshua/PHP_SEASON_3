<?php
$connect = mysqli_connect("localhost", "root", "", "my_database_3");
if(mysqli_connect_errno()) {
    echo "Failed to connect to mysql: " . mysqli_connect_error();
}