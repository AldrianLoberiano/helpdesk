<?php
/**
 * Dummy Data Seeder for IT Helpdesk System
 * 
 * Creates: 20+ users, 30+ tickets, comments, feedbacks, notifications, activity logs
 * 
 * Usage: Navigate to http://localhost/helpdesk/helpdesk_system/seed_dummy_data.php
 *   - Click "Seed Database" button or
 *   - Run from CLI: php seed_dummy_data.php
 */

require_once __DIR__ . '/config/database.php';

$db = getDB();
$isCLI = php_sapi_name() === 'cli';

function out($msg, $isCLI) {
    if ($isCLI) {
        echo $msg . "\n";
    } else {
        echo "<p>{$msg}</p>";
    }
}

function outPre($msg, $isCLI) {
    if ($isCLI) {
        echo $msg . "\n";
    } else {
        echo "<pre>" . htmlspecialchars($msg) . "</pre>";
    }
}

// --- Step 0: Create missing user_settings table ---
$db->exec("CREATE TABLE IF NOT EXISTS user_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    setting_key VARCHAR(50) NOT NULL,
    setting_value VARCHAR(255) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_setting (user_id, setting_key),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB");

out("✔ user_settings table ready", $isCLI);

// --- Step 1: Insert Users ---
$hash = password_hash('password123', PASSWORD_DEFAULT);

$technicians = [
    ['tech_carlos', 'carlos@helpdesk.com', 'Carlos Reyes', '09171234567', 2, 1],
    ['tech_maria', 'maria@helpdesk.com', 'Maria Santos', '09181234567', 2, 1],
    ['tech_james', 'james@helpdesk.com', 'James Cruz', '09191234567', 2, 1],
    ['tech_anita', 'anita@helpdesk.com', 'Anita Villanueva', '09201234567', 2, 1],
    ['tech_dennis', 'dennis@helpdesk.com', 'Dennis Garcia', '09211234567', 2, 1],
    ['tech_sarah', 'sarah@helpdesk.com', 'Sarah Lim', '09221234567', 2, 1],
];

$employees = [
    ['juan_d', 'juan@company.com', 'Juan Dela Cruz', '09171111111', 3, 1],
    ['anna_m', 'anna@company.com', 'Anna Mendoza', '09172222222', 3, 2],
    ['robert_c', 'robert@company.com', 'Robert Cruz', '09173333333', 3, 3],
    ['rachel_b', 'rachel@company.com', 'Rachel Bautista', '09174444444', 3, 4],
    ['kevin_t', 'kevin@company.com', 'Kevin Torres', '09175555555', 3, 5],
    ['maria_g', 'maria.g@company.com', 'Maria Garcia', '09176666666', 3, 1],
    ['jose_r', 'jose@company.com', 'Jose Reyes', '09177777777', 3, 2],
    ['sophia_l', 'sophia@company.com', 'Sophia Lim', '09178888888', 3, 3],
    ['daniel_p', 'daniel@company.com', 'Daniel Cruz', '09179999999', 3, 4],
    ['isabella_s', 'isabella@company.com', 'Isabella Santos', '09180000000', 3, 5],
    ['miguel_f', 'miguel@company.com', 'Miguel Fernandez', '09181111111', 3, 1],
    ['camille_v', 'camille@company.com', 'Camille Villanueva', '09182222222', 3, 2],
    ['andre_m', 'andre@company.com', 'Andre Mendoza', '09183333333', 3, 3],
    ['patricia_d', 'patricia@company.com', 'Patricia Dela Cruz', '09184444444', 3, 4],
];

$userIds = ['technicians' => [], 'employees' => []];

$stmtUser = $db->prepare("INSERT IGNORE INTO users (username, email, password, full_name, phone, role_id, department_id) VALUES (?, ?, ?, ?, ?, ?, ?)");

foreach ($technicians as $t) {
    $stmtUser->execute([$t[0], $t[1], $hash, $t[2], $t[3], $t[4], $t[5]]);
    $id = $db->lastInsertId();
    if ($id) $userIds['technicians'][] = $id;
}

foreach ($employees as $e) {
    $stmtUser->execute([$e[0], $e[1], $hash, $e[2], $e[3], $e[4], $e[5]]);
    $id = $db->lastInsertId();
    if ($id) $userIds['employees'][] = $id;
}

// Fetch existing users in case some already existed
$allTech = $db->query("SELECT id FROM users WHERE role_id = 2 ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
$allEmp = $db->query("SELECT id FROM users WHERE role_id = 3 ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
$adminId = $db->query("SELECT id FROM users WHERE role_id = 1 LIMIT 1")->fetchColumn();

out("✔ Users seeded: " . count($allTech) . " technicians, " . count($allEmp) . " employees", $isCLI);

// --- Step 2: Insert Tickets ---
$categories = $db->query("SELECT id FROM ticket_categories ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
$departments = $db->query("SELECT id FROM departments ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);

$subjects = [
    ['Wi-Fi not connecting in Conference Room B', 'The Wi-Fi in Conference Room B has been down since this morning. Multiple users cannot connect.', 1, 'High', 'Assigned'],
    ['Laptop screen flickering', 'My laptop screen keeps flickering every few minutes. It started after a Windows update.', 2, 'Medium', 'In Progress'],
    ['Printer on 3rd floor jammed', 'The HP LaserJet on the 3rd floor keeps jamming. I have cleared it twice already but it jams again within an hour.', 3, 'Medium', 'Assigned'],
    ['Need Adobe Photoshop installed', 'I need Adobe Photoshop CC installed for the upcoming marketing campaign. Please install from the licensed copy.', 4, 'Low', 'Pending'],
    ['Cannot access email on phone', 'I cannot sync my email to my mobile phone. Getting an authentication error.', 6, 'High', 'Created'],
    ['Internet extremely slow on floor 2', 'The internet on the 2nd floor has been extremely slow for the past 3 days. Speed tests show less than 1 Mbps.', 7, 'Critical', 'Assigned'],
    ['Password expired, cannot login', 'My password expired and I cannot login to the system. I need an urgent password reset.', 5, 'High', 'In Progress'],
    ['Monitor displaying wrong colors', 'My monitor is displaying washed-out colors. I have tried recalibrating but it did not help.', 2, 'Low', 'Created'],
    ['VPN not connecting from home', 'I cannot connect to the company VPN from home. I have tried both the desktop client and web version.', 1, 'Medium', 'Pending'],
    ['Software keeps crashing', 'Microsoft Excel keeps crashing whenever I open files larger than 10MB. This is affecting my productivity.', 4, 'High', 'Assigned'],
    ['New employee account setup', 'We have a new employee starting next Monday. Need account created with access to Finance department systems.', 5, 'Medium', 'Resolved'],
    ['Projector not detected in meeting room', 'The projector in Meeting Room 2 is not being detected by any laptop. HDMI cable seems fine.', 2, 'Medium', 'In Progress'],
    ['Request for additional monitor', 'I would like to request an additional monitor for my workstation. My role requires multitasking across multiple windows.', 8, 'Low', 'Created'],
    ['Email attachments not downloading', 'I cannot download attachments from my email. Clicking the download button does nothing.', 6, 'High', 'Assigned'],
    ['System running very slow', 'My computer has been running extremely slow for the past week. Boot time is over 5 minutes and apps take forever to open.', 2, 'Critical', 'In Progress'],
    ['VPN disconnects during video calls', 'The VPN keeps disconnecting whenever I join video calls on Zoom or Teams. This happens consistently.', 1, 'High', 'Created'],
    ['Need access to shared drive', 'I need read/write access to the Finance shared drive (\\\\server\\finance). Currently I only have read access.', 5, 'Low', 'Resolved'],
    ['Blue screen error on startup', 'My laptop shows a blue screen error every time I start it. Error code: IRQL_NOT_LESS_OR_EQUAL.', 2, 'Critical', 'Assigned'],
    ['Outlook calendar sync issues', 'My Outlook calendar is not syncing with my phone. Meetings created on desktop do not appear on mobile.', 6, 'Medium', 'Pending'],
    ['Request for standing desk', 'I would like to request a standing desk setup for ergonomic reasons. I have a medical recommendation.', 8, 'Low', 'Created'],
    ['Cannot print to network printer', 'I cannot print to the network printer on the 4th floor. It says the printer is offline but the physical printer is on.', 3, 'High', 'In Progress'],
    ['Software license expired', 'My Adobe Creative Suite license has expired. I need it renewed to continue my design work.', 4, 'High', 'Resolved'],
    ['Mouse and keyboard not responding', 'Both my wireless mouse and keyboard stopped working at the same time. Tried new batteries but no luck.', 2, 'Medium', 'Assigned'],
    ['Need access to Zoom Pro', 'I need a Zoom Pro license for hosting client meetings. The free version has a 40-minute limit.', 4, 'Low', 'Pending'],
    ['Server room temperature alert', 'The temperature monitoring system in the server room is showing 32°C. This is above the safe threshold.', 7, 'Critical', 'In Progress'],
    ['Desktop computer won\'t turn on', 'My desktop computer refuses to turn on. The power LED is not lighting up at all.', 2, 'High', 'Created'],
    ['Need new keyboard', 'My keyboard has several keys that do not respond. The spacebar and Enter key are particularly bad.', 2, 'Low', 'Resolved'],
    ['Company Wi-Fi blocking certain sites', 'The company Wi-Fi is blocking access to some research websites I need for my project. Please whitelist them.', 7, 'Medium', 'Pending'],
    ['Laptop battery drains quickly', 'My laptop battery only lasts about 30 minutes even when fully charged. It used to last 4+ hours.', 2, 'Medium', 'Assigned'],
    ['Cannot access ERP system', 'I am getting a "Connection Refused" error when trying to access the ERP system. This started this morning.', 5, 'Critical', 'Created'],
];

$statuses = ['Created', 'Pending', 'Assigned', 'In Progress', 'Resolved', 'Closed'];
$priorities = ['Low', 'Medium', 'High', 'Critical'];

$ticketIds = [];
$stmtTicket = $db->prepare("INSERT INTO tickets (ticket_number, subject, description, category_id, priority, status, created_by, assigned_to, department_id, created_at, resolved_at, closed_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($subjects as $i => $s) {
    $ticketNum = 'TKT-' . date('Ymd', strtotime("-" . rand(0, 30) . " days")) . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
    $createdAt = date('Y-m-d H:i:s', strtotime("-" . rand(0, 30) . " days -" . rand(0, 12) . " hours"));
    
    $createdBy = $allEmp[array_rand($allEmp)];
    $assignedTo = null;
    $resolvedAt = null;
    $closedAt = null;
    
    $status = $s[4];
    if (in_array($status, ['Assigned', 'In Progress', 'Resolved', 'Closed'])) {
        $assignedTo = $allTech[array_rand($allTech)];
    }
    if ($status === 'Resolved') {
        $resolvedAt = date('Y-m-d H:i:s', strtotime($createdAt . " +" . rand(1, 48) . " hours"));
    }
    if ($status === 'Closed') {
        $resolvedAt = date('Y-m-d H:i:s', strtotime($createdAt . " +" . rand(1, 24) . " hours"));
        $closedAt = date('Y-m-d H:i:s', strtotime($resolvedAt . " +" . rand(1, 72) . " hours"));
    }
    
    $dept = $departments[array_rand($departments)];
    
    $stmtTicket->execute([
        $ticketNum, $s[0], $s[1], $s[2], $s[3], $status,
        $createdBy, $assignedTo, $dept, $createdAt, $resolvedAt, $closedAt
    ]);
    $ticketIds[] = $db->lastInsertId();
}

out("✔ Tickets seeded: " . count($ticketIds), $isCLI);

// --- Step 3: Insert Ticket Comments ---
$commentTexts = [
    "I'm looking into this issue now. Will provide an update shortly.",
    "Can you provide more details about when this started happening?",
    "I've checked the hardware and it seems to be a configuration issue.",
    "This has been escalated to the senior technician.",
    "Please try restarting your computer and let me know if the issue persists.",
    "I've submitted a request for replacement parts. Expected delivery is 2-3 business days.",
    "The issue has been identified. It's related to the recent system update.",
    "I've applied a temporary fix. Please monitor and let me know if it happens again.",
    "This is a known issue. A permanent fix is being developed.",
    "Could you send a screenshot of the error message?",
    "I've checked with the vendor and they recommend upgrading to the latest version.",
    "The issue is resolved on our end. Please verify on your side.",
    "Thank you for the quick response!",
    "I've documented the steps to prevent this from happening again.",
    "This requires on-site inspection. I'll schedule a visit to your desk.",
    "The replacement has been ordered and should arrive by end of week.",
    "I've reset your credentials. Please try logging in again.",
    "The network team has been notified and is working on the issue.",
    "Please clear your browser cache and try again.",
    "I've updated the drivers. Let me know if the performance improves.",
];

$commentStmt = $db->prepare("INSERT INTO ticket_comments (ticket_id, user_id, comment, is_internal, created_at) VALUES (?, ?, ?, ?, ?)");

$commentCount = 0;
foreach ($ticketIds as $ticketId) {
    $numComments = rand(1, 4);
    for ($c = 0; $c < $numComments; $c++) {
        $isInternal = rand(0, 10) < 2 ? 1 : 0;
        $userId = $isInternal ? $adminId : $allTech[array_rand($allTech)];
        $createdAt = date('Y-m-d H:i:s', strtotime("-" . rand(0, 20) . " days -" . rand(0, 12) . " hours"));
        $commentStmt->execute([
            $ticketId, $userId, $commentTexts[array_rand($commentTexts)], $isInternal, $createdAt
        ]);
        $commentCount++;
    }
}

out("✔ Comments seeded: {$commentCount}", $isCLI);

// --- Step 4: Insert Feedbacks for Resolved/Closed Tickets ---
$feedbackComments = [
    "Very satisfied with the quick resolution. Thank you!",
    "Issue was resolved but it took longer than expected.",
    "Excellent service! The technician was very helpful and professional.",
    "Problem fixed, but I had to follow up multiple times.",
    "Great support. Everything is working perfectly now.",
    "The issue was resolved efficiently. Appreciate the help.",
    "Good service but would prefer faster response time.",
    "Thank you for resolving this issue promptly.",
    "Satisfied with the resolution but the process could be smoother.",
    "Outstanding support! Went above and beyond to help.",
];

$feedbackStmt = $db->prepare("INSERT INTO feedbacks (ticket_id, user_id, rating, comment, created_at) VALUES (?, ?, ?, ?, ?)");
$feedbackCount = 0;

foreach ($ticketIds as $ticketId) {
    $ticket = $db->prepare("SELECT status, created_by, created_at FROM tickets WHERE id = ?");
    $ticket->execute([$ticketId]);
    $t = $ticket->fetch();
    
    if (in_array($t['status'], ['Resolved', 'Closed']) && rand(0, 100) < 80) {
        $rating = rand(3, 5);
        $feedbackStmt->execute([
            $ticketId, $t['created_by'], $rating,
            $feedbackComments[array_rand($feedbackComments)],
            date('Y-m-d H:i:s', strtotime($t['created_at'] . " +" . rand(24, 120) . " hours"))
        ]);
        $feedbackCount++;
    }
}

out("✔ Feedbacks seeded: {$feedbackCount}", $isCLI);

// --- Step 5: Insert Notifications ---
$notifTemplates = [
    ['title' => 'Ticket Assigned', 'msg' => 'Your ticket %s has been assigned to a technician.', 'link' => 'user/view_ticket.php?id=%d'],
    ['title' => 'Ticket Updated', 'msg' => 'Your ticket %s has been updated.', 'link' => 'user/view_ticket.php?id=%d'],
    ['title' => 'Ticket Resolved', 'msg' => 'Your ticket %s has been resolved.', 'link' => 'user/view_ticket.php?id=%d'],
    ['title' => 'New Comment', 'msg' => 'A new comment was added to ticket %s.', 'link' => 'user/view_ticket.php?id=%d'],
    ['title' => 'Ticket Closed', 'msg' => 'Your ticket %s has been closed.', 'link' => 'user/view_ticket.php?id=%d'],
    ['title' => 'Ticket Pending', 'msg' => 'Your ticket %s is now pending.', 'link' => 'user/view_ticket.php?id=%d'],
];

$notifStmt = $db->prepare("INSERT INTO notifications (user_id, title, message, is_read, link, created_at) VALUES (?, ?, ?, ?, ?, ?)");
$notifCount = 0;

foreach ($ticketIds as $ticketId) {
    $ticket = $db->prepare("SELECT ticket_number, status, created_by, assigned_to, created_at FROM tickets WHERE id = ?");
    $ticket->execute([$ticketId]);
    $t = $ticket->fetch();
    
    $template = $notifTemplates[array_rand($notifTemplates)];
    $msg = sprintf($template['msg'], $t['ticket_number']);
    $link = sprintf($template['link'], $ticketId);
    $isRead = rand(0, 100) < 70 ? 1 : 0;
    $createdAt = date('Y-m-d H:i:s', strtotime($t['created_at'] . " +" . rand(1, 24) . " hours"));
    
    // Notify creator
    $notifStmt->execute([$t['created_by'], $template['title'], $msg, $isRead, $link, $createdAt]);
    $notifCount++;
    
    // Notify assignee if different
    if ($t['assigned_to'] && $t['assigned_to'] != $t['created_by']) {
        $notifStmt->execute([$t['assigned_to'], $template['title'], $msg, $isRead, $link, $createdAt]);
        $notifCount++;
    }
}

out("✔ Notifications seeded: {$notifCount}", $isCLI);

// --- Step 6: Insert Activity Logs ---
$actions = [
    ['action' => 'login', 'desc' => 'User logged in successfully'],
    ['action' => 'create_ticket', 'desc' => 'Created new ticket %s'],
    ['action' => 'update_ticket', 'desc' => 'Updated ticket %s status to %s'],
    ['action' => 'assign_ticket', 'desc' => 'Assigned ticket %s to %s'],
    ['action' => 'resolve_ticket', 'desc' => 'Resolved ticket %s'],
    ['action' => 'close_ticket', 'desc' => 'Closed ticket %s'],
    ['action' => 'add_comment', 'desc' => 'Added comment to ticket %s'],
    ['action' => 'submit_feedback', 'desc' => 'Submitted feedback for ticket %s'],
    ['action' => 'update_profile', 'desc' => 'Updated profile information'],
    ['action' => 'change_password', 'desc' => 'Changed account password'],
];

$ips = ['192.168.1.' . rand(10, 250), '192.168.2.' . rand(10, 250), '10.0.0.' . rand(10, 250)];
$logStmt = $db->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address, created_at) VALUES (?, ?, ?, ?, ?)");
$logCount = 0;

// Logs for ticket creation
foreach ($ticketIds as $ticketId) {
    $ticket = $db->prepare("SELECT ticket_number, created_by, created_at, status, assigned_to FROM tickets WHERE id = ?");
    $ticket->execute([$ticketId]);
    $t = $ticket->fetch();
    
    $logStmt->execute([
        $t['created_by'], 'create_ticket',
        sprintf('Created new ticket %s', $t['ticket_number']),
        $ips[array_rand($ips)],
        $t['created_at']
    ]);
    $logCount++;
    
    if ($t['assigned_to']) {
        $logStmt->execute([
            $t['assigned_to'], 'assign_ticket',
            sprintf('Assigned ticket %s to technician', $t['ticket_number']),
            $ips[array_rand($ips)],
            date('Y-m-d H:i:s', strtotime($t['created_at'] . " +1 hour"))
        ]);
        $logCount++;
    }
    
    if (in_array($t['status'], ['Resolved', 'Closed'])) {
        $logStmt->execute([
            $t['assigned_to'] ?? $t['created_by'], 'resolve_ticket',
            sprintf('Resolved ticket %s', $t['ticket_number']),
            $ips[array_rand($ips)],
            date('Y-m-d H:i:s', strtotime($t['created_at'] . " +2 hours"))
        ]);
        $logCount++;
    }
}

// Extra login logs
foreach (array_merge($allTech, $allEmp) as $userId) {
    $numLogins = rand(2, 8);
    for ($l = 0; $l < $numLogins; $l++) {
        $logStmt->execute([
            $userId, 'login', 'User logged in successfully',
            $ips[array_rand($ips)],
            date('Y-m-d H:i:s', strtotime("-" . rand(0, 30) . " days -" . rand(0, 12) . " hours"))
        ]);
        $logCount++;
    }
}

out("✔ Activity logs seeded: {$logCount}", $isCLI);

// --- Summary ---
out("", $isCLI);
out("========================================", $isCLI);
out("  SEEDING COMPLETE!", $isCLI);
out("========================================", $isCLI);

$countUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$countTickets = $db->query("SELECT COUNT(*) FROM tickets")->fetchColumn();
$countComments = $db->query("SELECT COUNT(*) FROM ticket_comments")->fetchColumn();
$countFeedbacks = $db->query("SELECT COUNT(*) FROM feedbacks")->fetchColumn();
$outNotifs = $db->query("SELECT COUNT(*) FROM notifications")->fetchColumn();
$countLogs = $db->query("SELECT COUNT(*) FROM activity_logs")->fetchColumn();

out("", $isCLI);
out("Total Users:       {$countUsers}", $isCLI);
out("Total Tickets:     {$countTickets}", $isCLI);
out("Total Comments:    {$countComments}", $isCLI);
out("Total Feedbacks:   {$countFeedbacks}", $isCLI);
out("Total Notifications: {$outNotifs}", $isCLI);
out("Total Activity Logs: {$countLogs}", $isCLI);
out("", $isCLI);
out("Login with any seeded account:", $isCLI);
out("  Username: tech_carlos (or any tech_* / employee username)", $isCLI);
out("  Password: password123", $isCLI);
out("", $isCLI);

if (!$isCLI) {
    echo "<br><a href='login.php'>Go to Login Page</a>";
}
?>
