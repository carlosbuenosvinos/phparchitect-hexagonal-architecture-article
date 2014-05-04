<?php
/**
 * Created by PhpStorm.
 * User: buenosvinos
 * Date: 04/05/14
 * Time: 09:38
 */
$db = new mysqli('localhost', 'root', '', 'blog');
$stmt = $db->prepare('SELECT * FROM posts WHERE id = ?');
$id = 1;
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

print_r($result->fetch_all(MYSQLI_ASSOC));
