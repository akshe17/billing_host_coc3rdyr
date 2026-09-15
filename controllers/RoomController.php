<?php
// controllers/RoomController.php

class RoomController
{
    private PDO $pdo;

    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    // ---------- READ: all rooms with their type + status ----------
    public function getAll(): array
    {
        return $this->pdo->query(
            'SELECT r.room_id, r.room_type_id, r.status_id, r.room_number,
                    r.floor_level, r.building,
                    rt.room_type_name,
                    rs.status_name, rs.color_code
             FROM `room` r
             INNER JOIN `room_type` rt ON rt.room_type_id = r.room_type_id
             INNER JOIN `room_status` rs ON rs.status_id = r.status_id
             ORDER BY r.room_id'
        )->fetchAll();
    }

    // ---------- READ: active room types for dropdown ----------
    public function getRoomTypes(): array
    {
        return $this->pdo->query(
            'SELECT room_type_id, room_type_name
             FROM `room_type`
             WHERE is_active = 1
             ORDER BY room_type_name'
        )->fetchAll();
    }

    // ---------- READ: statuses for dropdown ----------
    public function getStatuses(): array
    {
        return $this->pdo->query(
            'SELECT status_id, status_name, color_code
             FROM `room_status`
             ORDER BY status_name'
        )->fetchAll();
    }

    // ---------- CREATE ----------
    public function create(): void
    {
        $this->guard();

        $data   = $this->input();
        $errors = $this->validate($data);
        if ($errors) $this->json(422, ['success' => false, 'message' => 'Please fix the highlighted fields.', 'errors' => $errors]);

        if ($this->roomNumberExists($data['room_number'], $data['building'] ?? null, null)) {
            $this->json(409, ['success' => false, 'errors' => ['room_number' => 'This room number already exists in this building.']]);
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO `room`
                (room_type_id, status_id, room_number, floor_level, building)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            (int)$data['room_type_id'],
            (int)$data['status_id'],
            $data['room_number'],
            (isset($data['floor_level']) && $data['floor_level'] !== '') ? (int)$data['floor_level'] : null,
            !empty($data['building']) ? $data['building'] : null,
        ]);

        $this->json(201, [
            'success' => true,
            'message' => 'Room created successfully.',
            'id'      => (int)$this->pdo->lastInsertId(),
        ]);
    }

    // ---------- UPDATE ----------
    public function update(): void
    {
        $this->guard();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->json(400, ['success' => false, 'message' => 'Missing room id.']);

        $data   = $this->input();
        $errors = $this->validate($data);
        if ($errors) $this->json(422, ['success' => false, 'message' => 'Please fix the highlighted fields.', 'errors' => $errors]);

        if ($this->roomNumberExists($data['room_number'], $data['building'] ?? null, $id)) {
            $this->json(409, ['success' => false, 'errors' => ['room_number' => 'This room number already exists in this building.']]);
        }

        $stmt = $this->pdo->prepare(
            'UPDATE `room`
             SET room_type_id = ?, status_id = ?, room_number = ?, floor_level = ?, building = ?
             WHERE room_id = ?'
        );
        $stmt->execute([
            (int)$data['room_type_id'],
            (int)$data['status_id'],
            $data['room_number'],
            (isset($data['floor_level']) && $data['floor_level'] !== '') ? (int)$data['floor_level'] : null,
            !empty($data['building']) ? $data['building'] : null,
            $id,
        ]);

        $this->json(200, ['success' => true, 'message' => 'Room updated successfully.']);
    }

    // ---------- DELETE (hard, with FK check) ----------
    public function delete(): void
    {
        $this->guard();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->json(400, ['success' => false, 'message' => 'Missing room id.']);

        // Confirm it exists
        $stmt = $this->pdo->prepare('SELECT room_id FROM `room` WHERE room_id = ? LIMIT 1');
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            $this->json(404, ['success' => false, 'message' => 'Room not found.']);
        }

        // Check for FK references from room_assignment
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM `room_assignment` WHERE room_id = ?');
        $stmt->execute([$id]);
        $refCount = (int)$stmt->fetchColumn();

        if ($refCount > 0) {
            $this->json(409, [
                'success'    => false,
                'message'    => 'This item is currently being used and cannot be deleted.',
                'references' => $refCount,
            ]);
        }

        // Safe to delete
        $stmt = $this->pdo->prepare('DELETE FROM `room` WHERE room_id = ?');
        $stmt->execute([$id]);

        $this->json(200, ['success' => true, 'message' => 'Room deleted.']);
    }

    // ---------- HELPERS ----------
    private function guard(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(405, ['success' => false, 'message' => 'Method not allowed.']);
        }

        if (session_status() === PHP_SESSION_NONE) session_start();

        if (empty($_SESSION['user']) || (int)$_SESSION['user']['role_id'] !== 1) {
            $this->json(403, ['success' => false, 'message' => 'Access denied.']);
        }
    }

    private function input(): array
    {
        $raw = file_get_contents('php://input');
        return json_decode($raw, true) ?: $_POST;
    }

    private function validate(array $data): array
    {
        $errors = [];

        // Room type
        $typeId = (int)($data['room_type_id'] ?? 0);
        if ($typeId <= 0) {
            $errors['room_type_id'] = 'Please select a room type.';
        } else {
            $stmt = $this->pdo->prepare('SELECT room_type_id FROM `room_type` WHERE room_type_id = ? AND is_active = 1 LIMIT 1');
            $stmt->execute([$typeId]);
            if (!$stmt->fetch()) {
                $errors['room_type_id'] = 'Selected room type is not valid.';
            }
        }

        // Status
        $statusId = (int)($data['status_id'] ?? 0);
        if ($statusId <= 0) {
            $errors['status_id'] = 'Please select a status.';
        } else {
            $stmt = $this->pdo->prepare('SELECT status_id FROM `room_status` WHERE status_id = ? LIMIT 1');
            $stmt->execute([$statusId]);
            if (!$stmt->fetch()) {
                $errors['status_id'] = 'Selected status is not valid.';
            }
        }

        // Room number
        $num = trim((string)($data['room_number'] ?? ''));
        if ($num === '') {
            $errors['room_number'] = 'Room number is required.';
        } elseif (strlen($num) > 50) {
            $errors['room_number'] = 'Room number must be 50 characters or fewer.';
        }

        return $errors;
    }

    private function roomNumberExists(string $number, ?string $building, ?int $ignoreId): bool
    {
        $sql  = 'SELECT room_id FROM `room` WHERE room_number = ?';
        $args = [$number];

        if (!empty($building)) {
            $sql .= ' AND building = ?';
            $args[] = $building;
        } else {
            $sql .= ' AND (building IS NULL OR building = "")';
        }

        if ($ignoreId !== null) {
            $sql .= ' AND room_id <> ?';
            $args[] = $ignoreId;
        }

        $sql .= ' LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($args);
        return (bool)$stmt->fetch();
    }

    private function json(int $status, array $payload): void
    {
        http_response_code($status);
        echo json_encode($payload);
        exit;
    }
}