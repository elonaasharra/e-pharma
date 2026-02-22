<?php
require_once __DIR__ . '/paypal_config.php';
require_once __DIR__ . '/third_party_logger.php';  // ka funksion per te ruajtur request/response ne db ose file log

function paypal_curl($method, $url, $headers, $body)  // funksion ndihmes i cili ben kerkesen http me curl
{
    //cURL esht nje pjet php qe dergon kerkesa ne internet dhe merr pergjigje nga servera te tjere
    $ch = curl_init($url); //handler
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }

    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    return array($http, $resp, $err);
}

//merr akses token
function paypal_get_access_token()
{
    $url = PAYPAL_API_BASE . '/v1/oauth2/token';

    $headers = array(
        'Accept: application/json',
        'Accept-Language: en_US',
        'Content-Type: application/x-www-form-urlencoded'
    );

    $body = 'grant_type=client_credentials';

    // Basic auth
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ':' . PAYPAL_SECRET);

    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    $decoded = json_decode($resp, true);

    // LOG (mos log-o secret)
    log_third_party(array(
        'provider' => 'paypal',
        'action' => 'get_access_token',
        'status' => ($http >= 200 && $http < 300) ? 'success' : 'error',
        'request_payload' => array('grant_type' => 'client_credentials'),
        'response_payload' => $decoded ? $decoded : $resp,
        'http_code' => $http,
        'error_message' => $err ? $err : (($http >= 300 && isset($decoded['error_description'])) ? $decoded['error_description'] : null)
    ));

    if ($http >= 200 && $http < 300 && isset($decoded['access_token'])) {
        return $decoded['access_token'];
    }

    return null;
} // merr celsin e hyrjes

//krijojm order
function paypal_create_order($amount, $currency, $return_url, $cancel_url)
{
    $token = paypal_get_access_token();
    if (!$token) return array(false, 'No access token', null);

    $url = PAYPAL_API_BASE . '/v2/checkout/orders';

    $payload = array(
        'intent' => 'CAPTURE',
        'purchase_units' => array(
            array(
                'amount' => array(
                    'currency_code' => $currency,
                    'value' => number_format((float)$amount, 2, '.', '')
                )
            )
        ),
        'application_context' => array(
            'return_url' => $return_url,
            'cancel_url' => $cancel_url
        )
    );

    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    );

    list($http, $resp, $err) = paypal_curl('POST', $url, $headers, json_encode($payload));
    $decoded = json_decode($resp, true);

    log_third_party(array(
        'provider' => 'paypal',
        'action' => 'create_order',
        'status' => ($http >= 200 && $http < 300) ? 'success' : 'error',
        'request_payload' => $payload,
        'response_payload' => $decoded ? $decoded : $resp,
        'http_code' => $http,
        'error_message' => $err ? $err : (($http >= 300 && isset($decoded['message'])) ? $decoded['message'] : null),
        'correlation_id' => isset($decoded['id']) ? $decoded['id'] : null
    ));

    if ($http >= 200 && $http < 300 && isset($decoded['id'])) {
        return array(true, null, $decoded);
    }

    return array(false, 'Create order failed', $decoded);
}

//capture order
function paypal_capture_order($order_id)
{
    $token = paypal_get_access_token();
    if (!$token) return array(false, 'No access token', null);

    $url = PAYPAL_API_BASE . '/v2/checkout/orders/' . urlencode($order_id) . '/capture';

    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    );

    list($http, $resp, $err) = paypal_curl('POST', $url, $headers, null);
    $decoded = json_decode($resp, true);

    log_third_party(array(
        'provider' => 'paypal',
        'action' => 'capture_order',
        'status' => ($http >= 200 && $http < 300) ? 'success' : 'error',
        'request_payload' => array('order_id' => $order_id),
        'response_payload' => $decoded ? $decoded : $resp,
        'http_code' => $http,
        'error_message' => $err ? $err : (($http >= 300 && isset($decoded['message'])) ? $decoded['message'] : null),
        'correlation_id' => $order_id
    ));

    if ($http >= 200 && $http < 300) {
        return array(true, null, $decoded);
    }

    return array(false, 'Capture failed', $decoded);
} //finalizon pagesen dhe merr parate
