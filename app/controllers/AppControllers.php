<?php
/**
 * Dashboard Controller
 */
class DashboardController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();
        $sub  = Subscription::forUser($user['id']);

        $channels     = Channel::forUser($user['id']);
        $storageUsed  = Media::totalSizeForUser($user['id']);
        $mediaCount   = Media::count(['user_id' => $user['id']]);
        $maxScreens   = $sub['max_screens'] ?? 1;
        $maxStorage   = ($sub['max_storage_mb'] ?? 1024) * 1024 * 1024 / 1000; // convert MB to rough bytes

        $this->view('dashboard/index', [
            'title'       => 'Dashboard',
            'user'        => $user,
            'sub'         => $sub,
            'channels'    => $channels,
            'storageUsed' => $storageUsed,
            'mediaCount'  => $mediaCount,
            'maxScreens'  => $maxScreens,
            'maxStorage'  => ($sub['max_storage_mb'] ?? 1024) * 1024 * 1024,
        ]);
    }

    public function profile(): void
    {
        $this->requireAuth();
        $user = User::find($this->currentUser()['id']);
        $this->view('dashboard/profile', ['title' => 'Profile', 'user' => $user]);
    }

    public function updateProfile(): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $userId = $this->currentUser()['id'];
        $name   = Helpers::sanitize($_POST['name'] ?? '');

        if (strlen($name) < 2) {
            Session::flash('error', 'Name must be at least 2 characters.');
            $this->redirect('/profile');
        }

        $data = ['name' => $name];
        if (!empty($_POST['new_password'])) {
            if ($_POST['new_password'] !== $_POST['confirm_password']) {
                Session::flash('error', 'Passwords do not match.');
                $this->redirect('/profile');
            }
            $user = User::find($userId);
            if (!Helpers::verifyPassword($_POST['current_password'] ?? '', $user['password'])) {
                Session::flash('error', 'Current password is incorrect.');
                $this->redirect('/profile');
            }
            $data['password'] = Helpers::hashPassword($_POST['new_password']);
        }

        User::update($userId, $data);

        // Refresh session name
        $sessionUser = Session::get('user');
        $sessionUser['name'] = $name;
        Session::set('user', $sessionUser);

        Session::flash('success', 'Profile updated successfully.');
        $this->redirect('/profile');
    }
}

/**
 * Channel Controller
 */
