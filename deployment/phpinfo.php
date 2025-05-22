<?php
// This file is for checking PHP configuration on the server
// Delete this file after deployment for security reasons

// Check if a security token is provided
$securityToken = 'change_this_to_a_secure_random_string';

if (!isset($_GET['token']) || $_GET['token'] !== $securityToken) {
    die('Unauthorized access. Please provide a valid security token.');
}

// Display PHP information
phpinfo();
