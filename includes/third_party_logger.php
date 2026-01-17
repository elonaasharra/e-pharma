<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/session.php';

/**
 * Log për komunikime me palë të treta (PayPal, Stripe, etj.)
 * Compatible me PHP 5.6+
 */
function log_third_party($data)
{
    global $conn; // mysqli nga includes/db.php

    $provider         = isset($data['provider']) ? $data['provider'] : 'unknown';
    $action           = isset($data['action']) ? $data['action'] : 'unknown';
    $status           = isset($data['status']) ? $data['status'] : 'unknown';
    $request_payload  = isset($data['request_payload']) ? $data['request_payload'] : null;
    $response_payload = isset($data['response_payload']) ? $data['response_payload'] : null;
    $http_code        = isset($data['http_code']) ? $data['http_code'] : null;
    $error_message    = isset($data['error_message']) ? $data['error_message'] : null;
    $correlation_id   = isset($data['correlation_id']) ? $data['correlation_id'] : null;

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $ip      = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;

    // Mos log-o sekrete (basic hygiene)
    // Nëse payload është array, bëje json
    if (is_array($request_payload))  $request_payload  = json_encode($request_payload, JSON_UNESCAPED_UNICODE);
    if (is_array($response_payload)) $response_payload = json_encode($response_payload, JSON_UNESCAPED_UNICODE);

    $sql = "INSERT INTO third_party_logs
            (provider, action, status, request_payload, response_payload, http_code, error_message, correlation_id, user_id, ip_address)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) return;

    // types: s s s s s i s s i s
    $stmt->bind_param(
        "sssssissis",
        $provider,
        $action,
        $status,
        $request_payload,
        $response_payload,
        $http_code,
        $error_message,
        $correlation_id,
        $user_id,
        $ip
    );

    $stmt->execute();
    $stmt->close();
}
