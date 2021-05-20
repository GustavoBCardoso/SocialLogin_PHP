<?php
require ('..\vendor\autoload.php');

ob_start();
session_start();

if(empty($_SESSION["userLogin"])){
    echo "<h1> Guest User </h1>";

    /*
    * AUTH GOOGLE
    */

    $provider = new \League\OAuth2\Client\Provider\Facebook(FACEBOOK);
    $authUrl = $provider->getAuthorizationUrl([
        "scope" => ["email"]
    ]);

    $error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRIPPED);
    $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRIPPED);

    if($error) {
        echo "<h3> Você precisa autorizar para continuar! </h3>";
    }

    if($code) {
        $token = $provider->getAccessToken('authorization_code', ['code' => $code]);
        $_SESSION['userLogin'] = $provider->getResourceOwner($token);       
        header("Location: " . FACEBOOK['redirectUri']);
        exit;
    }

    echo "<a title='Facebook Login' href='{$authUrl}'> Facebook Login </a>";

} else {
    echo "<h1> User </h1>";
    /** @var $user League\OAuth2\Client\Provider\GoogleUser */
    $user = $_SESSION["userLogin"];
    /* 
        getId()
        getName()
        getFirstName()
        getLastName()
        getEmail()
        getPictureUrl()
    */

    echo "<img width='140' src='{$user->getPictureUrl()}' height=''alt='' title=''/>";
    echo "<h1>Bem-vindo(a) {$user->getName()}</h1>";
    //var_dump($user);
    
    echo "<br><br><a title='Sair' href='?off=true'> Sair </a>";
    $off = filter_input(INPUT_GET, "off", FILTER_VALIDATE_BOOLEAN);
    if($off) {
        unset($_SESSION['userLogin']);
        header("Location: " . FACEBOOK['redirectUri']);
    }
}

ob_end_flush();
