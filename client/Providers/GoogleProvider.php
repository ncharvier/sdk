<?php

class GoogleProvider
{
    public static string $clientId = '826051565991-k2v9lljmae2ghal7ui93jibmffhkker5.apps.googleusercontent.com';
    private static string $clientSecret = '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';

    public static function callback()
    {
        $token = GoogleProvider::getToken("https://oauth2.googleapis.com/token", GoogleProvider::$clientId, GoogleProvider::$clientSecret);
        $user = GoogleProvider::getUser($token);
        echo "<pre>";
        var_dump($user);
        echo "</pre>";
    }

    private static function getToken($baseUrl, $clientId, $clientSecret)
    {
        ["code"=> $code, "state" => $state] = $_GET;
        $queryParams = http_build_query([
            "client_id"=> $clientId,
            "client_secret"=> $clientSecret,
            "redirect_uri"=>"https://localhost/go_oauth_success",
            "code"=> $code,
            "grant_type"=>"authorization_code",
        ]);

        $context = stream_context_create([
            "http"=>[
                "method"=>"POST",
                "header"=>"Accept: application/x-www-form-urlencoded",
                "content"=>$queryParams,
            ]
        ]);

        $url = $baseUrl . "?{$queryParams}";
        $response = file_get_contents($url, false, $context);
        if (!$response) {
            echo $http_response_header;
            return;
        }
        ["access_token" => $token] = json_decode($response, true);

        return $token;
    }

    private static function getUser($token)
    {
        $context = stream_context_create([
            "http"=>[
                "header"=>"Authorization: Bearer {$token}"
            ]
        ]);

        $response = file_get_contents("https://people.googleapis.com/v1/people/me?personFields=names,emailAddresses", false, $context);

        if (!$response) {
            echo $http_response_header;
            return;
        }
        return json_decode($response, true);
    }
}