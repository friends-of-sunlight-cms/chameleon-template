<?php

use Sunlight\Core;
use Sunlight\Template;
use Sunlight\Util\Color;
use Sunlight\Util\DateTime;
use Sunlight\Util\Math;

// init core
require '../../../../../system/bootstrap.php';
Core::init('../../../../../', array(
    'env' => Core::ENV_WEB,
    'session_enabled' => false,
    'content_type' => 'text/css; charset=UTF-8',
));

// get template properties
$template = Template::getCurrent();
$config = $template->getConfig();
$random = $config->offsetGet('random');
$theme_path = $template->getWebPath() . '/';

// don't cache in the debug
header('Expires: ' . DateTime::formatForHttp((_debug || $random ? 1 : 2592000), true));

//prepare combination
if ($random) {
    $plist = $template->getPatternList();
    $pattern = pathinfo($plist[Math::randomInt(1, $config->offsetGet('pattern_counter'))]['file'], PATHINFO_FILENAME);
} else {
    $pattern = $config->offsetGet('pattern');
}
$header_bg = $config->offsetGet('header');
$color_schema = ($random ? Math::randomInt(0, 10) : $config->offsetGet('active'));
$GLOBALS['cdark'] = ($random ? (bool)Math::randomInt(0, 1) : $config->offsetGet('dark_mode'));

global $hue, $sat, $cdark, $light;

if (!function_exists('_chameleon_color')) {
    function _chameleon_color($loff = 0, $satc = null, $sat_abs = false, $light_abs = false)
    {
        // nacteni a uprava barev
        if ($satc === 0) {
            $light_abs = true;
            $loff += 127;
        }
        $h = $GLOBALS['hue'];
        if ($GLOBALS['cdark']) {
            $l = ($light_abs ? 255 - $loff : $GLOBALS['light'] - $loff);
        } else {
            $l = ($light_abs ? $loff : $GLOBALS['light'] + $loff);
        }
        $s = (isset($satc) ? ($sat_abs ? $satc : $GLOBALS['sat'] * $satc) : $GLOBALS['sat']);

        // vytvoreni hex kodu barvy
        $color = new Color(array($h, $s, $l), 1);

        return $color->getRgbStr();
    }
}

// vychozi HSL hodnoty
$hue = 0;
$light = 127;
$sat = 255;

// vychozi barevne hodnoty
$theme_link = null;
$theme_bar_text = null;
$theme_bar_shadow = null;
$theme_bar_flip = false;
if ($cdark) {
    $theme_white = '#000';
    $theme_black = '#fff';
    $theme_bg_info = '#00626A';
    $theme_bg_alert = '#845100';
    $theme_bg_danger = '#840000';
} else {
    $theme_white = '#fff';
    $theme_black = '#000';
    $theme_bg_info = '#D0EDEE';
    $theme_bg_alert = '#FFD183';
    $theme_bg_danger = '#FFA7A7';
}
$theme_bar_loff = 30;
$theme_text = $theme_black;
if ($cdark) {
    $theme_contrast = $theme_black;
    $theme_contrast2 = $theme_white;
} else {
    $theme_contrast = $theme_white;
    $theme_contrast2 = $theme_black;
}
$theme_link_loff = ($cdark ? -20 : -10);
$dark_suffix = ($cdark ? '_dark' : '');

// uprava podle schematu
switch ($color_schema) {
    // modry
    case 1:
        $hue = 145;
        $sat -= 10;
        break;

    // zeleny
    case 2:
        $hue = 70;
        if (!$cdark) {
            $light -= 20;
        }
        $sat *= 0.7;
        break;

    // cerveny
    case 3:
        $hue = 5;
        if (!$cdark) {
            $light -= 10;
        }
        break;

    // zluty
    case 4:
        $hue = 35;
        $theme_contrast = $theme_black;
        $theme_link = '#BE9B02';
        if (!$cdark) {
            $light -= 20;
            $theme_bar_flip = true;
        } else {
            $light += 5;
        }
        break;

    // purpurovy
    case 5:
        $hue = 205;
        break;

    // azurovy
    case 6:
        $hue = 128;
        if (!$cdark) {
            $light -= 10;
            $sat -= 70;
            $theme_link_loff -= 10;
            $theme_bar_flip = true;
        }
        break;

    // fialovy
    case 7:
        $hue = 195;
        if ($cdark) {
            $light += 10;
        }
        break;

    // hnedy
    case 8:
        $hue = 20;
        $light -= 10;
        $sat *= 0.6;
        break;

    // tmave modry
    case 9:
        $hue = 170;
        if (!$cdark) {

        } else {
            $theme_link_loff -= 20;
        }
        $sat *= 0.5;
        break;

    // sedy
    case 10:
        $hue = 150;
        $sat = 0;
        $theme_link = '#67939F';
        $theme_bar_loff = 50;
        if (!$cdark) {
            $theme_bar_flip = true;
        }
        break;

    // oranzovy
    default:
        $hue = 14;
        $theme_link = '#F84A00';
        $light -= 10;
        break;
}

