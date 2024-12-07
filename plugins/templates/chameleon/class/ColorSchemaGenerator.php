<?php

namespace SunlightTemplate\Chameleon;

use Sunlight\Util\Color;

class ColorSchemaGenerator
{
    /** @var int */
    private $schemaNumber;
    /** @var bool */
    private $dark;

    private $hue = 0;
    private $light = 127;
    private $sat = 255;

    private $colorMap = [];

    /**
     * @param int $schemaNumber 0-10
     */
    public function __construct(int $schemaNumber, bool $dark = false)
    {
        $this->schemaNumber = $schemaNumber;
        $this->dark = $dark;

        // vychozi barevne hodnoty
        $this->colorMap['theme_link'] = null;
        $this->colorMap['theme_bar_text'] = null;
        $this->colorMap['theme_bar_shadow'] = null;
        $this->colorMap['theme_bar_flip'] = false;
        if ($this->dark) {
            $this->colorMap['theme_white'] = '#000';
            $this->colorMap['theme_black'] = '#fff';
            $this->colorMap['theme_bg_info'] = '#00626A';
            $this->colorMap['theme_bg_alert'] = '#845100';
            $this->colorMap['theme_bg_danger'] = '#840000';
        } else {
            $this->colorMap['theme_white'] = '#fff';
            $this->colorMap['theme_black'] = '#000';
            $this->colorMap['theme_bg_info'] = '#D0EDEE';
            $this->colorMap['theme_bg_alert'] = '#FFD183';
            $this->colorMap['theme_bg_danger'] = '#FFA7A7';
        }
        $this->colorMap['theme_bar_loff'] = 30;
        $this->colorMap['theme_text'] = $this->colorMap['theme_black'];
        if ($this->dark) {
            $this->colorMap['theme_contrast'] = $this->colorMap['theme_black'];
            $this->colorMap['theme_contrast2'] = $this->colorMap['theme_white'];
        } else {
            $this->colorMap['theme_contrast'] = $this->colorMap['theme_white'];
            $this->colorMap['theme_contrast2'] = $this->colorMap['theme_black'];
        }
        $this->colorMap['theme_link_loff'] = ($this->dark ? -20 : -10);
        $this->colorMap['dark_suffix'] = ($this->dark ? '_dark' : '');
    }

    public function getColorMap(): array
    {
        $this->modifySchemaColors($this->schemaNumber);

        $this->colorMap['theme'] = $this->calculateColor(($this->dark ? 40 : 0))->getRgbStr();
        $this->colorMap['theme_lighter'] = $this->calculateColor(80)->getRgbStr();
        $this->colorMap['theme_lightest'] = $this->calculateColor(100)->getRgbStr();
        $this->colorMap['theme_smoke'] = $this->calculateColor(115, 0)->getRgbStr();
        $this->colorMap['theme_smoke_text'] = $this->calculateColor($this->light * 0.2, 0)->getRgbStr();
        $this->colorMap['theme_smoke_text_dark'] = $this->calculateColor(10, 0)->getRgbStr();
        $this->colorMap['theme_smoke_text_darker'] = $this->calculateColor(-30, 0)->getRgbStr();
        $this->colorMap['theme_smoke'] = $this->calculateColor(110, 0)->getRgbStr();
        $this->colorMap['theme_smoke_med'] = $this->calculateColor(90, 0)->getRgbStr();
        $this->colorMap['theme_smoke_dark'] = $this->calculateColor(60, 0)->getRgbStr();
        $this->colorMap['theme_smoke_darker'] = $this->calculateColor($this->dark ? -20 : -10, 0)->getRgbStr();
        $this->colorMap['theme_smoke_light'] = $this->calculateColor(110, 0)->getRgbStr();
        $this->colorMap['theme_smoke_lighter'] = $this->calculateColor(118, 0)->getRgbStr();
        $this->colorMap['theme_smoke_lightest'] = $this->calculateColor(125, 0)->getRgbStr();
        $this->colorMap['theme_smoke_lightest_colored'] = $this->calculateColor(125)->getRgbStr();
        $this->colorMap['theme_med'] = $this->calculateColor(30)->getRgbStr();
        $this->colorMap['theme_dark'] = $this->calculateColor(-10)->getRgbStr();

        $themeBar = $this->calculateColor($this->colorMap['theme_bar_loff']);
        $this->colorMap['theme_bar'] = $themeBar->getRgbStr();
        $rbg = $themeBar->getRgb();
        $this->colorMap['theme_bar_gradient'] = 'linear-gradient(to right, rgba(' . $rbg[0] . ',' . $rbg[1] . ',' . $rbg[2] . ',0), rgba(' . $rbg[0] . ',' . $rbg[1] . ',' . $rbg[2] . ',1), rgba(' . $rbg[0] . ',' . $rbg[1] . ',' . $rbg[2] . ',0));';

        if ($this->colorMap['theme_link'] == null) {
            $this->colorMap['theme_link'] = $this->calculateColor($this->colorMap['theme_link_loff'], 255, true);
        }
        if ($this->colorMap['theme_bar_shadow'] === null) {
            $this->colorMap['theme_bar_shadow'] = ($this->colorMap['theme_bar_flip'] ? 'rgba(255, 255, 255, 0.3)' : 'rgba(0, 0, 0, 0.3)');
        }
        if ($this->dark) {
            $this->colorMap['theme_bar_flip'] = !$this->colorMap['theme_bar_flip'];
        }
        if ($this->colorMap['theme_bar_text'] === null) {
            $this->colorMap['theme_bar_text'] = ($this->colorMap['theme_bar_flip'] ? $this->colorMap['theme_black'] : $this->colorMap['theme_white']);
        }
        if ($this->dark) {
            $this->colorMap['theme_alpha_shadow'] = 'rgba(255, 255, 255, 0.15)';
            $this->colorMap['theme_alpha_shadow2'] = 'rgba(255, 255, 255, 0.075)';
        } else {
            $this->colorMap['theme_alpha_shadow'] = 'rgba(0, 0, 0, 0.15)';
            $this->colorMap['theme_alpha_shadow2'] = 'rgba(0, 0, 0, 0.075)';
        }

        return $this->colorMap;
    }

