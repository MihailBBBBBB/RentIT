<?php
require_once 'dbh.inc.php';

$stmt = $pdo->prepare("SELECT Place_id, Name, Description, Adress, Owner, Price, Foto, Coordinates,
                       (SELECT AVG(Stars) FROM reviews WHERE reviews.Place_id = place.Place_id) AS Stars
                       FROM place");
$stmt->execute();
$places = $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];


$query = "SELECT * FROM users";
$stmt = $pdo->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

