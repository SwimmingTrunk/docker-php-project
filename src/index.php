<?php

$connection = new mysqli("db", "user", "password", "testdb");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$result = $connection->query("SELECT * FROM users LIMIT 1");

if (!$result) {
    die("Query failed: " . $connection->error);
}

$row = $result->fetch_assoc();

echo "ID: " . $row["id"] . "<br>";
echo "Name: " . htmlspecialchars($row["name"]);

$connection->close();
?>