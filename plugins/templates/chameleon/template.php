<?php

use Sunlight\Hcm;
use Sunlight\Template;

if (!defined('SL_ROOT')) exit;

$config = Template::getCurrent()->getConfig();

// content column size
$content_size = "";
if (!$config['show_left_sidebar'] && !$config['show_right_sidebar']) {
    $content_size = "md-twelve lg-twelve";
} elseif ($config['show_left_sidebar'] && $config['show_right_sidebar']) {
    $content_size = "md-six lg-six";
} else {
    $content_size = "md-nine lg-nine";
}
?>

<div id="mobileNav" class="sidenav" style="">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <?= Template::userMenu(); ?>
    <div class="hr"></div>
    <?= Template::menu($config['menu_start'], $config['menu_end']); ?>
    <div class="cleaner"></div>
</div>

<div id="wrapper" class="container">

    <div class="container header-container">
        <div class="superheader">

            <div id="header-usermenu" class="row">
                <div class="xs-twelve column"><?= Template::userMenu(); ?></div>
            </div>

            <div id="header" class="row">
                <div id="logo" class="xs-ten sm-ten md-eight column">
                    <div class="site-name">
                        <a href="<?= _e(Template::sitePath()); ?>"><?= Template::siteTitle(); ?></a>
                    </div>
                    <span class="site-description"><?= Template::siteDescription(); ?></span>
                </div>
                <div class="xs-two sm-two columns mobile-nav">
                    <span class="pull-right side-menu-hidden hamburger-icon" onclick="javascript:openNav()">&#9776;</span>
                </div>
                <div id="seachbox" class="xs-twelve sm-twelve md-four column"><?= Hcm::parse("[hcm]search[/hcm]"); ?></div>
            </div>
        </div>

    </div>

    <div class="container mainmenu-container">
        <div id="menu" class="row">
            <div class="twelve column"><?= Template::menu($config['menu_start'], $config['menu_end']); ?></div>
        </div>
        <?php
        if ($config['show_breadcrumbs']) {
            echo '<div id="breadcrumbs" class="row"><div class="twelve column"><span>' . _lang('chameleon.breadcrumb_caption') . ': </span>' . Template::breadcrumbs() . '</div></div>';
        }
        ?>
    </div>

    <div id="page" class="container content-container">
        <div class="row">
            <?php
            if ($config['show_left_sidebar']) {
                echo '<div id="left_sidebar" class="md-three column">'
                    . Template::boxes($config['switch_sidebars'] ? 'right' : 'left')
                    . '</div>';
            }
            ?>
            <div id="content" class="<?= $content_size; ?> column">
                <?= Template::content(); ?>
            </div>
            <?php
            if ($config['show_right_sidebar']) {
                echo '<div id="right_sidebar" class="md-three column">'
                    . Template::boxes($config['switch_sidebars'] ? 'left' : 'right')
                    . '</div>';
            }
            ?>
        </div>
    </div>
</div>

<div id="footer">
    <ul><?= Template::links(); ?>
        <li>
            <small>ChameleonTheme by</small>
            <a href="https://jdanek.eu" target="_blank">jDanek</a>
        </li>
    </ul>
</div>
<script src="<?= Template::asset('public/js/menu.js') ?>"></script>
