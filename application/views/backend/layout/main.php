<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <title>消息中心</title>
    <meta name="description" content="Bootstrap Metro Dashboard">
    <meta name="author" content="Dennis Ji">
    <meta name="keyword"
          content="Metro, Metro UI, Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link id="bootstrap-style" href="/resource/css/bootstrap.min.css" rel="stylesheet">
    <link href="/resource/css/jquery.dataTables.min.css" rel="stylesheet">
    <link id="base-style" href="/resource/css/style.css" rel="stylesheet">
    <link href="/resource/css/message.css" rel="stylesheet">
    <link href="/resource/css/jquery.datetimepicker.css" rel="stylesheet">
    <style>
        .filters input{
            width: 120px;
        }
    </style>
</head>

<body>
<!-- start: Header -->
<div class="navbar" style="z-index: 10;min-width: 1010px;position: absolute;width: 100%;">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="btn btn-navbar" data-toggle="collapse"
               data-target=".top-nav.nav-collapse,.sidebar-nav.nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="/event_log/index" style="padding: 0;line-height: 50px;">
                <i class="glyphicons-icon white home" style="width: 30px;"></i>
                <span>消息中心</span>
            </a>

            <!-- start: Header Menu -->
            <div class="nav-no-collapse header-nav">
                <ul class="nav pull-right">
                    <!-- start: User Dropdown -->
                    <li class="dropdown" style="margin-top: 6px;">
                        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="halflings-icon white user"></i> <?php echo $account; ?>
                        </a>
                        <a class="btn" href="/index/logout">
                            <i class="halflings-icon white off"></i> Logout
                        </a>
                    </li>
                    <!-- end: User Dropdown -->
                </ul>
            </div>
            <!-- end: Header Menu -->

        </div>
    </div>
</div>
<!-- start: Header -->

<div class="container-fluid-full" style="padding-top: 50px;box-sizing: border-box;">
    <div class="row-fluid" style="min-width: 1010px;">

        <!-- start: Main Menu -->
        <div id="sidebar-left" class="span2">
            <div class="nav-collapse sidebar-nav">
                <ul class="nav nav-tabs nav-stacked main-menu">
                    <li>
                        <a href="/change_config/change">
                            <i class="icon-align-justify"></i>
                            <span class="hidden-tablet"> 短信通道管理</span>
                        </a>
                    </li>
                    <li>
                        <a href="/telephone/index">
                            <i class="icon-align-justify"></i>
                            <span class="hidden-tablet"> 电话管理</span>
                        </a>
                    </li>
                    <li>
                        <a href="/template/index">
                            <i class="icon-align-justify"></i>
                            <span class="hidden-tablet"> 消息模板管理</span>
                        </a>
                    </li>
                    <li>
                        <a href="/message_log/index">
                            <i class="icon-align-justify"></i>
                            <span class="hidden-tablet"> 消息日志</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- end: Main Menu -->

        <noscript>
            <div class="alert alert-block span10">
                <h4 class="alert-heading">Warning!</h4>

                <p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a>
                    enabled to use this site.</p>
            </div>
        </noscript>


        <?php echo $content; ?>

