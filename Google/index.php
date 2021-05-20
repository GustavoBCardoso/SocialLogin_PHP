<?php

require ('..\vendor\autoload.php');

use League\OAuth2\Client\Provider\Google;
ob_start();
session_start();
if(empty($_SESSION["userLogin"])){
    echo "<h1> Guest User </h1>";

    /*
    * AUTH GOOGLE
    */

    $provider = new Google(GOOGLE);
    $authUrl = $provider->getAuthorizationUrl();

    $error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRIPPED);
    $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRIPPED);

    if($error) {
        echo "<h3> Você precisa autorizar para continuar! </h3>";
    }

    if($code) {
        $token = $provider->getAccessToken('authorization_code', ['code' => $code]);
        $_SESSION['userLogin'] = serialize($provider->getResourceOwner($token));
        $_SESSION['Token'] = $token->getToken();
        $_SESSION['RefreshToken'] = $token->getRefreshToken();
        $_SESSION['Expires'] = $token->getExpires();
        $_SESSION['Values'] = $token->getValues();
        
        header("Location: " . GOOGLE['redirectUri']);
        exit;
    }

    echo "<a title='Google Login' href='{$authUrl}'> Google Login </a>";

} else {
    echo "<h1> User </h1>";
    /** @var League\OAuth2\Client\Provider\GoogleUser $user */
    $user = unserialize($_SESSION["userLogin"]);
    /*  DADOS DISPONIVEIS
        getAvatar
        getEmail
        getLastName
        getFirstName
        getId
        getName
    */

    echo "<img width='140' src='{$user->getAvatar()}' alt='' title=''/>";
    echo "<h1>Bem-vindo(a) {$user->getName()}</h1>";

    echo "<br><br><a title='Sair' href='?off=true'> Sair </a>";
    $off = filter_input(INPUT_GET, "off", FILTER_VALIDATE_BOOLEAN);
    if($off) {
        unset($_SESSION['userLogin']);
        header("Location: " . GOOGLE['redirectUri']);
    }
}

ob_end_flush();
