<?php

namespace SunlightTemplate\Chameleon;

use Sunlight\Action\ActionResult;
use Sunlight\Core;
use Sunlight\Database\Database as DB;
use Sunlight\Localization\LocalizationDirectory;
use Sunlight\Plugin\Action\ConfigAction as BaseConfigAction;
use Sunlight\Plugin\Plugin;
use Sunlight\Util\Form;

class ConfigAction extends BaseConfigAction
{
    public const THEME_ID = 'chameleon';
    
    /** @var $plugin TemplatePlugin */
    protected $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
        // register lang for administration
        Core::$dictionary->registerSubDictionary(self::THEME_ID, new LocalizationDirectory(
            __DIR__ . DIRECTORY_SEPARATOR . '../lang/'
        ));
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
        $loaded_headers = glob(__DIR__ . DIRECTORY_SEPARATOR . "../images/headers/*.{jpg,png,bmp}", GLOB_BRACE);

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
        $config = $this->plugin->getConfig();

        return [
            // menu
            'menu_start' => [
                'label' => _lang(self::THEME_ID . '.menu_start'),
                'input' => '<input type="number" min="0" name="config[menu_start]" value="' . $config['menu_start'] . '" class="inputmini">',
                'type' => 'text'
            ],
            'menu_end' => [
                'label' => _lang(self::THEME_ID . '.menu_end'),
                'input' => '<input type="number" min="0" name="config[menu_end]" value="' . $config['menu_end'] . '" class="inputmini">',
                'type' => 'text'
            ],

            // theme
            'active' => [
                'label' => _lang('admin.settings.admin.adminscheme'),
                'input' => $this->createSelect('active', $colors, $config['active']),
                'type' => 'text'
            ],
            'pattern' => [
                'label' => _lang(self::THEME_ID . '.pattern'),
                'input' => $this->createPatternSelect('pattern', $pattern_list, $config['pattern']),
                'type' => 'text'
            ],
            'pattern_counter' => [
                'label' => _lang(self::THEME_ID . '.pattern_counter'),
                'input' => '<input type="text" name="config[pattern_counter]" value="' . $total . '" class="inputmini" readonly>',
                'type' => 'text'
            ],
            'header' => [
                'label' => _lang(self::THEME_ID . '.header'),
                'input' => $this->createSelect('header', $header_list, $config['header']),
                'type' => 'text'
            ],
            'dark_mode' => [
                'label' => _lang(self::THEME_ID . '.darkmode'),
                'input' => '<input type="checkbox" name="config[dark_mode]" value="1"' . Form::activateCheckbox($config['dark_mode']) . '>',
                'type' => 'checkbox'
            ],
            'rounded' => [
                'label' => _lang(self::THEME_ID . '.rounded'),
                'input' => '<input type="checkbox" name="config[rounded]" value="1"' . Form::activateCheckbox($config['rounded']) . '>',
                'type' => 'checkbox'
            ],
            'show_breadcrumbs' => [
                'label' => _lang(self::THEME_ID . '.show_breadcrumbs'),
                'input' => '<input type="checkbox" name="config[show_breadcrumbs]" value="1"' . Form::activateCheckbox($config['show_breadcrumbs']) . '>',
                'type' => 'checkbox'
            ],
            'random' => [
                'label' => _lang(self::THEME_ID . '.random'),
                'input' => '<input type="checkbox" name="config[random]" value="1"' . Form::activateCheckbox($config['random']) . Form::disableInputUnless(Core::$debug) . '>',
                'type' => 'checkbox'
            ],

            // sidebars
            'show_left_sidebar' => [
                'label' => _lang(self::THEME_ID . '.left.sidebar'),
                'input' => '<input type="checkbox" name="config[show_left_sidebar]" value="1"' . Form::activateCheckbox($config['show_left_sidebar']) . '>',
                'type' => 'checkbox'
            ],
            'show_right_sidebar' => [
                'label' => _lang(self::THEME_ID . '.right.sidebar'),
                'input' => '<input type="checkbox" name="config[show_right_sidebar]" value="1"' . Form::activateCheckbox($config['show_right_sidebar']) . '>',
                'type' => 'checkbox'
            ],
            'switch_sidebars' => [
                'label' => _lang(self::THEME_ID . '.switch.sidebars'),
                'input' => '<input type="checkbox" name="config[switch_sidebars]" value="1"' . Form::activateCheckbox($config['switch_sidebars']) . '>',
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
        $result .= count($options['common']) > 0 ? "<optgroup label='" . _lang(self::THEME_ID . '.pattern_common') . "'>" . implode("", $options['common']) . "</optgroup>" : "";
        $result .= count($options['dark']) > 0 ? "<optgroup label='" . _lang(self::THEME_ID . '.pattern_dark') . "'>" . implode("", $options['dark']) . "</optgroup>" : "";
        $result .= "</select>";
        return $result;
    }
}