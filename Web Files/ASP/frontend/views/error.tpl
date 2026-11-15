<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <!-- Metas -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <!-- Mobile metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

    <!-- Page title and icon -->
    <title>Private Stats Admin :: Internal Server Error</title>
    <link rel="icon" type="image/png" href="/ASP/frontend/images/bf2_icon.png">

    <!-- JavaScript Plugins -->
    <script type="text/javascript" src="/ASP/frontend/js/jquery-1.8.3.min.js"></script>

    <!-- Plugin Scripts -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/ASP/frontend/js/excanvas.min.js"></script>
    <![endif]-->

    <!-- Required Stylesheets -->
    <link rel="stylesheet" type="text/css" href="/ASP/frontend/css/core/error.css" media="screen">
</head>
<body class="special-page error-bg red dark with-log">
    <section id="error-desc">
        <ul class="action-tabs with-children-tip children-tip-left">
            <li>
                <a href="javascript:history.back()" title="Go back">
                    <img src="/ASP/frontend/images/icons/fugue/navigation-180.png" width="16" height="16" alt="Go Back">
                </a>
            </li>
            <li>
                <a href="javascript:window.location.reload()" title="Reload page">
                    <img src="/ASP/frontend/images/icons/fugue/arrow-circle.png" width="16" height="16" alt="Reload Page">
                </a>
            </li>
        </ul>
        <ul class="action-tabs right with-children-tip children-tip-right">
            <li>
                <a href="https://github.com/BF2Statistics/ASP/issues"
                   title="Get Support for this Error"
                   target="_blank">
                    <img src="/ASP/frontend/images/icons/fugue/balloon-reverse.png" width="16" height="16" alt="Support">
                </a>
            </li>
        </ul>
        <div class="block-border">
            <div class="block-content no-title">
                <div class="block-header">{headline}</div>

                <h2>Error Description</h2>
                <div class="fieldset grey-bg with-margin">
                    <p>
                        {message}
                    </p>
                </div>

                <h2>Error Details</h2>
                <ul class="picto-list">
                    <li class="icon-type-small"><span class="bold">Type:</span> {type}</li>
                    <li class="icon-tag-small"><span class="bold">Code:</span> {code}</li>
                    <li class="icon-doc-small"><span class="bold">File:</span> {file}</li>
                    <li class="icon-pin-small"><span class="bold">Line:</span> {line}</li>
                </ul>

                <h2>Stack Trace</h2>
                <ul class="picto-list icon-top with-line-spacing">
                    {stacktrace}
                    <li class="force-wrap">
                        <span class="bold">{file}</span> @ line <span class="bold">{line}</span>:
                        <br />
                        <ul class="picto-list">
                            <li class="icon-arr-small"><span>{func}({args})</span></li>
                        </ul>
                    </li>
                    {/stacktrace}
                </ul>
            </div>
        </div>
    </section>
</body>
</html>