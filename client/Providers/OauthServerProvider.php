<?php

class OauthServerProvider
{
    public static string $clientId = '67dc2be521bec2ff862d3ab057de216b';
    private static string $clientSecret = '04054cf433eeb3976252c81b6d657fda';

    // get token from code then get user info
    public static function callback()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            ["username"=> $username, "password" => $password] = $_POST;
            $specifParams = [
                "grant_type" => "password",
                "username" => $username,
                "password" => $password,
            ];
        } else {
            ["code"=> $code, "state" => $state] = $_GET;
            $specifParams = [
                "grant_type" => "authorization_code",
                "code" => $code
            ];
        }

        $queryParams = http_build_query(array_merge(
            $specifParams,
            [
                "redirect_uri" => "http://localhost:8081/oauth_success",
                "client_id" => OauthServerProvider::$clientId,
                "client_secret" => OauthServerProvider::$clientSecret,
            ]
        ));

        $response = file_get_contents("http://server:8080/token?{$queryParams}");
        if (!$response) {
            echo $http_response_header;
            return;
        }

        ["access_token" => $token] = json_decode($response, true);


        $context = stream_context_create([
            "http"=>[
                "header"=>"Authorization: Bearer {$token}"
            ]
        ]);

        $response = file_get_contents("http://server:8080/me", false, $context);
        if (!$response) {
            echo $http_response_header;
            return;
        }

        echo "<pre>";
        var_dump(json_decode($response, true));
        echo "</pre>";
    }
}