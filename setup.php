<?php

require_once("class.php");

$db = new Db();
$db->dropDB();
$db->setupDB();

?>
<a href="index.php">index.php</a>


