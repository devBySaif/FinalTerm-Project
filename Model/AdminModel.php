<?php

class AdminModel
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getDashboardStats()
    {
        $roleStmt = $this->db->prepare(
            "SELECT role, COUNT(*) AS total FROM users GROUP BY role ORDER BY role"
        );
        $roleStmt->execute();

        $pendingStmt = $this->db->prepare(
            "SELECT COUNT(*) AS total FROM post_requests WHERE status = ?"
        );
        $pendingStmt->execute(['pending']);

        $postsStmt = $this->db->prepare("SELECT COUNT(*) AS total FROM posts");
        $postsStmt->execute();

        $commentsStmt = $this->db->prepare("SELECT COUNT(*) AS total FROM comments");
        $commentsStmt->execute();

        return [
            'users_by_role' => $roleStmt->fetchAll(),
            'pending_requests' => (int) $pendingStmt->fetchColumn(),
            'total_posts' => (int) $postsStmt->fetchColumn(),
            'total_comments' => (int) $commentsStmt->fetchColumn(),
        ];
    }

    public function getManageableUsers()
    {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, role, is_verified, profile_picture, created_at
             FROM users
             WHERE role IN (?, ?)
             ORDER BY created_at DESC, id DESC"
        );
        $stmt->execute(['scout', 'user']);

        return $stmt->fetchAll();
    }

    public function getUserById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, role, is_verified FROM users WHERE id = ? LIMIT 1"
        );
        $stmt->execute([(int) $id]);

        return $stmt->fetch();
    }

    public function emailExists($email)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function createUser($name, $email, $passwordHash, $role, $isVerified)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password_hash, role, is_verified, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())"
        );

        return $stmt->execute([
            $name,
            $email,
            $passwordHash,
            $role,
            (int) $isVerified,
        ]);
    }

    public function toggleUserVerification($userId)
    {
        $user = $this->getUserById($userId);
        if (!$user || $user['role'] === 'admin') {
            return null;
        }

        $newStatus = (int) !$user['is_verified'];
        $stmt = $this->db->prepare("UPDATE users SET is_verified = ? WHERE id = ?");
        $stmt->execute([$newStatus, (int) $userId]);

        return $newStatus;
    }

    public function deleteUser($userId, $currentAdminId)
    {
        if ((int) $userId === (int) $currentAdminId) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([(int) $userId]);

        return $stmt->rowCount() > 0;
    }

    public function getPendingPostRequests()
    {
        $stmt = $this->db->prepare(
            "SELECT pr.id, pr.scout_id, pr.post_data, pr.requested_at, pr.status,
                    u.name AS scout_name, u.email AS scout_email
             FROM post_requests pr
             INNER JOIN users u ON u.id = pr.scout_id
             WHERE pr.status = ?
             ORDER BY pr.requested_at ASC, pr.id ASC"
        );
        $stmt->execute(['pending']);

        return $stmt->fetchAll();
    }

    public function getAllPosts()
    {
        $stmt = $this->db->prepare(
            "SELECT p.id, p.scout_id, p.title, p.short_history, p.country, p.genre,
                    p.cost_level, p.travel_medium_info, p.status, p.created_at, p.updated_at,
                    u.name AS scout_name
             FROM posts p
             LEFT JOIN users u ON u.id = p.scout_id
             ORDER BY p.created_at DESC, p.id DESC"
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getPostRequestById($requestId)
    {
        $stmt = $this->db->prepare(
            "SELECT id, scout_id, post_data, requested_at, status
             FROM post_requests
             WHERE id = ?
             LIMIT 1"
        );
        $stmt->execute([(int) $requestId]);

        return $stmt->fetch();
    }

    public function approvePostRequest($requestId)
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare(
                "SELECT id, scout_id, post_data
                 FROM post_requests
                 WHERE id = ? AND status = ?
                 LIMIT 1
                 FOR UPDATE"
            );
            $stmt->execute([(int) $requestId, 'pending']);
            $request = $stmt->fetch();

            if (!$request) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Pending request was not found.'];
            }

            $data = json_decode($request['post_data'], true);
            if (!is_array($data)) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Request data is not valid JSON.'];
            }

            $title = trim($data['title'] ?? '');
            $shortHistory = trim($data['short_history'] ?? ($data['history'] ?? ''));
            $country = trim($data['country'] ?? ($data['country_representation'] ?? ''));
            $genre = trim($data['genre'] ?? '');
            $costLevel = trim($data['cost_level'] ?? '');
            $travelMediumInfo = trim($data['travel_medium_info'] ?? ($data['travel_medium'] ?? ''));

            if (
                $title === '' ||
                $shortHistory === '' ||
                $country === '' ||
                $genre === '' ||
                !in_array($costLevel, ['low', 'medium', 'high'], true) ||
                $travelMediumInfo === ''
            ) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Request is missing required post fields.'];
            }

            $insert = $this->db->prepare(
                "INSERT INTO posts
                    (scout_id, title, short_history, country, genre, cost_level, travel_medium_info, status, created_at, updated_at)
                 VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
            );
            $insert->execute([
                (int) $request['scout_id'],
                $title,
                $shortHistory,
                $country,
                $genre,
                $costLevel,
                $travelMediumInfo,
                'approved',
            ]);

            $postId = (int) $this->db->lastInsertId();

            $delete = $this->db->prepare("DELETE FROM post_requests WHERE id = ?");
            $delete->execute([(int) $requestId]);

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Post request approved.',
                'post_id' => $postId,
            ];
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }

    public function rejectPostRequest($requestId)
    {
        $stmt = $this->db->prepare(
            "UPDATE post_requests SET status = ? WHERE id = ? AND status = ?"
        );
        $stmt->execute(['rejected', (int) $requestId, 'pending']);

        return $stmt->rowCount() > 0;
    }

    public function getPostById($postId)
    {
        $stmt = $this->db->prepare(
            "SELECT id, scout_id, title, short_history, country, genre, cost_level,
                    travel_medium_info, status, created_at, updated_at
             FROM posts
             WHERE id = ?
             LIMIT 1"
        );
        $stmt->execute([(int) $postId]);

        return $stmt->fetch();
    }

    public function updatePost($postId, array $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE posts
             SET title = ?, short_history = ?, country = ?, genre = ?, cost_level = ?,
                 travel_medium_info = ?, status = ?, updated_at = NOW()
             WHERE id = ?"
        );

        $stmt->execute([
            $data['title'],
            $data['short_history'],
            $data['country'],
            $data['genre'],
            $data['cost_level'],
            $data['travel_medium_info'],
            $data['status'],
            (int) $postId,
        ]);

        return $stmt->rowCount() >= 0;
    }

    public function deletePost($postId)
    {
        $stmt = $this->db->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([(int) $postId]);

        return $stmt->rowCount() > 0;
    }

    public function getComments()
    {
        $stmt = $this->db->prepare(
            "SELECT c.id, c.content, c.created_at, c.post_id, c.user_id,
                    p.title AS post_title, u.name AS commenter_name, u.email AS commenter_email
             FROM comments c
             INNER JOIN posts p ON p.id = c.post_id
             INNER JOIN users u ON u.id = c.user_id
             ORDER BY c.created_at DESC, c.id DESC"
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function deleteComment($commentId)
    {
        $stmt = $this->db->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->execute([(int) $commentId]);

        return $stmt->rowCount() > 0;
    }
}
