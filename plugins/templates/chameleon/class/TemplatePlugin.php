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

    public function getAction(string $name): ?PluginAction
    {
        if ($name === 'config') {
            return new ConfigAction($this);
        }
        return parent::getAction($name);
    }

    protected function getConfigDefaults(): array
    {
        return [
            'menu_start' => 0,
            'menu_end' => 5,
            'active' => 0,
            'pattern' => 1,
            'pattern_counter' => 0,
            'header' => 'header_bg0.png',
            'dark_mode' => false,
            'show_breadcrumbs' => false,
            'rounded' => false,
            'random' => false,
            'show_left_sidebar' => false,
            'show_right_sidebar' => true,
            'switch_sidebars' => false,
        ];
    }
}