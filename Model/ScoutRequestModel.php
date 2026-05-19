<?php

class ScoutRequestModel
{
    private mysqli $conn;

    public function __construct()
    {
        $this->conn = new mysqli('localhost', 'root', '', 'travel_guide');

        if ($this->conn->connect_error) {
            die('Database connection failed.');
        }

        $this->conn->set_charset('utf8mb4');
    }

    public function findUserByEmail(string $email): ?array
    {
        $stmt = $this->conn->prepare('SELECT id, name, email, password_hash, role, is_verified FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        return $user ?: null;
    }

    public function createRequest(int $scoutId, array $postData, ?int $originalPostId = null): bool
    {
        $json = json_encode($postData);
        $status = 'pending';
        $stmt = $this->conn->prepare('INSERT INTO post_requests (scout_id, original_post_id, post_data, status) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('iiss', $scoutId, $originalPostId, $json, $status);
        return $stmt->execute();
    }

    public function updateRequest(int $requestId, int $scoutId, array $postData): bool
    {
        $json = json_encode($postData);
        $stmt = $this->conn->prepare("UPDATE post_requests SET post_data = ? WHERE id = ? AND scout_id = ? AND status = 'pending'");
        $stmt->bind_param('sii', $json, $requestId, $scoutId);
        return $stmt->execute() && $stmt->affected_rows > 0;
    }

    public function deleteRequest(int $requestId, int $scoutId): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM post_requests WHERE id = ? AND scout_id = ? AND status = 'pending'");
        $stmt->bind_param('ii', $requestId, $scoutId);
        return $stmt->execute() && $stmt->affected_rows > 0;
    }

    public function getRequest(int $requestId, int $scoutId): ?array
    {
        $stmt = $this->conn->prepare('SELECT * FROM post_requests WHERE id = ? AND scout_id = ? LIMIT 1');
        $stmt->bind_param('ii', $requestId, $scoutId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if (!$row) {
            return null;
        }

        $row['post_data_array'] = json_decode($row['post_data'], true) ?: [];
        return $row;
    }

    public function getRequestsByScout(int $scoutId): array
    {
        $stmt = $this->conn->prepare('SELECT * FROM post_requests WHERE scout_id = ? ORDER BY requested_at DESC');
        $stmt->bind_param('i', $scoutId);
        $stmt->execute();
        $requests = [];

        foreach ($stmt->get_result() as $row) {
            $row['post_data_array'] = json_decode($row['post_data'], true) ?: [];
            $requests[] = $row;
        }

        return $requests;
    }

    public function getApprovedPostsByScout(int $scoutId): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM posts WHERE scout_id = ? AND status = 'approved' ORDER BY updated_at DESC");
        $stmt->bind_param('i', $scoutId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getApprovedPost(int $postId, int $scoutId): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM posts WHERE id = ? AND scout_id = ? AND status = 'approved' LIMIT 1");
        $stmt->bind_param('ii', $postId, $scoutId);
        $stmt->execute();
        $post = $stmt->get_result()->fetch_assoc();
        return $post ?: null;
    }
}
