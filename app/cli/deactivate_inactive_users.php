<?php
/**
 * Deactivates customers who haven't signed in for N days (default 40).
 * Each affected customer is emailed first; the account is only suspended if that email was sent.
 *
 *   php app/cli/deactivate_inactive_users.php                 dry run — lists who would be affected
 *   php app/cli/deactivate_inactive_users.php --apply         sends emails and suspends accounts
 *   php app/cli/deactivate_inactive_users.php --apply --days=40
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only.');
}

require_once __DIR__ . '/../../config/app.php';
require_once APP_PATH . '/helpers/Database.php';
require_once APP_PATH . '/helpers/Core.php';
require_once APP_PATH . '/helpers/Helpers.php';
require_once APP_PATH . '/helpers/Services.php';
require_once APP_PATH . '/models/UserSettings.php';
require_once APP_PATH . '/models/Models.php';

$options = getopt('', ['apply', 'days::']);
$apply   = isset($options['apply']);
$days    = max(1, (int)($options['days'] ?? 40));
$cutoff  = date('Y-m-d H:i:s', strtotime("-$days days"));

// Never-signed-in accounts are measured from creation date. Only active customers qualify,
// so admins and already-suspended accounts are skipped, and a failed run retries next time.
$candidates = Database::fetchAll(
    "SELECT id, name, email, last_login_at, created_at
     FROM users
     WHERE role = 'customer'
       AND status = 'active'
       AND COALESCE(last_login_at, created_at) <= ?",
    [$cutoff]
);

echo ($apply ? 'APPLY' : 'DRY RUN') . " — inactive for {$days}+ days: " . count($candidates) . " customer(s)\n";

$deactivated = 0;
$failed      = 0;

foreach ($candidates as $user) {
    $lastSeen = $user['last_login_at'] ?? $user['created_at'];
    $idleDays = (int)floor((time() - strtotime($lastSeen)) / 86400);
    $label    = "#{$user['id']} {$user['email']} (idle {$idleDays}d)";

    if (!$apply) {
        echo "  would deactivate: $label\n";
        continue;
    }

    if (!Mailer::sendInactivityReminder($user, $idleDays)) {
        $failed++;
        echo "  EMAIL FAILED, not deactivated: $label\n";
        error_log("Inactivity reminder failed for user #{$user['id']}");
        continue;
    }

    User::suspend((int)$user['id']);
    ActivityLog::log('account_deactivated_inactive', "Deactivated after {$idleDays} days of inactivity", (int)$user['id']);
    $deactivated++;
    echo "  deactivated: $label\n";
}

if ($apply) {
    echo "Done. Deactivated: $deactivated, email failures: $failed\n";
}
