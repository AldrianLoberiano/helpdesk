<?php
/**
 * Get Notification Count - AJAX Endpoint
 * IT Helpdesk Ticketing System
 */

require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/json');

$db = getDB();
$user_id = getCurrentUserId();

try {
    $stmt->execute([$user_id]);
    $count = $stmt->fetchColumn();
    echo json_encode(['count' => intval($count)]);
} catch (PDOException $e) {
    echo json_encode(['count' => 0]);
}
