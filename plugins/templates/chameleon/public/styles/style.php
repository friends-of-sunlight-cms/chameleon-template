<?php

use Sunlight\Core;
use Sunlight\Router;
use Sunlight\Template;
use Sunlight\Util\Color;
use Sunlight\Util\DateTime;
use SunlightTemplate\Chameleon\ColorSchemaGenerator;

// init core
require '../../../../../system/bootstrap.php';
Core::init([
    'env' => Core::ENV_WEB,
    'session_enabled' => false,
    'content_type' => 'text/css; charset=UTF-8',
]);

// get template properties
$template = Core::$pluginManager->getPlugins()->getTemplate('chameleon');
$config = $template->getConfig();
$random = $config['random'];

// don't cache in the debug
header('Expires: ' . DateTime::formatForHttp((Core::$debug || $random ? 1 : 2592000), true));

//prepare combination
if ($random) {
    $plist = $template->getPatternList();
    $pattern = pathinfo($plist[random_int(1, ($config['pattern_counter'] - 1))]['file'], PATHINFO_FILENAME);
} else {
    $pattern = $config['pattern'];
}

$header_bg = !empty($config['header_custom'])
    ? Router::path($config['header_custom'], ['absolute' => false])
    : $template->getAssetPath('images/headers/' . $config['header']);

