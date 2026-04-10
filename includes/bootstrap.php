<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../admin/db/connection.php';

function db(): PDO
{
    return getDBConnection();
}

function app_name(): string
{
    return 'Music Mood Recommender';
}

function esc(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect_to(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function mood_options(): array
{
    return [
        'Happy' => ['emoji' => '😊', 'label' => 'Happy', 'hint' => 'Bright and uplifting tracks'],
        'Sad' => ['emoji' => '😢', 'label' => 'Sad', 'hint' => 'Soft songs for quiet moments'],
        'Energetic' => ['emoji' => '⚡', 'label' => 'Energetic', 'hint' => 'High-tempo motivation'],
        'Relaxed' => ['emoji' => '🌙', 'label' => 'Relaxed', 'hint' => 'Calm and easy listening'],
        'Romantic' => ['emoji' => '❤️', 'label' => 'Romantic', 'hint' => 'Warm, tender moods'],
        'Angry' => ['emoji' => '🔥', 'label' => 'Angry', 'hint' => 'Heavy beats to match intensity'],
    ];
}

function valid_mood(string $mood): bool
{
    return array_key_exists($mood, mood_options());
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']) && is_array($_SESSION['user']);
}

function current_user(): ?array
{
    return is_logged_in() ? $_SESSION['user'] : null;
}

function login_user(array $user): void
{
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'username' => $user['username'],
        'email' => $user['email'] ?? null,
        'is_admin' => (bool) ($user['is_admin'] ?? false),
    ];
}

function logout_user(): void
{
    unset($_SESSION['user']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect_to('/login.php');
    }
}

function require_admin(): void
{
    $user = current_user();

    if (!$user || empty($user['is_admin'])) {
        redirect_to('/admin/admin.php');
    }
}

function fetch_songs(?string $mood = null): array
{
    $pdo = db();

    if ($mood !== null && valid_mood($mood)) {
        $statement = $pdo->prepare('SELECT id, title, artist, mood, audio_url FROM songs WHERE mood = :mood ORDER BY id DESC');
        $statement->execute(['mood' => $mood]);
    } else {
        $statement = $pdo->query('SELECT id, title, artist, mood, audio_url FROM songs ORDER BY id DESC');
    }

    return $statement->fetchAll();
}

function normalize_audio_url(string $value): string
{
    if ($value === '') {
        return '';
    }

    if (preg_match('~^https?://~i', $value)) {
        return $value;
    }

    return '/' . ltrim($value, '/');
}
