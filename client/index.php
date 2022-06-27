<?php

require "Providers/OauthServerProvider.php";
require "Providers/FacebookProvider.php";
require "Providers/GithubProvider.php";

// Create a login page with a link to oauth
function login()
{
    $queryParams = http_build_query([
        "state"=>bin2hex(random_bytes(16)),
        "client_id"=> OauthServerProvider::$clientId,
        "scope"=>"profile",
        "response_type"=>"code",
        "redirect_uri"=>"http://localhost:8081/oauth_success",
    ]);

    echo "
        <div class='m-5'>
            <form method=\"POST\" action=\"/oauth_success\">
                <input class='form-control mb-2' type=\"text\" name=\"username\" placeholder='Username'/>
                <input class='form-control mb-2' type=\"password\" name=\"password\" placeholder='Password'/>
                <input class='btn btn-sm btn-success mb-2' type=\"submit\" value=\"Login\"/>
            </form>
    ";

    $fbQueryParams = http_build_query([
        "state"=>bin2hex(random_bytes(16)),
        "client_id"=> FacebookProvider::$clientId,
        "scope"=>"public_profile,email",
        "redirect_uri"=>"https://localhost/fb_oauth_success",
    ]);

    $ghQueryParams = http_build_query([
        "state"=>bin2hex(random_bytes(16)),
        "client_id"=> GithubProvider::$clientId,
        "scope"=>"user",
        "redirect_uri"=>"https://localhost/gh_oauth_success",
    ]);

    echo "<a class='btn btn-sm btn-primary mb-2' href=\"http://localhost:8080/auth?{$queryParams}\">Login with Oauth-Server</a><br>";
    echo "<a class='btn btn-sm btn-primary mb-2' href=\"https://www.facebook.com/v13.0/dialog/oauth?{$fbQueryParams}\">Login with Facebook</a><br>";
    echo "<a class='btn btn-sm btn-primary mb-2' href=\"https://github.com/login/oauth/authorize?{$ghQueryParams}\">Login with Github</a><br>";

    echo "</div>";
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>OAuth2 - connection hub</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    </head>
    <body>
        <?php
        $route = $_SERVER["REQUEST_URI"];
        switch (strtok($route, "?")) {
            case '/login':
                login();
                break;
            case '/oauth_success':
                OauthServerProvider::callback();
                break;
            case '/fb_oauth_success':
                FacebookProvider::callback();
                break;
            case '/gh_oauth_success':
                GithubProvider::callback();
                break;
            default:
                http_response_code(404);
        }
        ?>
    </body>
</html>


