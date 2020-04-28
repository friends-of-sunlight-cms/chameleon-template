<?php

namespace SunlightTemplate\Chameleon;

use Sunlight\Core;
use Sunlight\Database\Database as DB;
use Sunlight\Localization\LocalizationDirectory;
use Sunlight\Plugin\Action\ConfigAction;
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

    const THEME_ID = 'chameleon';

    function __construct(array $data, PluginManager $manager)
    {
        // register lang for administration
        Core::$lang->registerSubDictionary(self::THEME_ID, new LocalizationDirectory(__DIR__ . DIRECTORY_SEPARATOR . 'Resources/languages/'));
        parent::__construct($data, $manager);
    }


    public function getPatternList()
    {
        $list = __DIR__ . DIRECTORY_SEPARATOR . 'Resources/pattern_list.php';
        if (file_exists($list)) {
            return require $list;
        }
        return array();
    }

    public function getAction($name)
    {
        if ($name == 'config') {
            return new CustomConfig($this);
        }
        return parent::getAction($name);
    }

    protected function getConfigDefaults()
    {
        return array(
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
        );
    }
}

class CustomConfig extends ConfigAction
{
    /** @var $plugin Configurable */
    protected $plugin;

    function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }

    protected function execute()
    {
        // automatic increment cache (enforce reload css)
        if (!_debug && (isset($_POST['save']) || isset($_POST['reset']))) {
            DB::update(_setting_table, "var=" . DB::val('cacheid'), array('val' => DB::raw('val+1')));
        }
        return parent::execute();
    }

    protected function getFields()
    {
        // patterns
        $pattern_list = $this->plugin->getPatternList();
        $total = count($pattern_list);

        // headers
        $header_list = array();
        $loaded_headers = glob(__DIR__ . DIRECTORY_SEPARATOR . "images/headers/*.{jpg,png,bmp}", GLOB_BRACE);

        foreach ($loaded_headers as $file) {
            $info = pathinfo($file);
            $header_list[$info['filename']] = $info['basename'];
        }

        // colors
        $colors = array();
        for ($c = 0; $c <= 10; ++$c) {
            $colors[_lang('admin.settings.admin.adminscheme.' . $c)] = $c;
        }

        // config instance
        $cfg = $this->plugin->getConfig();

        return array(
            // menu
            'separator_menu' => array('label' => _lang(Configurable::THEME_ID . '.separator_menu'), 'input' => '', 'type' => ''),
            'menu_start' => array(
                'label' => _lang(Configurable::THEME_ID . '.menu_start'),
                'input' => '<input type="number" min="0" name="config[menu_start]" value="' . $cfg->offsetGet('menu_start') . '" class="inputmini">',
                'type' => 'text'
            ),
            'menu_end' => array(
                'label' => _lang(Configurable::THEME_ID . '.menu_end'),
                'input' => '<input type="number" min="0" name="config[menu_end]" value="' . $cfg->offsetGet('menu_end') . '" class="inputmini">',
                'type' => 'text'
            ),

            // theme
            'separator_theme' => array('label' => _lang(Configurable::THEME_ID . '.separator_theme'), 'input' => '', 'type' => ''),
            'active' => array(
                'label' => _lang('admin.settings.admin.adminscheme'),
                'input' => $this->createSelect('active', $colors, $cfg->offsetGet('active')),
                'type' => 'text'
            ),
            'pattern' => array(
                'label' => _lang(Configurable::THEME_ID . '.pattern'),
                'input' => $this->createPatternSelect('pattern', $pattern_list, $cfg->offsetGet('pattern')),
                'type' => 'text'
            ),
            'pattern_counter' => array(
                'label' => _lang(Configurable::THEME_ID . '.pattern_counter'),
                'input' => '<input type="text" name="config[pattern_counter]" value="' . $total . '" class="inputmini" readonly>',
                'type' => 'text'
            ),
            'header' => array(
                'label' => _lang(Configurable::THEME_ID . '.header'),
                'input' => $this->createSelect('header', $header_list, $cfg->offsetGet('header')),
                'type' => 'text'
            ),
            'dark_mode' => array(
                'label' => _lang(Configurable::THEME_ID . '.darkmode'),
                'input' => '<input type="checkbox" name="config[dark_mode]" value="1"' . Form::activateCheckbox($cfg->offsetGet('dark_mode')) . '>',
                'type' => 'checkbox'
            ),
            'rounded' => array(
                'label' => _lang(Configurable::THEME_ID . '.rounded'),
                'input' => '<input type="checkbox" name="config[rounded]" value="1"' . Form::activateCheckbox($cfg->offsetGet('rounded')) . '>',
                'type' => 'checkbox'
            ),
            'show_breadcrumbs' => array(
                'label' => _lang(Configurable::THEME_ID . '.show_breadcrumbs'),
                'input' => '<input type="checkbox" name="config[show_breadcrumbs]" value="1"' . Form::activateCheckbox($cfg->offsetGet('show_breadcrumbs')) . '>',
                'type' => 'checkbox'
            ),
            'random' => array(
                'label' => _lang(Configurable::THEME_ID . '.random'),
                'input' => '<input type="checkbox" name="config[random]" value="1"' . Form::activateCheckbox($cfg->offsetGet('random')) . Form::disableInputUnless(_debug) . '>',
                'type' => 'checkbox'
            ),

            // sidebars
            'separator_sidebars' => array('label' => _lang(Configurable::THEME_ID . '.separator_sidebars'), 'input' => '', 'type' => ''),
            'show_left_sidebar' => array(
                'label' => _lang(Configurable::THEME_ID . '.left.sidebar'),
                'input' => '<input type="checkbox" name="config[show_left_sidebar]" value="1"' . Form::activateCheckbox($cfg->offsetGet('show_left_sidebar')) . '>',
                'type' => 'checkbox'
            ),
            'show_right_sidebar' => array(
                'label' => _lang(Configurable::THEME_ID . '.right.sidebar'),
                'input' => '<input type="checkbox" name="config[show_right_sidebar]" value="1"' . Form::activateCheckbox($cfg->offsetGet('show_right_sidebar')) . '>',
                'type' => 'checkbox'
            ),
            'switch_sidebars' => array(
                'label' => _lang(Configurable::THEME_ID . '.switch.sidebars'),
                'input' => '<input type="checkbox" name="config[switch_sidebars]" value="1"' . Form::activateCheckbox($cfg->offsetGet('switch_sidebars')) . '>',
                'type' => 'checkbox'
            ),
        );
    }

    private function createSelect($name, $options, $default)
    {
        $result = "<select name='config[" . $name . "]'>";
        foreach ($options as $k => $v) {
            $result .= "<option value='" . $v . "'" . ($default == $v ? " selected" : "") . ">" . $k . "</option>";
        }
        $result .= "</select>";
        return $result;
    }

    private function createPatternSelect($name, $patterns, $default)
    {
        $options = array();
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