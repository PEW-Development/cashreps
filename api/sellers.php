<?php

include 'conn.php';

    try {
        // Set the PDO error mode to exception
        $dbcon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the SQL statement
        $stmt = $dbcon->prepare("SELECT DISTINCT seller FROM cashreps WHERE available = 1");

        // Execute the prepared statement
        $stmt->execute();

        // Fetch all rows as an associative array
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // JSON encode the result and print it
        echo json_encode($result);
    } catch(PDOException $e) {
        // Handle any exceptions and print the error message
        echo "Error: " . $e->getMessage();
    }