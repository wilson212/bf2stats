<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<!-- Title and Icon -->
    <title>BF2 Private Stats - Login Page</title>
    <link rel="icon" type="image/png" href="/ASP/frontend/images/icons/bf2.png">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <!-- Viewport Metatag -->
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <!-- Required Stylesheets -->
    <link rel="stylesheet" type="text/css" href="/ASP/frontend/css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/ASP/frontend/css/fonts/ptsans/stylesheet.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/ASP/frontend/css/fonts/icomoon/style.css" media="screen">

    <link rel="stylesheet" type="text/css" href="/ASP/frontend/css/core/login.css" media="screen">
	<link rel="stylesheet" type="text/css" href="/ASP/frontend/css/bf2.theme.css" media="screen" />

    <!-- JavaScript Plugins -->
    <script type="text/javascript" src="/ASP/frontend/js/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="/ASP/frontend/js/placeholder/jquery.placeholder.min.js"></script>

    <!-- jQuery-UI Dependent Scripts -->
    <script type="text/javascript" src="/ASP/frontend/jui/js/jquery-ui-effects.min.js"></script>

    <!-- Plugin Scripts -->
    <script type="text/javascript" src="/ASP/frontend/js/validate/jquery.validate-min.js"></script>

    <!-- Login Script -->
    <script type="text/javascript" src="/ASP/frontend/js/login.js"></script>
</head>

<body>

<div id="mws-login-wrapper">
    <div id="mws-login">
        <h1><img src="/ASP/frontend/images/icons/bf2.png"> Private Stats Login</h1>
        <div class="mws-login-lock"><i class="icon-lock"></i></div>
        <div id="mws-login-form">
            <form class="mws-form" method="post">
                <input type="hidden" name="action" value="login" />
                <input type="hidden" name="csrf_token" value="<?php echo \System\Security::CsrfToken(); ?>" />
                <div class="mws-form-row">
                    <div class="mws-form-item">
                        <input type="text" name="username" class="mws-login-username required" placeholder="username" autocomplete="off">
                    </div>
                </div>
                <div class="mws-form-row">
                    <div class="mws-form-item">
                        <input type="password" name="password" class="mws-login-password required" placeholder="password" autocomplete="off">
                    </div>
                </div>
                <div class="mws-form-row">
                    <input type="submit" value="Login" class="btn btn-danger mws-login-button">
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
