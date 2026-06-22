<?php
class ScheduleController extends BaseController
{
    public function index(string $channelId): void
    {
        $this->requireAuth();
        [$channel, $sub] = $this->loadChannelAndSub($channelId);
        $this->requireScheduling($sub);

        $editId  = (int)($_GET['edit'] ?? 0);
        $editing = $editId ? ContentSchedule::belongsToChannel($editId, (int)$channelId) : null;

        $this->view('channels/schedules', [
            'title'     => 'Schedules – ' . Helpers::e($channel['name']),
            'channel'   => $channel,
            'schedules' => ContentSchedule::forChannel((int)$channelId),
            'media'     => Media::forUser($this->currentUser()['id']),
            'editing'   => $editing,
        ]);
    }

    public function store(string $channelId): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        [$channel, $sub] = $this->loadChannelAndSub($channelId);
        $this->requireScheduling($sub);

        [$data, $error] = $this->parseForm();
        if ($error) {
            Session::flash('error', $error);
            $this->redirect('/channels/' . $channelId . '/schedules');
        }

        // Verify media belongs to this user
        $userId   = $this->currentUser()['id'];
        $data['media_ids'] = $this->filterOwnedMedia($data['media_ids'], $userId);
        if (empty($data['media_ids'])) {
            Session::flash('error', 'Select at least one image you own.');
            $this->redirect('/channels/' . $channelId . '/schedules');
        }

        ContentSchedule::create((int)$channelId, $data);
        ActivityLog::log('schedule_created', "Schedule '{$data['name']}' created for channel #{$channelId}");
        Session::flash('success', 'Schedule created.');
        $this->redirect('/channels/' . $channelId . '/schedules');
    }

    public function update(string $channelId, string $scheduleId): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        [$channel, $sub] = $this->loadChannelAndSub($channelId);
        $this->requireScheduling($sub);

        $schedule = ContentSchedule::belongsToChannel((int)$scheduleId, (int)$channelId);
        if (!$schedule) $this->abort(404);

        [$data, $error] = $this->parseForm();
        if ($error) {
            Session::flash('error', $error);
            $this->redirect('/channels/' . $channelId . '/schedules');
        }

        $userId   = $this->currentUser()['id'];
        $data['media_ids'] = $this->filterOwnedMedia($data['media_ids'], $userId);
        if (empty($data['media_ids'])) {
            Session::flash('error', 'Select at least one image you own.');
            $this->redirect('/channels/' . $channelId . '/schedules');
        }

        ContentSchedule::update((int)$scheduleId, $data);
        ActivityLog::log('schedule_updated', "Schedule #{$scheduleId} updated for channel #{$channelId}");
        Session::flash('success', 'Schedule updated.');
        $this->redirect('/channels/' . $channelId . '/schedules');
    }

    public function destroy(string $channelId, string $scheduleId): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        [$channel, $sub] = $this->loadChannelAndSub($channelId);
        $this->requireScheduling($sub);

        $schedule = ContentSchedule::belongsToChannel((int)$scheduleId, (int)$channelId);
        if (!$schedule) $this->abort(404);

        Database::execute('DELETE FROM content_schedules WHERE id = ?', [(int)$scheduleId]);
        ActivityLog::log('schedule_deleted', "Schedule #{$scheduleId} deleted from channel #{$channelId}");
        Session::flash('success', 'Schedule deleted.');
        $this->redirect('/channels/' . $channelId . '/schedules');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function loadChannelAndSub(string $channelId): array
    {
        $user    = $this->currentUser();
        $channel = Channel::find((int)$channelId);
        if (!$channel || !Channel::userOwns((int)$channelId, $user['id'])) {
            $this->abort(404);
        }
        return [$channel, Subscription::forUser($user['id'])];
    }

    private function requireScheduling(?array $sub): void
    {
        if (!Subscription::isActive($sub) || empty($sub['scheduling_enabled'])) {
            Session::flash('error', 'Content scheduling is available on the Pro plan. Please upgrade to access this feature.');
            $this->redirect('/billing');
        }
    }

    private function filterOwnedMedia(array $ids, int $userId): array
    {
        return array_values(array_filter($ids, fn($id) => Media::userOwns($id, $userId)));
    }

    private function parseForm(): array
    {
        $name   = Helpers::sanitize($_POST['name'] ?? '');
        $starts = trim($_POST['starts_at'] ?? '');
        $ends   = trim($_POST['ends_at'] ?? '');

        if (strlen($name) < 2)               return [[], 'Schedule name must be at least 2 characters.'];
        if (!$starts || !strtotime($starts)) return [[], 'A valid start date and time is required.'];
        if ($ends && strtotime($ends) <= strtotime($starts)) {
            return [[], 'End date/time must be after the start date/time.'];
        }

        $mediaIds = array_values(array_filter(array_map('intval', (array)($_POST['media_ids'] ?? []))));
        if (empty($mediaIds)) return [[], 'Select at least one image for this schedule.'];

        return [[
            'name'      => $name,
            'starts_at' => date('Y-m-d H:i:s', strtotime($starts)),
            'ends_at'   => $ends ? date('Y-m-d H:i:s', strtotime($ends)) : null,
            'media_ids' => $mediaIds,
            'priority'  => max(0, min(10, (int)($_POST['priority'] ?? 0))),
        ], null];
    }
}
