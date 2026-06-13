<?php
/**
 * User - Reopen Ticket
 * IT Helpdesk Ticketing System
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('Employee');

$db = getDB();
$user_id = getCurrentUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('danger', 'Invalid request.');
        redirect('tickets.php');
    }

    $ticket_id = intval($_POST['ticket_id'] ?? 0);
    
    if ($ticket_id) {
        try {
            // Verify ticket belongs to user and is closed
            $stmt = $db->prepare("SELECT id, assigned_to FROM tickets WHERE id = ? AND created_by = ? AND status = 'Closed'");
            $stmt->execute([$ticket_id, $user_id]);
            $ticket = $stmt->fetch();
            
            if ($ticket) {
                $stmt = $db->prepare("UPDATE tickets SET status = 'In Progress', closed_at = NULL, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$ticket_id]);
                
                // Add comment
                $stmt = $db->prepare("INSERT INTO ticket_comments (ticket_id, user_id, comment) VALUES (?, ?, ?)");
                $stmt->execute([$ticket_id, $user_id, "Ticket reopened by user"]);
                
                // Notify technician if assigned
                if ($ticket['assigned_to']) {
                    $stmt = $db->prepare("INSERT INTO notifications (user_id, title, message, link) VALUES (?, ?, ?, ?)");
                    $stmt->execute([
                        $ticket['assigned_to'],
                        'Ticket Reopened',
                        'A ticket has been reopened by the user',
                        "../technician/view_ticket.php?id=$ticket_id"
                    ]);
                }
                
                // Notify admins
                $stmt = $db->prepare("SELECT id FROM users WHERE role_id = 1");
                $stmt->execute();
                $admins = $stmt->fetchAll();
                foreach ($admins as $admin) {
                    $stmt = $db->prepare("INSERT INTO notifications (user_id, title, message, link) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$admin['id'], 'Ticket Reopened', "Ticket #$ticket_id has been reopened", "../admin/view_ticket.php?id=$ticket_id"]);
                }
                
                setFlashMessage('success', 'Ticket reopened successfully.');
            } else {
                setFlashMessage('danger', 'Ticket not found or cannot be reopened.');
            }
        } catch (PDOException $e) {
            setFlashMessage('danger', 'Error reopening ticket.');
        }
    }
    redirect("view_ticket.php?id=$ticket_id");
}

redirect('tickets.php');
