<?php

namespace SunlightTemplate\Chameleon;

use Sunlight\Core;
use Sunlight\Localization\LocalizationDirectory;
use Sunlight\Plugin\Action\PluginAction;
use Sunlight\Plugin\PluginData;
use Sunlight\Plugin\PluginManager;
use Sunlight\Plugin\TemplatePlugin as BaseTemplatePlugin;

class TemplatePlugin extends BaseTemplatePlugin
{
    public function onBreadcrumbs(array $args): void
    {
        // render
        $output = '';
        if (!empty($args['breadcrumbs']) && (!$args['only_when_multiple'] || count($args['breadcrumbs']) >= 2) && $args['output'] === '') {
            $output .= "<ul class=\"breadcrumbs\">\n";
            foreach ($args['breadcrumbs'] as $crumb) {
                $output .= '<li><a href="' . _e($crumb['url']) . "\">{$crumb['title']}</a></li>\n";
            }
            $output .= "</ul>\n";
        }

        $args['output'] .= !empty($output)
            ? _buffer(function () use ($output) { ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <?= $output ?>
                            </div>
                        </div>
                    </div>
            <?php })
            : '&nbsp;';
    }

    public function getPatternList(): array
    {
        $list = __DIR__ . DIRECTORY_SEPARATOR . '../script/pattern_list.php';
        if (file_exists($list)) {
            return require $list;
        }
        return [];
    }
}