// vypocet barev
$theme = _chameleon_color(($cdark ? 40 : 0));
$theme_lighter = _chameleon_color(80);
$theme_lightest = _chameleon_color(100);
$theme_smoke = _chameleon_color(115, 0);
$theme_smoke_text = _chameleon_color($light * 0.2, 0);
$theme_smoke_text_dark = _chameleon_color(10, 0);
$theme_smoke_text_darker = _chameleon_color(-30, 0);
$theme_smoke = _chameleon_color(110, 0);
$theme_smoke_med = _chameleon_color(90, 0);
$theme_smoke_dark = _chameleon_color(60, 0);
$theme_smoke_darker = _chameleon_color($cdark ? -20 : -10, 0);
$theme_smoke_light = _chameleon_color(110, 0);
$theme_smoke_lighter = _chameleon_color(118, 0);
$theme_smoke_lightest = _chameleon_color(125, 0);
$theme_smoke_lightest_colored = _chameleon_color(125);
$theme_med = _chameleon_color(30);
$theme_dark = _chameleon_color(-10);
$theme_bar = _chameleon_color($theme_bar_loff);

if ($theme_link == null) {
    $theme_link = _chameleon_color($theme_link_loff, 255, true);
}
if ($theme_bar_shadow === null) {
    $theme_bar_shadow = ($theme_bar_flip ? 'rgba(255, 255, 255, 0.3)' : 'rgba(0, 0, 0, 0.3)');
}
if ($cdark) {
    $theme_bar_flip = !$theme_bar_flip;
}
if ($theme_bar_text === null) {
    $theme_bar_text = ($theme_bar_flip ? $theme_black : $theme_white);
}
if ($cdark) {
    $theme_alpha_shadow = 'rgba(255, 255, 255, 0.15)';
    $theme_alpha_shadow2 = 'rgba(255, 255, 255, 0.075)';
} else {
    $theme_alpha_shadow = 'rgba(0, 0, 0, 0.15)';
    $theme_alpha_shadow2 = 'rgba(0, 0, 0, 0.075)';
}