class ChannelController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $user     = $this->currentUser();
        $sub      = Subscription::forUser($user['id']);
        $channels = Channel::forUser($user['id']);
        $maxScreens = $sub['max_screens'] ?? 1;

        $this->view('channels/index', [
            'title'      => 'TV Channels',
            'channels'   => $channels,
            'maxScreens' => $maxScreens,
            'sub'        => $sub,
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();
        $sub  = Subscription::forUser($user['id']);

        if (!Subscription::isActive($sub)) {
            Session::flash('error', 'Your subscription has expired. Please renew to create channels.');
            $this->redirect('/billing');
        }

        $currentCount = Channel::countForUser($user['id']);
        if ($currentCount >= ($sub['max_screens'] ?? 1)) {
            Session::flash('error', 'You\'ve reached your plan limit. Upgrade to add more screens.');
            $this->redirect('/billing');
        }

        $this->view('channels/create', ['title' => 'Create Channel', 'sub' => $sub]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $user = $this->currentUser();
        $sub  = Subscription::forUser($user['id']);

        if (!Subscription::isActive($sub) || Channel::countForUser($user['id']) >= ($sub['max_screens'] ?? 1)) {
            $this->json(['error' => 'Plan limit reached'], 403);
        }

        $name        = Helpers::sanitize($_POST['name'] ?? '');
        $orientation = in_array($_POST['orientation'] ?? '', ['landscape','portrait']) ? $_POST['orientation'] : 'landscape';

        if (strlen($name) < 2) {
            Session::flash('error', 'Channel name must be at least 2 characters.');
            $this->redirect('/channels/create');
        }

        $channelId = Channel::create($user['id'], [
            'name'                => $name,
            'description'         => Helpers::sanitize($_POST['description'] ?? ''),
            'orientation'         => $orientation,
            'slide_duration'      => max(3, min(60, (int)($_POST['slide_duration'] ?? 8))),
            'transition_effect'   => in_array($_POST['transition'] ?? '', ['fade','slide-left','slide-right','zoom','crossfade'])
                                         ? $_POST['transition'] : 'fade',
            'auto_refresh_minutes'=> max(5, min(60, (int)($_POST['auto_refresh'] ?? 15))),
            'background_color'    => preg_match('/^#[0-9a-fA-F]{6}$/', $_POST['bg_color'] ?? '') ? $_POST['bg_color'] : '#000000',
        ]);

        ActivityLog::log('channel_created', "Channel #$channelId created");
        Session::flash('success', 'Channel created! Add your first slide to get started.');
        $this->redirect('/channels/' . $channelId);
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $user    = $this->currentUser();
        $channel = Channel::find((int)$id);

        if (!$channel || !Channel::userOwns((int)$id, $user['id'])) {
            $this->abort(404, 'Channel not found.');
        }

        $slides    = Slide::forChannelAll((int)$id);
        $media     = Media::forUser($user['id']);
        $sub       = Subscription::forUser($user['id']);
        $maxSlides = (int)($sub['max_slides'] ?? 15);

        $this->view('channels/show', [
            'title'      => Helpers::e($channel['name']),
            'channel'    => $channel,
            'slides'     => $slides,
            'media'      => $media,
            'displayUrl' => Helpers::displayUrl($channel['slug']),
            'maxSlides'  => $maxSlides,
        ]);
    }

    public function edit(string $id): void
    {
        $this->requireAuth();
        $user    = $this->currentUser();
        $channel = Channel::find((int)$id);
        if (!$channel || !Channel::userOwns((int)$id, $user['id'])) $this->abort(404);
        $this->view('channels/edit', ['title' => 'Edit Channel', 'channel' => $channel]);
    }

    public function update(string $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $user    = $this->currentUser();
        $channel = Channel::find((int)$id);
        if (!$channel || !Channel::userOwns((int)$id, $user['id'])) $this->abort(404);

        Channel::update((int)$id, [
            'name'                 => Helpers::sanitize($_POST['name'] ?? ''),
            'description'          => Helpers::sanitize($_POST['description'] ?? ''),
            'orientation'          => in_array($_POST['orientation'] ?? '', ['landscape','portrait']) ? $_POST['orientation'] : 'landscape',
            'slide_duration'       => max(3, min(60, (int)($_POST['slide_duration'] ?? 8))),
            'transition_effect'    => in_array($_POST['transition'] ?? '', ['fade','slide-left','slide-right','zoom','crossfade'])
                                          ? $_POST['transition'] : 'fade',
            'auto_refresh_minutes' => max(5, min(60, (int)($_POST['auto_refresh'] ?? 15))),
            'background_color'     => preg_match('/^#[0-9a-fA-F]{6}$/', $_POST['bg_color'] ?? '') ? $_POST['bg_color'] : '#000000',
            'is_active'            => isset($_POST['is_active']) ? 1 : 0,
        ]);

        Session::flash('success', 'Channel settings saved.');
        $this->redirect('/channels/' . $id);
    }

    public function delete(string $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $user    = $this->currentUser();
        $channel = Channel::find((int)$id);
        if (!$channel || !Channel::userOwns((int)$id, $user['id'])) $this->abort(404);

        Slide::deleteForChannel((int)$id);
        Channel::delete((int)$id);

        ActivityLog::log('channel_deleted', "Channel #$id deleted");
        Session::flash('success', 'Channel deleted.');
        $this->redirect('/channels');
    }

    public function addSlide(string $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $user    = $this->currentUser();
        $channel = Channel::find((int)$id);
        if (!$channel || !Channel::userOwns((int)$id, $user['id'])) $this->json(['error' => 'Not found'], 404);

        $sub      = Subscription::forUser($user['id']);
        $maxSlides = (int)($sub['max_slides'] ?? 15);
        $current  = (int)(Database::fetchOne(
            'SELECT COUNT(*) as c FROM slides WHERE channel_id = ?', [(int)$id]
        )['c'] ?? 0);

        if ($current >= $maxSlides) {
            $this->json(['error' => "This channel has reached its limit of $maxSlides slides. Remove a slide or upgrade your plan."], 400);
        }

        $mediaId = (int)($_POST['media_id'] ?? 0);
        if (!$mediaId || !Media::userOwns($mediaId, $user['id'])) {
            $this->json(['error' => 'Invalid media'], 400);
        }

        $slideId = Slide::addToChannel((int)$id, $mediaId);
        $this->json(['success' => true, 'slide_id' => $slideId, 'slide_count' => $current + 1, 'max_slides' => $maxSlides]);
    }

    public function removeSlide(string $channelId, string $slideId): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $user    = $this->currentUser();
        $channel = Channel::find((int)$channelId);
        if (!$channel || !Channel::userOwns((int)$channelId, $user['id'])) $this->json(['error' => 'Not found'], 404);

        Database::execute('DELETE FROM slides WHERE id = ? AND channel_id = ?', [(int)$slideId, (int)$channelId]);
        $this->json(['success' => true]);
    }

    public function reorderSlides(string $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $user    = $this->currentUser();
        $channel = Channel::find((int)$id);
        if (!$channel || !Channel::userOwns((int)$id, $user['id'])) $this->json(['error' => 'Not found'], 404);

        $order = json_decode($_POST['order'] ?? '[]', true);
        if (!is_array($order)) $this->json(['error' => 'Invalid order'], 400);

        Slide::reorder((int)$id, array_map('intval', $order));
        $this->json(['success' => true]);
    }

    public function rotateSlide(string $channelId, string $slideId): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $user    = $this->currentUser();
        $channel = Channel::find((int)$channelId);
        if (!$channel || !Channel::userOwns((int)$channelId, $user['id'])) $this->json(['error' => 'Not found'], 404);

        $hasRotation = (bool) Database::fetchOne("SHOW COLUMNS FROM slides LIKE 'rotation'");
        if (!$hasRotation) {
            // Auto-add the column on first rotate request
            try {
                Database::execute('ALTER TABLE slides ADD COLUMN rotation SMALLINT UNSIGNED NOT NULL DEFAULT 0');
            } catch (Exception $e) {
                $this->json(['error' => 'Could not add rotation column: ' . $e->getMessage()], 500);
            }
        }

        $slide = Database::fetchOne(
            'SELECT id, rotation FROM slides WHERE id = ? AND channel_id = ?',
            [(int)$slideId, (int)$channelId]
        );
        if (!$slide) $this->json(['error' => 'Slide not found'], 404);

        $next = ((int)($slide['rotation'] ?? 0) + 90) % 360;
        Database::execute('UPDATE slides SET rotation = ? WHERE id = ?', [$next, (int)$slideId]);
        $this->json(['success' => true, 'rotation' => $next]);
    }

    public function preview(string $slug): void
    {
        $channel = Channel::findBySlug($slug);
        if (!$channel) $this->abort(404, 'Channel not found.');
        $this->redirect('/display/' . $slug . '?preview=1');
    }
}

