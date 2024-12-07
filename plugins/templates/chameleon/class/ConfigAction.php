<?php

namespace SunlightTemplate\Chameleon;

use Sunlight\Action\ActionResult;
use Sunlight\Core;
use Sunlight\Database\Database as DB;
use Sunlight\Localization\LocalizationDirectory;
use Sunlight\Plugin\Action\ConfigAction as BaseConfigAction;
use Sunlight\Plugin\Plugin;
use Sunlight\Util\Form;
use Sunlight\Util\Request;
use Sunlight\Util\SelectOption;

class ConfigAction extends BaseConfigAction
{
    public const THEME_ID = 'chameleon';

    /** @var $plugin TemplatePlugin */
    protected $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
        // register lang for config
        Core::$dictionary->registerSubDictionary(
            'chameleon',
            new LocalizationDirectory(
                __DIR__ . DIRECTORY_SEPARATOR . '../lang/'
            )
        );
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
        $loaded_patterns = $this->plugin->getPatternList();
        $total_patterns = count($loaded_patterns);

        // headers
        $header_list = [];
        $loaded_headers = glob(__DIR__ . DIRECTORY_SEPARATOR . "../images/headers/*.{jpg,png,bmp}", GLOB_BRACE);
        foreach ($loaded_headers as $file) {
            $info = pathinfo($file);
            $header_list[$info['basename']] = $info['filename'];
        }

        // patterns
        $pattern_list = [];
        foreach ($loaded_patterns as $pattern) {
            $filename = pathinfo($pattern['file'], PATHINFO_FILENAME);
            $pattern_list[_lang(self::THEME_ID . '.pattern_' . (!$pattern['dark'] ? 'common' : 'dark'))][] = new SelectOption($filename, (!empty($pattern['name']) ? $pattern['name'] : $filename));
        }

        // colors
        $colors = [];
        for ($c = 0; $c <= 10; ++$c) {
            $colors[$c] = _lang('admin.settings.admin.adminscheme.' . $c);
        }

        // config instance
        $config = $this->plugin->getConfig();

        return [
            // menu
            'menu_start' => [
                'label' => _lang(self::THEME_ID . '.menu_start'),
                'input' => Form::input('number', 'config[menu_start]', Request::post('config[menu_start]', $config['menu_start']), [
                    'class' => 'inputmini',
                    'min' => 0,
                ]),
                'type' => 'text',
            ],
            'menu_end' => [
                'label' => _lang(self::THEME_ID . '.menu_end'),
                'input' => Form::input('number', 'config[menu_end]', Request::post('config[menu_end]', $config['menu_end']), [
                    'class' => 'inputmini',
                    'min' => 0,
                ]),
                'type' => 'text',
            ],

            // theme
            'active' => [
                'label' => _lang('admin.settings.admin.adminscheme'),
                'input' => Form::select('config[active]', $colors, $config['active']),
                'type' => 'text',
            ],
            'pattern' => [
                'label' => _lang(self::THEME_ID . '.pattern'),
                'input' => Form::select('config[pattern]', $pattern_list, $config['pattern']),
                'type' => 'text',
            ],
            'pattern_counter' => [
                'label' => _lang(self::THEME_ID . '.pattern_counter'),
                'input' => Form::input('text', 'config[pattern_counter]', $total_patterns, ['class' => 'inputmini', 'readonly' => true]),
                'type' => 'text',
            ],
            'header' => [
                'label' => _lang(self::THEME_ID . '.header'),
                'input' => Form::select('config[header]', $header_list, $config['header']),
                'type' => 'text',
            ],
            'header_custom' => [
                'label' => _lang(self::THEME_ID . '.header_custom')
                    . '<br><small>(1240px x 240px)</small>',
                'input' => Form::input('text', 'config[header_custom]', Request::post('header_custom', $config['header_custom']), [
                    'class' => 'inputmedium',
                    'placeholder' => 'upload/...',
                ]),
                'type' => 'text',
            ],
            'dark_mode' => [
                'label' => _lang(self::THEME_ID . '.darkmode'),
                'input' => Form::input('checkbox', 'config[dark_mode]', '1', [
                    'checked' => Request::post('config[dark_mode]', $config['dark_mode']),
                ]),
                'type' => 'checkbox',
            ],
            'rounded' => [
                'label' => _lang(self::THEME_ID . '.rounded'),
                'input' => Form::input('checkbox', 'config[rounded]', '1', [
                    'checked' => Request::post('config[rounded]', $config['rounded']),
                ]),
                'type' => 'checkbox',
            ],
            'show_breadcrumbs' => [
                'label' => _lang(self::THEME_ID . '.show_breadcrumbs'),
                'input' => Form::input('checkbox', 'config[show_breadcrumbs]', '1', [
                    'checked' => Request::post('config[show_breadcrumbs]', $config['show_breadcrumbs']),
                ]),
                'type' => 'checkbox',
            ],
            'random' => [
                'label' => _lang(self::THEME_ID . '.random'),
                'input' => Form::input('checkbox', 'config[random]', '1', [
                    'checked' => Request::post('config[random]', $config['random']),
                    'disabled' => !Core::$debug,
                ]),
                'type' => 'checkbox',
            ],

            // sidebars
            'show_left_sidebar' => [
                'label' => _lang(self::THEME_ID . '.left.sidebar'),
                'input' => Form::input('checkbox', 'config[show_left_sidebar]', '1', [
                    'checked' => Request::post('config[show_left_sidebar]', $config['show_left_sidebar']),
                ]),
                'type' => 'checkbox',
            ],
            'show_right_sidebar' => [
                'label' => _lang(self::THEME_ID . '.right.sidebar'),
                'input' => Form::input('checkbox', 'config[show_right_sidebar]', '1', [
                    'checked' => Request::post('config[show_right_sidebar]', $config['show_right_sidebar']),
                ]),
                'type' => 'checkbox',
            ],
        ];
    }
}