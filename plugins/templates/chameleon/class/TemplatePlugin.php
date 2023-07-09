<?php

namespace SunlightTemplate\Chameleon;

use Sunlight\Plugin\Action\PluginAction;
use Sunlight\Plugin\TemplatePlugin as BaseTemplatePlugin;

/**
 * Chameleon Configurable plugin
 *
 * @author Jirka Daněk <jdanek.eu>
 */
class TemplatePlugin extends BaseTemplatePlugin
{
    public function getPatternList(): array
    {
        $list = __DIR__ . DIRECTORY_SEPARATOR . '../script/pattern_list.php';
        if (file_exists($list)) {
            return require $list;
        }
        return [];
    }
}