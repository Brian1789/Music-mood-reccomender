<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Login required.',
        'songs' => [],
    ]);
    exit;
}

$mood = trim($_GET['mood'] ?? '');

if (!valid_mood($mood)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Please choose a valid mood.',
        'songs' => [],
    ]);
    exit;
}

$statement = db()->prepare('SELECT id, title, artist, mood, audio_url FROM songs WHERE mood = :mood ORDER BY id DESC');
$statement->execute(['mood' => $mood]);
$songs = array_map(static function (array $song): array {
    return [
        'id' => (int) $song['id'],
        'title' => $song['title'],
        'artist' => $song['artist'],
        'mood' => $song['mood'],
        'audio_url' => normalize_audio_url($song['audio_url']),
    ];
}, $statement->fetchAll());

echo json_encode([
    'success' => true,
    'message' => 'Songs loaded.',
    'songs' => $songs,
]);
