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

$title = trim($_POST['title'] ?? '');
$artist = trim($_POST['artist'] ?? '');
$mood = trim($_POST['mood'] ?? '');
$audioUrl = trim($_POST['audio_url'] ?? '');
$upload = $_FILES['audio_file'] ?? null;

if ($title === '' || $artist === '' || !valid_mood($mood)) {
    set_flash('error', 'Please complete the song details.');
    redirect_to('/admin/admin.php');
}

$storedAudioUrl = '';

if (is_array($upload) && ($upload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK && !empty($upload['tmp_name'])) {
    $allowedExtensions = ['mp3', 'wav', 'ogg', 'm4a'];
    $extension = strtolower(pathinfo((string) $upload['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions, true)) {
        set_flash('error', 'Unsupported audio file type.');
        redirect_to('/admin/admin.php');
    }

    $fileName = bin2hex(random_bytes(12)) . '.' . $extension;
    $destination = __DIR__ . '/../uploads/' . $fileName;

    if (!move_uploaded_file((string) $upload['tmp_name'], $destination)) {
        set_flash('error', 'Failed to upload the audio file.');
        redirect_to('/admin/admin.php');
    }

    $storedAudioUrl = '/uploads/' . $fileName;
} elseif ($audioUrl !== '' && filter_var($audioUrl, FILTER_VALIDATE_URL)) {
    $storedAudioUrl = $audioUrl;
}

if ($storedAudioUrl === '') {
    set_flash('error', 'Add an audio URL or upload a file.');
    redirect_to('/admin/admin.php');
}

$statement = db()->prepare('INSERT INTO songs (title, artist, mood, audio_url, created_at) VALUES (:title, :artist, :mood, :audio_url, NOW())');
$statement->execute([
    'title' => $title,
    'artist' => $artist,
    'mood' => $mood,
    'audio_url' => $storedAudioUrl,
]);

set_flash('success', 'Song added successfully.');
redirect_to('/admin/admin.php');