    private function modifySchemaColors(int $color_schema): void
    {
        // uprava podle schematu
        switch ($color_schema) {
            // modry
            case 1:
                $this->hue = 145;
                $this->sat -= 10;
                break;

            // zeleny
            case 2:
                $this->hue = 70;
                if (!$this->dark) {
                    $this->light -= 20;
                }
                $this->sat *= 0.7;
                break;

            // cerveny
            case 3:
                $this->hue = 5;
                if (!$this->dark) {
                    $this->light -= 10;
                }
                break;

            // zluty
            case 4:
                $this->hue = 35;
                $this->colorMap['theme_contrast'] = $this->colorMap['theme_black'];
                $this->colorMap['theme_link'] = '#BE9B02';
                if (!$this->dark) {
                    $this->light -= 20;
                    $this->colorMap['theme_bar_flip'] = true;
                } else {
                    $this->light += 5;
                }
                break;

            // purpurovy
            case 5:
                $this->hue = 205;
                break;

            // azurovy
            case 6:
                $this->hue = 128;
                if (!$this->dark) {
                    $this->light -= 10;
                    $this->sat -= 70;
                    $this->colorMap['theme_link_loff'] -= 10;
                    $this->colorMap['theme_bar_flip'] = true;
                }
                break;

            // fialovy
            case 7:
                $this->hue = 195;
                if ($this->dark) {
                    $this->light += 10;
                }
                break;

            // hnedy
            case 8:
                $this->hue = 20;
                $this->light -= 10;
                $this->sat *= 0.6;
                break;

            // tmave modry
            case 9:
                $this->hue = 170;
                if ($this->dark) {
                    $this->colorMap['theme_link_loff'] -= 20;
                }
                $this->sat *= 0.5;
                break;

            // sedy
            case 10:
                $this->hue = 150;
                $this->sat = 0;
                $this->colorMap['theme_link'] = '#67939F';
                $this->colorMap['theme_bar_loff'] = 50;
                if (!$this->dark) {
                    $this->colorMap['theme_bar_flip'] = true;
                }
                break;

            // oranzovy
            default:
                $this->hue = 14;
                $this->colorMap['theme_link'] = '#F84A00';
                $this->light -= 10;
                break;
        }
    }


    private function calculateColor($loff = 0, $satc = null, bool $sat_abs = false, bool $light_abs = false): Color
    {
        // nacteni a uprava barev
        if ($satc === 0) {
            $light_abs = true;
            $loff += 127;
        }
        $h = $this->hue;
        if ($this->dark) {
            $l = ($light_abs ? 255 - $loff : $this->light - $loff);
        } else {
            $l = ($light_abs ? $loff : $this->light + $loff);
        }
        $s = (isset($satc) ? ($sat_abs ? $satc : $this->sat * $satc) : $this->sat);

        return new Color([$h, $s, $l], 1);
    }
}