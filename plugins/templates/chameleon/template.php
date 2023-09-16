<?php

use Sunlight\Hcm;
use Sunlight\Template;

if (!defined('SL_ROOT')) exit;

$config = Template::getCurrent()->getConfig();

$breadcrumbs = ($config['show_breadcrumbs'] ? Template::breadcrumbs([], true) : '');
$leftBox = ($config['show_left_sidebar'] ? (include __DIR__ . DIRECTORY_SEPARATOR . 'script/includes/left_box.php') : '');;
$rightBox = ($config['show_right_sidebar'] ? (include __DIR__ . DIRECTORY_SEPARATOR . 'script/includes/right_box.php') : '');;

?>

<div id="mobileNav" class="sidenav" style="">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <?= Template::userMenu(); ?>
    <div class="hr"></div>
    <?= Template::menu($config['menu_start'], $config['menu_end']); ?>
    <div class="cleaner"></div>
</div>

<div class="wrapper container-fluid">

    <section>

        <header>

            <nav id="user-menu" class="row">
                <div id="header-usermenu" class="col-xs-12">
                    <div class="box"><?= Template::userMenu(); ?></div>
                </div>
            </nav>

            <div class="row">

                <div id="logo" class="col-xs-10 col-sm-6 col-md-8">
                    <div class="box">
                        <div class="site-name">
                            <a href="<?= _e(Template::sitePath()); ?>"><?= Template::siteTitle(); ?></a>
                        </div>
                        <span class="site-description"><?= Template::siteDescription(); ?></span>
                    </div>
                </div>

                <div class="col-xs-2 mobile-nav">
                    <div id="hamburger-menu" class="box">
                        <span class="hamburger-icon" onclick="javascript:openNav()">&#9776;</span>
                    </div>
                </div>

                <div id="seachbox" class="col-xs-12 col-sm-6 col-md-4">
                    <div class="box">
                        <?= Hcm::parse("[hcm]search[/hcm]"); ?>
                    </div>
                </div>

            </div>

            <div id="menu" class="col-xs-12">
                <div class="box"><?= Template::menu($config['menu_start'], $config['menu_end']); ?></div>
            </div>

        </header>

        <section id="page">

            <?= $breadcrumbs ?>

            <div class="row">

                <?= $leftBox ?>

                <div id="content" class="col-xs first-xs">
                    <div class="box">
                        <?= Template::heading(); ?>
                        <?= Template::content(); ?>
                    </div>
                </div>

                <?= $rightBox ?>

            </div>

        </section>

    </section>

</div>

<div id="footer" class="row">
    <div class="col-xs-12">
        <div class="box">
            <ul><?= Template::links(); ?>
                <li>
                    Template Chameleon by
                    <a href="https://github.com/friends-of-sunlight-cms/" target="_blank">Friends of SunLight CMS</a>
                </li>
            </ul>
        </div>
    </div>
</div>
<script src="<?= Template::asset('public/js/menu.js') ?>"></script>
