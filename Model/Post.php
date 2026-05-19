<?php

class Post {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function latestApproved($limit = 6) {
        $stmt = $this->pdo->prepare(
            "SELECT id, title, short_history, country, genre, cost_level, created_at
             FROM posts
             WHERE status = 'approved'
             ORDER BY created_at DESC
             LIMIT ?"
        );

        $stmt->bindValue(1, (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
