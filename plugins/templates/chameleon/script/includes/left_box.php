<?php

use Sunlight\Template;

if (!defined('SL_ROOT')) exit;

return _buffer(function () use ($config) { ?>
    <div id="left_sidebar" class="col-xs-12 col-md-3 first-md">
        <div class="box">
            <?= Template::boxes('left') ?>
        </div>
    </div>
<?php });