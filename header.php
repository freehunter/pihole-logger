<?php
    $cmd = "echo $((`cat /sys/class/thermal/thermal_zone0/temp|cut -c1-2`)).$((`cat /sys/class/thermal/thermal_zone0/temp|cut -c3-4`))";
    $output = shell_exec($cmd);
    $output = str_replace(["\r\n","\r","\n"],"", $output);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self' https://api.github.com; script-src 'self' 'unsafe-eval'; style-src 'self' 'unsafe-inline'">
    <title>Pi-hole Admin Console</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon" />
    <meta name="theme-color" content="#367fa9">
    <link rel="apple-touch-icon" sizes="180x180" href="img/logo.svg">
    <link rel="icon" type="image/png" sizes="192x192"  href="img/logo.svg">
    <link rel="icon" type="image/png" sizes="96x96" href="img/logo.svg">
    <meta name="msapplication-TileColor" content="#367fa9">
    <meta name="msapplication-TileImage" content="img/logo.svg">

    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="css/font-awesome-4.5.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="css/ionicons-2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <link href="css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <link href="css/skin-blue.min.css" rel="stylesheet" type="text/css" />
    <link rel="icon" type="image/png" sizes="160x160" href="img/logo.svg" />
    <style type="text/css">
        .glow { text-shadow: 0px 0px 5px #fff; }
        h3 { transition-duration: 500ms }
    </style>

    <!--[if lt IE 9]>
    <script src="js/other/html5shiv.min.js"></script>
    <script src="js/other/respond.min.js"></script>
    <![endif]-->
</head>
<body class="skin-blue sidebar-mini">
<!-- JS Warning -->
<div>
    <link rel="stylesheet" type="text/css" href="css/js-warn.css">
    <input type="checkbox" id="js-hide" />
    <div class="js-warn" id="js-warn-exit"><h1>Javascript Is Disabled</h1><p>Javascript seems to be disabled. This will break some site features.</p>
        <p>To enable Javascript click <a href="http://www.enable-javascript.com/" target="_blank">here</a></p><label for="js-hide">Close</label></div>
</div>
<!-- /JS Warning -->
<script src="js/pihole/header.js"></script>
<div class="wrapper">
    <header class="main-header">
        <!-- Logo -->
        <a href="http://pi-hole.net" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>P</b>H</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Pi</b>-hole</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- User Account: style can be found in dropdown.less -->
                    <li id="dropdown-menu" class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle">
                            <img src="img/logo.svg" class="user-image" style="border-radius: initial" sizes="160x160" alt="Pi-hole logo" />
                            <span class="hidden-xs">Pi-hole</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="img/logo.svg" sizes="160x160" alt="User Image" />
                                <p>
                                    Open Source Ad Blocker
                                    <small>Designed For Raspberry Pi</small>
                                </p>
                            </li>
                            <!-- Menu Body -->
                            <li class="user-body">
                                <div class="col-xs-4 text-center">
                                    <a href="https://github.com/jacobsalmela/pi-hole">Github</a>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <a href="http://jacobsalmela.com/block-millions-ads-network-wide-with-a-raspberry-pi-hole-2-0/">Details</a>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <a href="https://github.com/pi-hole/pi-hole/releases">Updates</a>
                                </div>
                            </li>
                            <!-- Menu Footer -->
                            <li class="user-footer">
                                <!-- Update alerts -->
                                <div id="alPiholeUpdate" class="alert alert-info alert-dismissible fade in" role="alert" hidden>
                                    <a class="alert-link" href="https://github.com/pi-hole/pi-hole/releases">There's an update available for this Pi-hole!</a>
                                </div>
                                <div id="alWebUpdate" class="alert alert-info alert-dismissible fade in" role="alert" hidden>
                                    <a class="alert-link" href="https://github.com/pi-hole/AdminLTE/releases">There's an update available for this Web Interface!</a>
                                </div>

                                <!-- PayPal -->
                                <div>
                                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                                        <input type="hidden" name="cmd" value="_s-xclick">
                                        <input type="hidden" name="hosted_button_id" value="3J2L3Z4DHW9UY">
                                        <input style="display: block; margin: 0 auto;" type="image" src="img/donate.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="img/logo.svg" sizes="160x160" alt="Pi-hole logo" />
                </div>
                <div class="pull-left info">
                    <p>Status</p>
                    <?php
                        $pistatus = exec('pgrep dnsmasq | wc -l');
                        if ($pistatus > "0") {
                            echo '<a href="#"><i class="fa fa-circle" style="color:#7FFF00"></i> Active</a>';
                        } else {
                            echo '<a href="#"><i class="fa fa-circle" style="color:#FF0000"></i> Offline</a>';
                        }

                        // CPU Temp
                        if ($output > "45") {
                            echo '<a href="#"><i class="fa fa-fire" style="color:#FF0000"></i> Temp: ' . $output . '</a>';
                        } else {
                            echo '<a href="#"><i class="fa fa-fire" style="color:#3366FF"></i> Temp: ' . $output . '</a>';
                        }
                    ?>
                </div>
            </div>
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">
                <li class="header">MAIN NAVIGATION</li>
                <!-- Home Page -->
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i> <span>Main Page</span>
                    </a>
                </li>
                <!-- Query Log -->
                <li>
                    <a href="queries.php">
                        <i class="fa fa-file-text-o"></i> <span>Query Log</span>
                    </a>
                </li>
                <!-- Whitelist -->
                <li>
                    <a href="list.php?l=white">
                        <i class="fa fa-pencil-square-o"></i> <span>Whitelist</span>
                    </a>
                </li>
                <!-- Blacklist -->
                <li>
                    <a href="list.php?l=black">
                        <i class="fa fa-ban"></i> <span>Blacklist</span>
                    </a>
                </li>
                <!-- Logs -->
                <li>
                    <a href="logqueries.php">
                        <i class="fa fa-file-text-o"></i> <span>Logs</span>
                    </a>
                </li>
                <!-- Donate -->
                <li>
                    <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3J2L3Z4DHW9UY">
                        <i class="fa fa-paypal"></i> <span>Donate</span>
                    </a>
                </li>
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
