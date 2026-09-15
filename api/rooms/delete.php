<?php
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../controllers/RoomController.php';
(new RoomController($pdo))->delete();