?>
/*
<style>

    /* Chameleon theme */
    html {
        margin-left: calc(100vw - 100%);
    }

    body {
        color: <?php echo $theme_text;?>;
        margin: 0px;
        padding: 0px;
        background: <?php echo $theme_lightest; ?> url('<?php echo $theme_path ;?>images/patterns/<?php echo $pattern; ?>.png') repeat;
        background-attachment: fixed;
        font-family: 'Source Sans Pro', sans-serif;
    }

    #wrapper.container {
        background-color: <?php echo $theme_white; ?>;
        border: 1px solid<?php echo $theme_smoke_med;?>;
        border-radius: <?php echo ($config->offsetGet('rounded') ? '10px' : '0px'); ?>;
        box-shadow: 0px 10px 25px -6px <?php echo $theme_alpha_shadow;?>;
        margin: 10px auto;
    }

    .header-container {
        border-top-right-radius: <?php echo ($config->offsetGet('rounded') ? '5px' : '0px'); ?>;
        border-top-left-radius: <?php echo ($config->offsetGet('rounded') ? '5px' : '0px'); ?>;
        margin-top: 0.5rem;
        background-image: url('<?php echo $theme_path ;?>images/headers/<?php echo $header_bg; ?>');
        background-repeat: no-repeat;
        background-position: top center;
    }

    h1, h2, h3 {
        margin: 0;
        padding: 0;
        font-weight: 300;
    }

    h1 {
        font-size: 2rem;
    }

    h2 {
        font-size: 1.1rem;
    }

    a {
        color: <?php echo $theme_link;?>;
    }

    #header {
        height: 140px;
    }

    #header-usermenu ul {
        float: right;
        margin: 0px -10px 0px 0px;
        padding: 2px 20px;
        list-style: none;
        line-height: normal;
        background-color: rgba(0, 0, 0, 0.5);
    }

    #header-usermenu li {
        display: inline-block;
    }

    #header-usermenu li:not(:last-child):after {
        color: <?php echo $theme_smoke_med;?>;
        content: " | ";
    }

    #logo {
        height: 130px;
        padding: 30px 0px 0px 30px;
    }

    #logo div.site-name {
        font-size: 3rem;
    }

    #logo div.site-name a {
        color: <?php echo $theme_link;?>;
        text-decoration: none;
        text-shadow: 0px 0px 5px<?php echo $theme_white;?>;
    }

    span.site-description {
        font-weight: bold;
        text-shadow: 0px 0px 5px<?php echo $theme_white;?>;
    }

    div#seachbox {
        padding-top: 50px;
        text-align: right;
    }

    #page {
        /*padding-top: 2rem;*/
        padding-bottom: 100px;
    }

    #menu {
        overflow: hidden;
        background: <?php echo $theme_bar;?>;
        margin-bottom: 1rem;
    }

    #menu ul {
        margin: 0px 0px 0px 0px;
        padding: 0px 0px;
        list-style: none;
        line-height: normal;
    }

    #menu li {
        display: inline-block;
    }

    #menu a {
        display: block;
        padding: 0px 10px 0px 10px;
        line-height: 45px;
        text-decoration: none;
        text-transform: uppercase;
        text-align: center;
        font-size: 1rem;
        font-weight: 200;
        color: <?php echo $theme_bar_text; ?>;
        border: none;
    }

    #menu li.active a {
        text-decoration: underline;
        color: <?php echo $theme_black; ?>;
        font-weight: bold;
    }

    #menu a:hover {
        text-decoration: underline;
    }

    #menu li.active a {
    }

    #menu .last {
        border-right: none;
    }

    #breadcrumbs {
        font-size: 0.75rem;
        margin-bottom: 0.3rem;
        color: <?php echo $theme_smoke_text;?>;
    }

    #breadcrumbs span{
        font-weight: bold;
    }

    #breadcrumbs ul.breadcrumbs {
        display: inline;
        padding: 0;
        margin: 0;
    }

    #breadcrumbs ul.breadcrumbs li {
        display: inline;
    }

    #breadcrumbs ul.breadcrumbs li a {
        text-decoration: none;
        margin: 0 3px;
    }

    #breadcrumbs ul.breadcrumbs li a:hover {
        text-decoration: underline;
    }

    #breadcrumbs ul.breadcrumbs li:last-child a {
        color: inherit;
        text-decoration: none;
    }

    #breadcrumbs ul.breadcrumbs li:not(:last-child):after {
        content: " >";
        color: <?php echo $theme_smoke_text;?>;
    }


    #footer {
        text-align: center;
        font-size: 0.7rem;
    }

    #footer li {
        display: inline-block;
    }

    #footer li:not(:last-child):after {
        content: " \2022 ";
        color: <?php echo $theme_smoke_text;?>;
    }

    /*=========================================================*/
    /* Boxes */
    ul.boxes {
        padding: 0;
        margin: 0;
    }

    ul.boxes li:last-child {
        margin-bottom: 20px;
    }

    ul.boxes li {
        list-style: none;
    }

    ul.boxes ul.menu {
        padding-left: 20px ;
    }

    .box-title {
        padding: 0.5em;
        background: <?php echo $theme_bar;?>;
        color: <?php echo $theme_white;?>;
        margin: 10px 0;
    }

    /* Tables */
    td, th {
        padding: 3px 5px;
    }

    th {
        text-align: left;
        font-weight: normal;
    }

    tr.valign-top td,
    tr.valign-top th {
        vertical-align: top;
    }

    .widetable,
    .widetable2,
    .topic-table {
        width: 100%;
    }

    .widetable {
        border: 1px solid<?php echo $theme_smoke_med;?>;
    }

    .widetable td {
        padding: 6px 15px;
        width: 50%;
    }

    .widetable2 td {
        padding: 6px 10px;
    }

    /* Forms generic */
    fieldset {
        margin: 1em 0;
        padding: 8px;
        border: 1px solid<?php echo $theme_smoke_med;?>;
    }

    legend {
        padding: 0 10px;
    }

    form {
        margin: 0;
        padding: 0;
    }

    input,
    textarea,
    button,
    select {
        font-family: inherit;
        font-size: 0.875rem;
    }

    input[type=text],
    input[type=password],
    input[type=submit],
    input[type=button],
    input[type=reset],
    input[type=email],
    input[type=number],
    input[type=search],
    button,
    select {
        padding: 3px;
    }

    legend {
        color: <?php echo $theme_black;?>;
        font-weight: bold;
    }

    input[type=checkbox],
    input[type=radio] {
        vertical-align: middle;
    }

    /* Form classes */
    .inputmedium {
        width: 370px;
    }

    .inputsmall {
        width: 177px;
    }

    .areamedium {
        height: 150px;
        width: 495px;
    }

    .areasmall {
        height: 100px;
        width: 370px;
    }

    /* Search */
    .searchform input.q {
        width: 100px;
    }

    /* Lists */
    .list-title {
        margin: 15px 0 3px;
        padding: 0;
    }

    .list-title a {
        font-size: 1.1875rem;
    }

    .list-perex {
        margin: 0;
    }

    .list-perex *:last-child {
        margin-bottom: 0;
    }

    .list-perex-image {
        border: 1px solid<?php echo $theme_smoke_med;?>;
        float: left;
        margin: 6px 6px 6px 0;
        max-width: 96px;
    }

    ul.list-info {
        clear: both;
        margin: 0;
        padding: 4px 0 15px 0;
        list-style-type: none;
        font-size: 0.9rem;
        font-style: italic;
        color: <?php echo $theme_smoke_text;?>
    }

    ul.list-info li {
        display: inline;
        padding-right: 0.5em;
    }

    ul.list-info li:after {
        content: "•";
        padding-left: 0.5em;
    }

    ul.list-info li:last-child:after {
        display: none;
    }

    ul.list-info a {
        color: <?php echo $theme_smoke_text;?>;
        text-decoration: none;
    }

    ul.list-info strong {
        font-weight: normal;
    }

    /* Article */
    .article-navigation {
        border-bottom: 1px solid<?php echo $theme_smoke_med;?>;
        margin-bottom: 15px;
        padding-bottom: 10px;
    }

    .article-perex {
        color: gray;
        font-style: italic;
    }

    .article-perex-image {
        border: 1px solid<?php echo $theme_smoke_med;?>;
        float: right;
        margin: 0 8px 8px;
        max-width: 150px;
    }

    .article-footer {
        color: <?php echo $theme_smoke_text;?>;
        width: 100%;
    }

    .article-footer a {
        color: <?php echo $theme_smoke_text;?>;
        text-decoration: none;
    }

    .article-footer td {
        padding: 10px;
        vertical-align: top;
    }

    .article-info {
        margin-left: 0;
        list-style-type: none;
        font-size: 0.9rem;
    }

    .article-rating {
        border: 1px solid<?php echo $theme_smoke_med;?>;
        padding: 5px;
    }

    .article-rating td {
        padding: 0 2px;
        text-align: center;
        vertical-align: top;
    }

    .article-rating tr.r1 * {
        font-weight: bold;
    }

    /* User */
    .avatar {
        border: 1px solid<?php echo $theme_smoke_med;?>;
    }

    .profiletable .avatartd {
        padding: 5px;
    }

    .profiletable .note {
        overflow: auto;
        padding: 0 5px 5px 0;
        width: 100%;
    }

    .profiletable td {
        padding: 3px 10px;
    }

    /* User messages */
    .messages-menu a {
        padding: 0 6px;
        text-decoration: none;
    }

    .messages-menu a.active {
        font-weight: bold;
    }

    .messages-table {
        border: 1px solid<?php echo $theme_smoke_med;?>;
        width: 100%
    }

    .messages-table a {
        text-decoration: none;
    }

    .messages-table a.notread {
        font-weight: bold;
    }

    .messages-table th,
    .messages-table td {
        padding: 5px 15px;
    }

    /* Posts / comments */
    .posts {
        margin-top: 20px;
        padding-top: 10px;
        border-top: 1px solid<?php echo $theme_smoke_med;?>;
    }

    .posts-pm {
        margin-top: 0;
    }

    .posts h2 {
        margin-bottom: 10px;
    }

    .posts-form {
        padding-bottom: 10px;
    }

    .posts-form-buttons > span {
        margin-left: 10px;
    }

    .posts-form-buttons > span:first-child {
        margin-left: 0;
    }

    .posts-form-buttons a.bbcode-button {
        background: url("<?php echo $theme_path ;?>images/bbcode/button-body.png") left top no-repeat;
        /*display: inline-block;*/
        height: 16px;
        padding: 6px 4px;
        width: 16px;
    }

    .posts-form-buttons a.bbcode-button img {
        vertical-align: top;
    }

    .posts-form-buttons img {
        vertical-align: middle;
    }

    .post-list {
        border-top: 1px solid<?php echo $theme_smoke_med;?>;
    }

    .post {
        padding: 5px 5px 0 5px;
        margin: 10px 0;
        clear: both;
    }

    .post-author {
        text-decoration: none;
    }

    .post-author-guest {
        color: <?php echo $theme_smoke_text;?>;
    }

    .post-info {
        color: <?php echo $theme_smoke_text;?>;
        font-size: 0.75rem;
    }

    .post-postlink {
        color: <?php echo $theme_link;?>;
        float: right;
        font-size: 0.75rem;
        position: relative;
        right: 5px;
        text-decoration: none;
    }

    .post-actions {

    }

    .post-actions a {
        font-size: 0.75rem;
        text-decoration: none;
        margin: 0 3px;
    }

    .post-actions a:hover {
        text-decoration: underline;
    }

    .post-answer {
        margin: 20px 0 20px 50px;
    }

    .post-answer .post-body {
        border-bottom: 0;
        padding-bottom: 4px;
    }

    .post-answer .post-body-withavatar .post-body-text {
        border-left: none;
        padding-left: 3px;
    }

    .post-body {
        margin: 0;
        padding: 8px 0;
        border-bottom: 1px solid<?php echo $theme_smoke_med;?>;
    }

    .post-body-text {
        line-height: 150%;
    }

    .post-body-withavatar {
        min-height: 75px;
        padding-left: 60px;
    }

    .post-body-withavatar img.avatar {
        float: left;
        margin: 5px 0 0 -60px;
        max-width: 50px;
    }

    .post-smiley {
        vertical-align: middle;
    }

    /* BBCode */
    .bbcode-img {
        max-height: 800px;
        max-width: 400px;
    }

    /* Topic list */
    .topic-table thead th {
        background-color: <?php echo $theme_smoke; ?>;
    }

    .topic-table tr:hover {
        background-color: <?php echo $theme_lightest; ?>;
    }

    .topic-icon-cell {
        border-right: none;
        padding-right: 0;
        width: 41px;
    }

    .topic-main-cell {
        border-left: none;
        white-space: nowrap;
        width: 50%;
    }

    .topic-pages {
        margin-left: .5em;
    }

    .topic-pages a {
        background-color: <?php echo $theme_smoke_lightest;?>;
        border: 1px solid<?php echo $theme_smoke_med;?>;
        display: inline-block;
        margin: 0 2px;
        padding: 0 4px;
        text-decoration: none;
    }

    .topic-table {
        border-collapse: collapse;
    }

    .topic-table tr.topic-hl td {
        background-color: <?php echo $theme_smoke; ?>;
    }

    .topic-table tr td {
        background-color: <?php echo $theme_smoke_lightest; ?>;
    }

    .topic-table td,
    .topic-table th {
        border: 1px solid<?php echo $theme_white; ?>;
        padding: 5px;
    }

    /* Single topic */
    .topic h2 {
        margin-bottom: 0;
    }

    .topic .post {
        margin: 0;
    }

    .topic .post-body {
        border: 0;
    }

    /* Poll and shoutbox */
    .poll,
    .sbox {
        margin: 1em 0;
        border: 1px solid<?php echo $theme_smoke_med;?>;
        background-color: <?php echo $theme_smoke_lightest;?>;
    }

    .poll {
        width: 100%;
    }

    .poll-answer {
        border-top: 1px solid<?php echo $theme_smoke_med;?>;
        padding: 6px 0 5px;
    }

    .poll-answer .votebutton {
        margin: 3px 4px 2px 0;
    }

    .poll-answer div {
        background: url("<?php echo $theme_path ;?>images/system/votebar.gif") repeat-x;
        height: 11px;
        margin: 6px 2px 4px 0;
    }

    .poll-answer input {
        margin: 0;
        padding: 0;
    }

    .poll-content,
    .sbox-content {
        padding: 5px;
    }

    .poll-question,
    .sbox-title {
        padding-bottom: 5px;
        text-align: center;
    }

    .sbox form,
    .sbox table {
        margin: 0;
        padding: 0;
    }

    .sbox table {
        border-collapse: collapse;
        width: 100%;
    }

    .sbox table th {
        width: 44px;
    }

    .sbox-input {
        width: 100%;
    }

    .sbox-item {
        border-top: 1px solid<?php echo $theme_smoke_med;?>;
        padding: 10px 5px 10px 0;
    }

    .sbox-posts {
        height: 350px;
        overflow: auto;
    }

    /* Galleries */
    .gallery {
        margin: 1em 0;
        width: 100%;
    }

    .gallery img {
        border: 1px solid<?php echo $theme_smoke_med;?>;
        margin: 3px;
    }

    .gallery td {
        background-color: <?php echo $theme_lightest;?>;
        overflow: hidden;
        text-align: center;
        vertical-align: middle;
    }

    /* Paginator */
    .paging {
        margin: 1em 0;
        text-align: center;
    }

    .paging-label {
        display: none;
    }

    .paging a {
        padding: 0.2em 0.6em;
        border: 1px solid<?php echo $theme_smoke_med;?>;
        background-color: <?php echo $theme_smoke_lightest;?>;
        text-decoration: none;
    }

    .paging a.act,
    .paging a:hover {
        text-decoration: underline;
    }

    /* Security image (captcha) */
    .cimage {
        border: 1px solid<?php echo $theme_smoke_med;?>;
        height: 41px;
        margin-left: 5px;
        vertical-align: top;
        width: 240px;
    }

    .inputc {
        font-family: monospace;
        font-size: 25px;
        height: 34px;
        text-transform: uppercase;
        width: 112px;
    }

    /* System messages */
    .message {
        color: #000;
        background: #fff no-repeat 5px 5px;
        border: 1px solid<?php echo $theme_smoke_med;?>;
        font-weight: bold;
        line-height: 120%;
        margin: 1em 0;
        padding: 11px 5px 13px 48px;
    }

    .message-ok {
        background-image: url("<?php echo $theme_path ;?>images/icons/info.png");
    }

    .message-warn {
        background-image: url("<?php echo $theme_path ;?>images/icons/warning.png");
    }

    .message-err {
        background-image: url("<?php echo $theme_path ;?>images/icons/error.png");
    }

    .message ul {
        margin: 0;
        padding: 5px 0 0 15px;
    }

    /* Horizontal line */
    .hr {
        display: block;
        height: 10px;
        margin-top: 10px;
        border-top: 1px solid<?php echo $theme_smoke_med;?>;
    }

    .hr hr {
        display: none;
    }

    /* RSS */
    .rsslink {
        float: right;
        margin: 0 0 0.5em 1em;
    }

    /* Backlink */
    .backlink {
        margin: 1em 0;
    }

    /* Buttons */
    a.button, button, input[type=submit], input[type=reset] {
        display: inline-block;
        margin: 0;
        padding: 6px;
        border: 1px solid<?php echo $theme_smoke_med; ?>;
        background: <?php echo $theme_smoke_lighter; ?>;
        background: linear-gradient(to bottom, <?php echo $theme_smoke_lightest; ?>, <?php echo $theme_smoke; ?>);
        color: <?php echo $theme_text; ?>;
        vertical-align: middle;
        font-weight: normal;
        font-size: 0.8rem;
        line-height: 1;
        text-decoration: none;

    }

    a.button img.icon {
        float: left;
        margin: -1px 0 -1px 0;
        padding: 0 6px 0 0;
    }

    a.button:hover, button:hover, input[type=submit]:hover, input[type=reset]:hover {
        background: <?php echo $theme_lightest; ?>;
        background: linear-gradient(to bottom, <?php echo $theme_lightest; ?>, <?php echo $theme_lighter; ?>);
        border-color: <?php echo $theme_lighter; ?>;
        cursor: pointer;
    }

    /* Generic */
    .left {
        float: left;
        margin: 1px 10px 5px 1px;
    }

    .right {
        float: right;
        margin: 1px 1px 5px 10px;
    }

    .center {
        text-align: center;
    }

    .hidden {
        display: none;
    }

    .hint {
        color: <?php echo $theme_smoke_med;?>;
        font-size: 0.625rem;
        padding: 0 4px;
    }

    .bborder {
        padding-bottom: 0.8em;
        border-bottom: 1px solid<?php echo $theme_smoke_med;?>;
    }

    .wlimiter {
        overflow: auto;
        width: 100%;
    }

    .pre {
        display: block;
        font-family: monospace;
        white-space: nowrap;
        max-height: 300px;
        overflow: auto;
    }

    .icon {
        margin-top: -1px;
        vertical-align: middle;
    }

    a .icon, .text-icon .icon {
        padding-right: 5px;
    }

    .important {
        color: red;
    }

    .cleaner {
        clear: both;
    }

    /* === Mobile menu === */

    .hamburger-icon {
        font-size: 30px;
        cursor: pointer;
        margin: 20px;
    }

    .sidenav {
        border: 1px solid #ededed;
        height: 100%;
        width: 0;
        position: fixed;
        z-index: 10;
        top: 0;
        left: 0;
        background-color: <?php echo $theme_white; ?>;
        overflow-x: hidden;
        /*transition: 0.5s;*/
        padding-top: 35px;
        visibility: hidden;
    }

    .sidenav.visible {
        visibility: visible;
    }

    .sidenav.hidden {
        visibility: hidden;
    }

    .sidenav span.mobile-menu-title {
        position: absolute;
        top: 15px;
        font-size: 2rem;
        margin-left: 10px;
    }

    .sidenav ul {
        list-style: none !important;
    }

    .sidenav ul li {
        margin: 5px;
    }

    .sidenav .closebtn {
        position: absolute;
        top: 0;
        right: 25px;
        font-size: 3rem;
        margin-left: 50px;
        color: <?php echo $theme_black; ?>;
        text-decoration: none;
        padding-top: 4px;
    }

    /*.sidenav a.closebtn {color: #000; text-decoration: none;padding-top: 4px;}*/

    .sidenav ul li a {
        padding: 5px 5px 5px 10px;
        text-decoration: underline;
        background: none;
        color: <?php echo $theme_link;?>;
        display: block;
    }

    .sidenav ul li a:hover {
        color: <?php echo $theme_link;?>;
    }

    @media screen and (max-height: 450px) {
        .sidenav {
            padding-top: 15px;
        }
    }

    .navbar {
        display: block;
        margin-top: 10px;
        line-height: 2.5rem;
    }

    .mobile-nav {
        display: none;
        text-align: right;
    }

    /* === Tweak responsivity === */
    @media (max-width: 425px) {
        body {
            background: none;
        }

        #wrapper.container {
            background-color: <?php echo $theme_white; ?>;
            border: 1px solid<?php echo $theme_smoke_med;?>;
            border-radius: 0;
            box-shadow: none;
            margin: 0;
        }

        .header-container {
            border-radius: 0;
            margin-top: 0;
            background: none;
        }

        .mobile-nav {
            display: block;
        }

        #logo {
            height: 130px;
            padding: 20px 0px 0px 10px;
        }

        #header, #logo {
            height: auto;
        }

        #logo div.site-name {
            font-size: 1rem;
        }

        span.site-description {
            font-weight: normal;
        }

        div#header-usermenu, div#seachbox, .mainmenu-container {
            display: none;
            visibility: hidden;
        }

        ul.article-info {
            padding: 0;
        }

        /* upravy presahu */
        table.topic-table {
            width: 100%;
        }

        /* omezeni nedulezitych sloupcu fora pro mobilni zobrazeni */
        table.topic-table thead tr th:nth-child(2),
        table.topic-table tbody tr td:nth-child(3) {
            display: none;
        }

        .topic-main-cell {
            white-space: normal;
        }

        /* captcha na dalsi radek */
        img.cimage {
            display: block;
        }

        /* clanky a prispevky */
        div.list-info {
            text-align: center;
        }

        small.post-info {
            display: block;
        }

        .posts-book .post {
            background-color: #fcfcfc;
        }

        .post-author {
            font-size: 0.8rem;
        }

        .posts-book ul {
            margin: 0;
            padding: 0;
        }

        .posts-book ul li {
            list-style: none;
        }

        .article-info td {
            padding: 0px;
        }

        input.inputmedium,
        textarea.areasmall, textarea.areamedium {
            width: auto;
            height: auto;
        }
    }

    @media (max-width: 320px) {
        td.avatartd {
            display: none;
        }
    }
