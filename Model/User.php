<?php

class User {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createUser($name, $email, $password, $role) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare(
            "INSERT INTO users
            (name, email, password_hash, role, is_verified)
            VALUES (?, ?, ?, ?, 0)"
        );

        return $stmt->execute([
            $name,
            $email,
            $hash,
            $role
        ]);
    }

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users WHERE email = ?"
        );

        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        return $this->getUserById($id);
    }

    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);

        if (!$user) {
            return false;
        }

        return password_verify($password, $user['password_hash']) ? $user : false;
    }

    public function emailExistsForAnotherUser($email, $id) {
        $stmt = $this->pdo->prepare(
            "SELECT id FROM users WHERE email = ? AND id <> ?"
        );

        $stmt->execute([$email, $id]);
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($id, $name, $email, $profilePicture = null) {
        if ($profilePicture) {
            $stmt = $this->pdo->prepare(
                "UPDATE users SET name = ?, email = ?, profile_picture = ? WHERE id = ?"
            );

            return $stmt->execute([
                $name,
                $email,
                $profilePicture,
                $id
            ]);
        }

        $stmt = $this->pdo->prepare(
            "UPDATE users SET name = ?, email = ? WHERE id = ?"
        );

        return $stmt->execute([
            $name,
            $email,
            $id
        ]);
    }

    public function updatePassword($id, $newPassword) {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare(
            "UPDATE users SET password_hash = ? WHERE id = ?"
        );

        return $stmt->execute([
            $hash,
            $id
        ]);
    }

    public function storeRememberToken($id, $hashedValidator) {
        if (!$this->hasColumn('users', 'remember_token')) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            "UPDATE users SET remember_token = ? WHERE id = ?"
        );

        return $stmt->execute([$hashedValidator, $id]);
    }

    public function clearRememberToken($id) {
        if (!$this->hasColumn('users', 'remember_token')) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            "UPDATE users SET remember_token = NULL WHERE id = ?"
        );

        return $stmt->execute([$id]);
    }

    private function hasColumn($table, $column) {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?"
        );

        $stmt->execute([$table, $column]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
