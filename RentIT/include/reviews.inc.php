<?php
require_once 'dbh.inc.php';
$stmt = $pdo->prepare("SELECT u.Name, r.Place_id, r.Stars, r.Comment, r.Date, r.User_id
                       FROM users AS u
                       LEFT JOIN reviews AS r ON u.User_id = r.User_id");
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];