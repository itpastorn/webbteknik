    <?php
    /**
     * Testing curl and persona
     *
     * Currently a connection is made when running from the CLI, but not when accessed via web
     */

    echo "<pre>\n";

    $data = new StdClass();
    $data->assertion = "foo";
    $data->audience = "http://localhost";

    // Do curl
    $url = 'https://verifier.login.persona.org/verify';
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_HEADER         => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        /*
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_FOLLOWLOCATION => false,
        CURLINFO_HEADER_OUT    => true,
        CURLOPT_CAINFO         => '/etc/ssl/certs/ca-bundle.crt',
        */
        CURLOPT_HTTPHEADER => array('Content-Type: application/json')
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    echo "EXEC now\n\n";
    $response = curl_exec($ch);
    echo curl_error($ch);
    echo "\n\n";
    $info = curl_getinfo($ch);
    foreach ($info as $ki => $ii) {
        if ( is_array($ii) ) {
            $ii = "(array)";
        }
        echo $ki . " => " . $ii . "\n";
    }
    curl_close($ch);

    // Check response
    if ( empty($response) ) {
        header("HTTP/1.0 401 Authentication is possible but has failed");
        echo 'Response is empty - assertion failed: ';
        echo '{"reason" : "Assertion failed, verifying server returned empty content"}';
        exit;
    }

    //$response = json_decode($response);
    echo 'Response decoded: ' . $response . "\n\n";