/**
 * Media Controller
 */
class MediaController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $media = Media::forUser($user['id']);
        $sub   = Subscription::forUser($user['id']);
        $storageUsed = Media::totalSizeForUser($user['id']);

        $this->view('media/index', [
            'title'        => 'Media Library',
            'media'        => $media,
            'storageUsed'  => $storageUsed,
            'maxStorage'   => ($sub['max_storage_mb'] ?? 1024) * 1024 * 1024,
        ]);
    }

    public function upload(): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $user = $this->currentUser();
        $sub  = Subscription::forUser($user['id']);

        if (!Subscription::isActive($sub)) {
            $this->json(['error' => 'Subscription required to upload media.'], 403);
        }

        if (empty($_FILES['files'])) {
            $this->json(['error' => 'No files received.'], 400);
        }

        $maxStorageBytes = (int)($sub['max_storage_mb'] ?? 512) * 1024 * 1024;
        $storageUsed     = Media::totalSizeForUser($user['id']);

        if ($storageUsed >= $maxStorageBytes) {
            $this->json(['error' => 'Storage limit of ' . Helpers::formatBytes($maxStorageBytes) . ' reached. Delete unused files or upgrade your plan.'], 403);
        }

        $storage = new StorageService();
        $uploaded = [];
        $errors   = [];

        // Handle multiple file uploads — files[] always produces arrays in $_FILES
        $files = $_FILES['files'];
        $count = is_array($files['name']) ? count($files['name']) : 1;

        for ($i = 0; $i < $count; $i++) {
            $file = [
                'name'     => is_array($files['name'])     ? $files['name'][$i]     : $files['name'],
                'type'     => is_array($files['type'])     ? $files['type'][$i]     : $files['type'],
                'tmp_name' => is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'],
                'error'    => is_array($files['error'])    ? $files['error'][$i]    : $files['error'],
                'size'     => is_array($files['size'])     ? $files['size'][$i]     : $files['size'],
            ];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = $file['name'] . ': Upload error.';
                continue;
            }

            if ($storageUsed + $file['size'] > $maxStorageBytes) {
                $errors[] = $file['name'] . ': Storage limit reached (' . Helpers::formatBytes($maxStorageBytes) . ' total).';
                continue;
            }

            try {
                $meta    = $storage->store($file, $user['id']);
                $mediaId = Media::create($user['id'], $meta);
                $media   = Media::find($mediaId);
                $storageUsed += $meta['file_size'];
                $uploaded[] = [
                    'id'       => $mediaId,
                    'name'     => $media['original_name'],
                    'url'      => $media['public_url'],
                    'size'     => Helpers::formatBytes($media['file_size']),
                    'width'    => $media['width'],
                    'height'   => $media['height'],
                ];
            } catch (Exception $e) {
                $errors[] = $file['name'] . ': ' . $e->getMessage();
            }
        }

        $this->json(['uploaded' => $uploaded, 'errors' => $errors]);
    }

    public function delete(string $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $user  = $this->currentUser();
        $media = Media::find((int)$id);

        if (!$media || !Media::userOwns((int)$id, $user['id'])) {
            $this->json(['error' => 'Not found'], 404);
        }

        if (Media::isUsedInSlide((int)$id)) {
            $this->json(['error' => 'This image is used in a channel. Remove it from all channels first.'], 400);
        }

        $storage = new StorageService($media['storage_driver']);
        $storage->delete($media);
        Media::delete((int)$id);

        $this->json(['success' => true]);
    }

    public function crop(string $id): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $user  = $this->currentUser();
        $media = Media::find((int)$id);

        if (!$media || !Media::userOwns((int)$id, $user['id'])) {
            $this->json(['error' => 'Not found.'], 404);
        }

        if (!extension_loaded('gd')) {
            $this->json(['error' => 'Image processing (GD extension) is not available on this server.'], 500);
        }

        $x      = (int) round((float) ($_POST['x']      ?? 0));
        $y      = (int) round((float) ($_POST['y']      ?? 0));
        $w      = (int) round((float) ($_POST['width']  ?? 0));
        $h      = (int) round((float) ($_POST['height'] ?? 0));
        $rotate = (int) ($_POST['rotate'] ?? 0);

        if ($w < 1 || $h < 1) {
            $this->json(['error' => 'Invalid crop selection.'], 400);
        }

        $mime   = $media['mime_type'];
        $driver = $media['storage_driver'] ?? 'local';

        // Load source image
        if ($driver === 'local') {
            $filePath = PUBLIC_PATH . '/' . $media['storage_path'];
            $src = match($mime) {
                'image/jpeg' => @imagecreatefromjpeg($filePath),
                'image/png'  => @imagecreatefrompng($filePath),
                'image/webp' => @imagecreatefromwebp($filePath),
                default      => null,
            };
        } else {
            $raw = @file_get_contents($media['public_url']);
            $src = $raw ? @imagecreatefromstring($raw) : null;
        }

        if (!$src) {
            $this->json(['error' => 'Could not read image file.'], 500);
        }

        // Apply rotation (Cropper.js rotate is CW, imagerotate is CCW)
        if ($rotate !== 0) {
            $src = imagerotate($src, -$rotate, 0);
        }

        // Clamp crop box to image bounds
        $srcW = imagesx($src);
        $srcH = imagesy($src);
        $x = max(0, min($x, $srcW - 1));
        $y = max(0, min($y, $srcH - 1));
        $w = min($w, $srcW - $x);
        $h = min($h, $srcH - $y);

        // Crop + resize to 1920×1080
        $out = imagecreatetruecolor(1920, 1080);

        if ($mime === 'image/png') {
            imagealphablending($out, false);
            imagesavealpha($out, true);
            $bg = imagecolorallocatealpha($out, 0, 0, 0, 127);
            imagefilledrectangle($out, 0, 0, 1920, 1080, $bg);
        }

        imagecopyresampled($out, $src, 0, 0, $x, $y, 1920, 1080, $w, $h);
        imagedestroy($src);

        // Encode to temp file
        $tmp = tempnam(sys_get_temp_dir(), 'crop_');
        $ok  = match($mime) {
            'image/png'  => imagepng($out, $tmp, 8),
            'image/webp' => imagewebp($out, $tmp, 85),
            default      => imagejpeg($out, $tmp, 90),
        };
        imagedestroy($out);

        if (!$ok) {
            @unlink($tmp);
            $this->json(['error' => 'Failed to encode cropped image.'], 500);
        }

        $newSize = filesize($tmp);

        // Save back to storage
        if ($driver === 'local') {
            copy($tmp, $filePath);
        } else {
            $storage = new StorageService($driver);
            $storage->putContent(file_get_contents($tmp), $media['storage_path'], $mime, $driver);
        }
        @unlink($tmp);

        // Update DB record
        Database::execute(
            'UPDATE media SET width = 1920, height = 1080, file_size = ? WHERE id = ?',
            [$newSize, (int)$id]
        );

        $this->json(['success' => true, 'url' => $media['public_url'] . '?v=' . time()]);
    }
}
