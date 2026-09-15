<?php
// api/doctors/toggle-active.php

require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../controllers/DoctorController.php';

(new DoctorController($pdo))->toggleActive();