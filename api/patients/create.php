<?php
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../controllers/PatientController.php';
(new PatientController($pdo))->create();