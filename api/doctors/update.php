<?php
// api/doctors/update.php

require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../ontrollers/DoctorController.php';

(new DoctorController($pdo))->update();