$colorSchemaGen = new ColorSchemaGenerator(
    ($random ? random_int(0, 10) : $config['active']), // color scheme
    ($random ? (bool)random_int(0, 1) : $config['dark_mode']) // dark
);
$colorMap = $colorSchemaGen->getColorMap();
?>
/*
<style>
    /* Chameleon theme */
    html {
        margin-left: calc(100vw - 100%);
    }

    body {
        color: <?= $colorMap['theme_text']; ?>;
        background: <?= $colorMap['theme_lightest']; ?> url('<?= $template->getAssetPath('images/patterns/' . $pattern . '.png') ?>') repeat;
        background-attachment: fixed;
        font-family: 'Source Sans Pro', sans-serif;
    }

    .wrapper {
        box-sizing: border-box;
        max-width: 1200px;
    }

    section#page {
        padding: 15px 0 40px 0;
        background-color: <?= $colorMap['theme_white']; ?>;
        border-bottom-right-radius: <?= ($config['rounded'] ? '10px' : '0px'); ?>;
        border-bottom-left-radius: <?= ($config['rounded'] ? '10px' : '0px'); ?>;
        box-shadow: 0px 10px 25px -6px<?= $colorMap['theme_alpha_shadow']; ?>;
    }

    header {
        border-top-right-radius: <?= ($config['rounded'] ? '10px' : '0px'); ?>;
        border-top-left-radius: <?= ($config['rounded'] ? '10px' : '0px'); ?>;
        background-color: <?= $colorMap['theme_lighter']; ?>;
        background-image: url('<?= $header_bg; ?>'), radial-gradient(circle, <?= $colorMap['theme_bar']; ?> 0%, <?= $colorMap['theme_lighter']; ?> 60%);
        background-repeat: no-repeat;
        background-position: top center;
        background-size: cover;
        -ms-background-size: cover;
        -o-background-size: cover;
        -moz-background-size: cover;
        -webkit-background-size: cover;
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
        color: <?= $colorMap['theme_link']; ?>;
    }

    #header-usermenu a {
        color: <?= $colorMap['theme_bar_text']; ?>;
    }

    #header-usermenu ul {
        float: right;
        margin: 0;
        padding: 2px 20px;
        list-style: none;
        line-height: normal;
        min-width: 35%;
        text-align: right;
        background: <?= $colorMap['theme_bar_gradient']; ?>;
    }

    #header-usermenu li {
        display: inline-block;
    }

    #header-usermenu li:not(:last-child):after {
        color: <?= $colorMap['theme_smoke_med']; ?>;
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
        color: <?= $colorMap['theme_link']; ?>;
        text-decoration: none;
        text-shadow: 0px 0px 5px<?= $colorMap['theme_white']; ?>;
    }

    span.site-description {
        margin: 0 0 0 0.3rem;
        font-weight: bold;
        text-shadow: 0px 0px 5px<?= $colorMap['theme_white']; ?>;
    }

    div#seachbox {
        padding-top: 50px;
        text-align: center;
    }

    #page {
        padding-bottom: 100px;
    }

    #menu {
        overflow: hidden;
        background: <?= $colorMap['theme_bar']; ?>;
        /*margin-bottom: 1rem;*/
    }

    #content {
        margin: 0.3rem 0.5rem;
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
        padding: 0px 10px;
        line-height: 45px;
        text-decoration: none;
        text-transform: uppercase;
        text-align: center;
        font-size: 1rem;
        font-weight: 200;
        color: <?= $colorMap['theme_bar_text']; ?>;
        border: none;
    }

    #menu li.active a {
        text-decoration: underline;
        color: <?= $colorMap['theme_black']; ?>;
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

    ul.breadcrumbs {
        font-size: 0.75rem;
        padding: 0 0 5px 5px;
        margin: 0;
        color: <?= $colorMap['theme_smoke_text']; ?>;
        list-style-type: none;
    }

    ul.breadcrumbs li {
        display: inline;
    }

    ul.breadcrumbs li a {
        text-decoration: none;
        margin: 0 3px;
    }

    ul.breadcrumbs li a:hover {
        text-decoration: underline;
    }

    ul.breadcrumbs li:last-child a {
        /*color: inherit;*/
        color: <?= $colorMap['theme_smoke_text_darker']; ?>;
        text-decoration: none;
    }

    ul.breadcrumbs li:not(:last-child):after {
        content: " >";
        color: <?= $colorMap['theme_smoke_text']; ?>;
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
        color: <?= $colorMap['theme_smoke_text']; ?>;
    }

    /*=========================================================*/
    /* Boxes */
    ul.boxes {
        padding: 0;
        margin: 0;
    }

    ul.boxes li {
        list-style: none;
    }

    ul.boxes ul.menu {
        padding-left: 20px;
    }

    .box-title {
        padding: 0.5em;
        background: <?= $colorMap['theme_bar']; ?>;
        color: <?= $colorMap['theme_white']; ?>;
        margin: 10px 0;
    }

    /* Tables */
    td,
    th {
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
        border: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
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
        border: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
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
        color: <?= $colorMap['theme_black']; ?>;
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
        border: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
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
        color: <?= $colorMap['theme_smoke_text']; ?>
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
        color: <?= $colorMap['theme_smoke_text']; ?>;
        text-decoration: none;
    }

    ul.list-info strong {
        font-weight: normal;
    }

    /* Article */
    .article-navigation {
        border-bottom: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
        margin-bottom: 15px;
        padding-bottom: 10px;
    }

    .article-perex {
        color: gray;
        font-style: italic;
    }

    .article-perex-image {
        border: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
        float: right;
        margin: 0 8px 8px;
        max-width: 150px;
    }

    .article-footer {
        color: <?= $colorMap['theme_smoke_text']; ?>;
        width: 100%;
    }

    .article-footer a {
        color: <?= $colorMap['theme_smoke_text']; ?>;
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
        border: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
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
        border: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
        vertical-align: middle;
        border-radius: 50%;
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
        border: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
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
        border-top: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
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
        background: url("<?= $template->getAssetPath('images/bbcode/button-body.png') ?>") left top no-repeat;
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
        border-top: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
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
        color: <?= $colorMap['theme_smoke_text']; ?>;
    }

    .post-info {
        color: <?= $colorMap['theme_smoke_text']; ?>;
        font-size: 0.75rem;
    }

    .post-postlink {
        color: <?= $colorMap['theme_link']; ?>;
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
        border-bottom: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
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
        background-color: <?= $colorMap['theme_smoke']; ?>;
    }

    .topic-table tr:hover {
        background-color: <?= $colorMap['theme_lightest']; ?>;
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
        background-color: <?= $colorMap['theme_smoke_lightest']; ?>;
        border: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
        display: inline-block;
        margin: 0 2px;
        padding: 0 4px;
        text-decoration: none;
    }

    .topic-table {
        border-collapse: collapse;
    }

    .topic-table tr.topic-hl td {
        background-color: <?= $colorMap['theme_smoke']; ?>;
    }

    .topic-table tr td {
        background-color: <?= $colorMap['theme_smoke_lightest']; ?>;
    }

    .topic-table td,
    .topic-table th {
        border: 1px solid<?= $colorMap['theme_white']; ?>;
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
        border: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
        background-color: <?= $colorMap['theme_smoke_lightest']; ?>;
    }

    .poll {
        width: 100%;
    }

    .poll-answer {
        border-top: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
        padding: 6px 0 5px;
    }

    .poll-answer .votebutton {
        margin: 3px 4px 2px 0;
    }

    .poll-answer div {
        background: url("<?= $template->getAssetPath('images/system/votebar.gif') ?>") repeat-x;
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
        border-top: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
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
        border: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
        margin: 3px;
    }

    .gallery td {
        background-color: <?= $colorMap['theme_lightest']; ?>;
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
        border: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
        background-color: <?= $colorMap['theme_smoke_lightest']; ?>;
        text-decoration: none;
    }

    .paging a.act,
    .paging a:hover {
        text-decoration: underline;
    }

    /* Security image (captcha) */
    .cimage {
        border: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
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
        border: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
        font-weight: bold;
        line-height: 120%;
        margin: 1em 0;
        padding: 11px 5px 13px 48px;
    }

    .message-ok {
        background-image: url("<?= $template->getAssetPath('images/icons/info.png') ?>");
    }

    .message-warn {
        background-image: url("<?= $template->getAssetPath('images/icons/warning.png') ?>");
    }

    .message-err {
        background-image: url("<?= $template->getAssetPath('images/icons/error.png') ?>");
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
        border-top: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
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
    a.button,
    button,
    input[type=submit],
    input[type=reset] {
        display: inline-block;
        margin: 0;
        padding: 6px;
        border: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
        background: <?= $colorMap['theme_smoke_lighter']; ?>;
        background: linear-gradient(to bottom, <?= $colorMap['theme_smoke_lightest']; ?>, <?= $colorMap['theme_smoke']; ?>);
        color: <?= $colorMap['theme_text']; ?>;
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

    a.button:hover,
    button:hover,
    input[type=submit]:hover,
    input[type=reset]:hover {
        background: <?= $colorMap['theme_lightest']; ?>;
        background: linear-gradient(to bottom, <?= $colorMap['theme_lightest']; ?>, <?= $colorMap['theme_lighter']; ?>);
        border-color: <?= $colorMap['theme_lighter']; ?>;
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
        color: <?= $colorMap['theme_smoke_med']; ?>;
        font-size: 0.625rem;
        padding: 0 4px;
    }

    .bborder {
        padding-bottom: 0.8em;
        border-bottom: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
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

    a .icon,
    .text-icon .icon {
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
    }

    .sidenav {
        /*border: 1px solid #ededed;*/
        height: 100%;
        width: 0;
        position: fixed;
        z-index: 10;
        top: 0;
        left: 0;
        background-color: #1c1c1c;
        overflow-x: hidden;
        padding-top: 35px;
        visibility: hidden;
    }

    .sidenav .hr {
        border-top: 1px solid #000;
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
        color: <?= $colorMap['theme_bar']; ?>;
        text-decoration: none;
        padding-top: 4px;
    }

    /*.sidenav a.closebtn {color: #000; text-decoration: none;padding-top: 4px;}*/

    .sidenav ul li a {
        padding: 5px 5px 5px 10px;
        text-decoration: underline;
        background: none;
        color: <?= $colorMap['theme_bar']; ?>;
        display: block;
    }

    .sidenav ul li a:hover {
        color: <?= $colorMap['theme_bar']; ?>;
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
    }

    /* === Tweak responsivity === */
    @media (max-width: 425px) {
        body {
            background: none;
            margin: 0;
        }

        .wrapper {
            background-color: <?= $colorMap['theme_white']; ?>;
            border: 1px solid<?= $colorMap['theme_smoke_med']; ?>;
            border-radius: 0;
            box-shadow: none;
            margin: 0;
            padding: 0;
        }

        #hamburger-menu {
            margin: 10px;
        }

        header {
            border-radius: 0;
            margin-top: 0;
        }

        .mobile-nav {
            display: block;
        }

        #logo {
            height: 130px;
            padding: 10px;
        }

        header,
        #logo {
            height: auto;
        }

        #logo div.site-name {
            font-size: 2rem;
            margin: 0 0 0 0.5rem;
        }

        span.site-description {
            display: none;
        }

        div#header-usermenu,
        div#seachbox {
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
        textarea.areasmall,
        textarea.areamedium {
            width: auto;
            height: auto;
        }
    }

    @media (max-width: 320px) {
        td.avatartd {
            display: none;
        }
    }