<?php

namespace SunlightTemplate\Chameleon;

use Sunlight\Action\ActionResult;
use Sunlight\Core;
use Sunlight\Database\Database as DB;
use Sunlight\Localization\LocalizationDirectory;
use Sunlight\Plugin\Action\ConfigAction;
use Sunlight\Plugin\Action\PluginAction;
use Sunlight\Plugin\Plugin;
use Sunlight\Plugin\PluginManager;
use Sunlight\Plugin\TemplatePlugin;
use Sunlight\Util\Form;

/**
 * Chameleon Configurable plugin
 *
 * @author Jirka Daněk <jdanek.eu>
 */
class Configurable extends TemplatePlugin
{

    public const THEME_ID = 'chameleon';

    public function __construct($data, PluginManager $manager)
    {
        // register lang for administration
        Core::$dictionary->registerSubDictionary(self::THEME_ID, new LocalizationDirectory(__DIR__ . DIRECTORY_SEPARATOR . 'Resources/languages/'));
        parent::__construct($data, $manager);
    }


    public function getPatternList(): array
    {
        $list = __DIR__ . DIRECTORY_SEPARATOR . 'Resources/pattern_list.php';
        if (file_exists($list)) {
            return require $list;
        }
        return [];
    }

    public function getAction(string $name): PluginAction
    {
        if ($name === 'config') {
            return new CustomConfig($this);
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
            'header' => 'header_bg1.png',
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

class CustomConfig extends ConfigAction
{
    /** @var $plugin Configurable */
    protected $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }

    protected function execute(): ActionResult
    {
        // automatic increment cache (enforce reload css)
        if (!Core::$debug && (isset($_POST['save']) || isset($_POST['reset']))) {
            DB::update('setting', "var=" . DB::val('cacheid'), ['val' => DB::raw('val+1')]);
        }
        return parent::execute();
    }

    protected function getFields(): array
    {
        // patterns
        $pattern_list = $this->plugin->getPatternList();
        $total = count($pattern_list);

        // headers
        $header_list = [];
        $loaded_headers = glob(__DIR__ . DIRECTORY_SEPARATOR . "images/headers/*.{jpg,png,bmp}", GLOB_BRACE);

        foreach ($loaded_headers as $file) {
            $info = pathinfo($file);
            $header_list[$info['filename']] = $info['basename'];
        }

        // colors
        $colors = [];
        for ($c = 0; $c <= 10; ++$c) {
            $colors[_lang('admin.settings.admin.adminscheme.' . $c)] = $c;
        }

        // config instance
        $cfg = $this->plugin->getConfig();

        return [
            // menu
            'menu_start' => [
                'label' => _lang(Configurable::THEME_ID . '.menu_start'),
                'input' => '<input type="number" min="0" name="config[menu_start]" value="' . $cfg->offsetGet('menu_start') . '" class="inputmini">',
                'type' => 'text'
            ],
            'menu_end' => [
                'label' => _lang(Configurable::THEME_ID . '.menu_end'),
                'input' => '<input type="number" min="0" name="config[menu_end]" value="' . $cfg->offsetGet('menu_end') . '" class="inputmini">',
                'type' => 'text'
            ],

            // theme
            'active' => [
                'label' => _lang('admin.settings.admin.adminscheme'),
                'input' => $this->createSelect('active', $colors, $cfg->offsetGet('active')),
                'type' => 'text'
            ],
            'pattern' => [
                'label' => _lang(Configurable::THEME_ID . '.pattern'),
                'input' => $this->createPatternSelect('pattern', $pattern_list, $cfg->offsetGet('pattern')),
                'type' => 'text'
            ],
            'pattern_counter' => [
                'label' => _lang(Configurable::THEME_ID . '.pattern_counter'),
                'input' => '<input type="text" name="config[pattern_counter]" value="' . $total . '" class="inputmini" readonly>',
                'type' => 'text'
            ],
            'header' => [
                'label' => _lang(Configurable::THEME_ID . '.header'),
                'input' => $this->createSelect('header', $header_list, $cfg->offsetGet('header')),
                'type' => 'text'
            ],
            'dark_mode' => [
                'label' => _lang(Configurable::THEME_ID . '.darkmode'),
                'input' => '<input type="checkbox" name="config[dark_mode]" value="1"' . Form::activateCheckbox($cfg->offsetGet('dark_mode')) . '>',
                'type' => 'checkbox'
            ],
            'rounded' => [
                'label' => _lang(Configurable::THEME_ID . '.rounded'),
                'input' => '<input type="checkbox" name="config[rounded]" value="1"' . Form::activateCheckbox($cfg->offsetGet('rounded')) . '>',
                'type' => 'checkbox'
            ],
            'show_breadcrumbs' => [
                'label' => _lang(Configurable::THEME_ID . '.show_breadcrumbs'),
                'input' => '<input type="checkbox" name="config[show_breadcrumbs]" value="1"' . Form::activateCheckbox($cfg->offsetGet('show_breadcrumbs')) . '>',
                'type' => 'checkbox'
            ],
            'random' => [
                'label' => _lang(Configurable::THEME_ID . '.random'),
                'input' => '<input type="checkbox" name="config[random]" value="1"' . Form::activateCheckbox($cfg->offsetGet('random')) . Form::disableInputUnless(Core::$debug) . '>',
                'type' => 'checkbox'
            ],

            // sidebars
            'show_left_sidebar' => [
                'label' => _lang(Configurable::THEME_ID . '.left.sidebar'),
                'input' => '<input type="checkbox" name="config[show_left_sidebar]" value="1"' . Form::activateCheckbox($cfg->offsetGet('show_left_sidebar')) . '>',
                'type' => 'checkbox'
            ],
            'show_right_sidebar' => [
                'label' => _lang(Configurable::THEME_ID . '.right.sidebar'),
                'input' => '<input type="checkbox" name="config[show_right_sidebar]" value="1"' . Form::activateCheckbox($cfg->offsetGet('show_right_sidebar')) . '>',
                'type' => 'checkbox'
            ],
            'switch_sidebars' => [
                'label' => _lang(Configurable::THEME_ID . '.switch.sidebars'),
                'input' => '<input type="checkbox" name="config[switch_sidebars]" value="1"' . Form::activateCheckbox($cfg->offsetGet('switch_sidebars')) . '>',
                'type' => 'checkbox'
            ],
        ];
    }

    private function createSelect($name, $options, $default): string
    {
        $result = "<select name='config[" . $name . "]'>";
        foreach ($options as $k => $v) {
            $result .= "<option value='" . $v . "'" . ($default == $v ? " selected" : "") . ">" . $k . "</option>";
        }
        $result .= "</select>";
        return $result;
    }

    private function createPatternSelect($name, $patterns, $default): string
    {
        $options = [];
        foreach ($patterns as $pattern) {
            $filename = pathinfo($pattern['file'], PATHINFO_FILENAME);
            $options[!$pattern['dark'] ? 'common' : 'dark'][] = "<option value='" . $filename . "'" . ($default == $filename ? " selected" : "") . ">" . (!empty($pattern['name']) ? $pattern['name'] : $filename) . "</option>\n";
        }

        $result = "<select name='config[" . $name . "]'>";
        $result .= count($options['common']) > 0 ? "<optgroup label='" . _lang(Configurable::THEME_ID . '.pattern_common') . "'>" . implode("", $options['common']) . "</optgroup>" : "";
        $result .= count($options['dark']) > 0 ? "<optgroup label='" . _lang(Configurable::THEME_ID . '.pattern_dark') . "'>" . implode("", $options['dark']) . "</optgroup>" : "";
        $result .= "</select>";
        return $result;
    }
}