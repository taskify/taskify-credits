<?php

try {
    //connect as appropriate as above
    $db = new PDO('mysql:host=localhost;dbname=melvincarvalho;charset=utf8mb4', 'me', '');
    //print_r($db);
} catch(PDOException $ex) {
    echo "An Error occured!"; //user friendly message
    some_logging_function($ex->getMessage());
}
