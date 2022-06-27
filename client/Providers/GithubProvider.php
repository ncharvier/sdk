<?php

class GithubProvider
{
    public static string $clientId = '6ff68a9f78bee7536d39';
    private static string $clientSecret = '2cb7c224aa2a7e2a17b8eeb537a26de761c745cc';

    public static function callback()
    {
        $token = GithubProvider::getToken("https://github.com/login/oauth/access_token", GithubProvider::$clientId, GithubProvider::$clientSecret);
        $user = GithubProvider::getUser($token);
        $unifiedUser = (fn () => [
            "id" => $user["id"],
            "name" => $user["name"],
            "email" => $user["email"],
            "firstName" => $user['first_name'],
            "lastName" => $user['last_name'],
        ])();
        echo "<pre>";
        var_dump($unifiedUser);
        echo "</pre>";
    }

    private static function getToken($baseUrl, $clientId, $clientSecret)
    {
        $context = stream_context_create([
            "http"=>[
                "header"=>"Accept: application/json"
            ]
        ]);

        ["code"=> $code, "state" => $state] = $_GET;
        $queryParams = http_build_query([
            "client_id"=> $clientId,
            "client_secret"=> $clientSecret,
            "redirect_uri"=>"https://localhost/gh_oauth_success",
            "code"=> $code,
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
                "header"=>"Authorization: token {$token}\r\n" .
                          "User-Agent: SDK Application"
            ]
        ]);

        $response = file_get_contents("https://api.github.com/user", false, $context);

        if (!$response) {
            echo $http_response_header;
            return;
        }
        return json_decode($response, true);
    }
}