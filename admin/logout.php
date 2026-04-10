<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

logout_user();
set_flash('success', 'Admin session ended.');
redirect_to('/admin/admin.php');
