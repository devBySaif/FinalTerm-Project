<?php

class Wishlist {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function add($userId, $postId) {
        $stmt = $this->pdo->prepare(
            "SELECT id FROM wishlist WHERE user_id = ? AND post_id = ?"
        );
        $stmt->execute([$userId, $postId]);

        if ($stmt->rowCount() > 0) {
            return false;
        }

        $insert = $this->pdo->prepare(
            "INSERT INTO wishlist (user_id, post_id) VALUES (?, ?)"
        );

        return $insert->execute([$userId, $postId]);
    }

    public function remove($userId, $postId) {
        $stmt = $this->pdo->prepare(
            "DELETE FROM wishlist WHERE user_id = ? AND post_id = ?"
        );

        $stmt->execute([$userId, $postId]);

        return $stmt->rowCount() > 0;
    }

    public function getForUser($userId) {
        $stmt = $this->pdo->prepare(
            "SELECT w.post_id, p.title, p.country, p.cost_level
             FROM wishlist w
             INNER JOIN posts p ON p.id = w.post_id
             WHERE w.user_id = ?
             ORDER BY w.added_at DESC"
        );

        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
