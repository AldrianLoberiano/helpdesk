<?php
/**
 * Technician - Update Ticket
 * IT Helpdesk Ticketing System
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('Technician');

$db = getDB();
$user_id = getCurrentUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('danger', 'Invalid request.');
        redirect('tickets.php');
    }

    $ticket_id = intval($_POST['ticket_id'] ?? 0);
    $new_status = $_POST['status'] ?? '';
    $valid_statuses = ['In Progress', 'Resolved'];
    
    if ($ticket_id && in_array($new_status, $valid_statuses)) {
        try {
            // Verify ticket is assigned to this technician
            $stmt = $db->prepare("SELECT id, created_by FROM tickets WHERE id = ? AND assigned_to = ?");
            $stmt->execute([$ticket_id, $user_id]);
            $ticket = $stmt->fetch();
            
            if ($ticket) {
                if ($new_status === 'Resolved') {
                    $stmt = $db->prepare("UPDATE tickets SET status = ?, resolved_at = NOW(), updated_at = NOW() WHERE id = ?");
                } else {
                    $stmt = $db->prepare("UPDATE tickets SET status = ?, updated_at = NOW() WHERE id = ?");
                }
                $stmt->execute([$new_status, $ticket_id]);
                
                // Add comment
                $stmt = $db->prepare("INSERT INTO ticket_comments (ticket_id, user_id, comment) VALUES (?, ?, ?)");
                $stmt->execute([$ticket_id, $user_id, "Status changed to $new_status"]);
                
                // Notify user
                $stmt = $db->prepare("INSERT INTO notifications (user_id, title, message, link) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $ticket['created_by'],
                    'Ticket Updated',
                    "Your ticket status has been changed to $new_status",
                    "../user/view_ticket.php?id=$ticket_id"
                ]);
                
                setFlashMessage('success', 'Ticket status updated successfully.');
            } else {
                setFlashMessage('danger', 'Ticket not found or access denied.');
            }
        } catch (PDOException $e) {
            setFlashMessage('danger', 'Error updating ticket status.');
        }
    }
    redirect("view_ticket.php?id=$ticket_id");
}

redirect('tickets.php');
