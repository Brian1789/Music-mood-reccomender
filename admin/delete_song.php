<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$user = current_user();

if (!$user || empty($user['is_admin'])) {
    redirect_to('/admin/admin.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('/admin/admin.php');
}

$songId = (int) ($_POST['song_id'] ?? 0);

if ($songId <= 0) {
    set_flash('error', 'Invalid song selection.');
    redirect_to('/admin/admin.php');
}

$statement = db()->prepare('SELECT id, audio_url FROM songs WHERE id = :id LIMIT 1');
$statement->execute(['id' => $songId]);
$song = $statement->fetch();

if (!$song) {
    set_flash('error', 'Song not found.');
    redirect_to('/admin/admin.php');
}

if (!empty($song['audio_url']) && str_starts_with((string) $song['audio_url'], '/uploads/')) {
    $filePath = __DIR__ . '/../' . ltrim((string) $song['audio_url'], '/');
    if (is_file($filePath)) {
        @unlink($filePath);
    }
}

$delete = db()->prepare('DELETE FROM songs WHERE id = :id');
$delete->execute(['id' => $songId]);

set_flash('success', 'Song deleted.');
redirect_to('/admin/admin.php');
