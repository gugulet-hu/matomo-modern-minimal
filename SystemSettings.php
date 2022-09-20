<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\Modern;

use Piwik\Container\StaticContainer;
use Piwik\Settings\Setting;
use Piwik\Settings\FieldConfig;

/**
 * Defines Settings for Modern.
 */
class SystemSettings extends \Piwik\Settings\Plugin\SystemSettings
{
    /** @var Setting */
    public $modernDarkMode;

    // /** @var Setting */
    public $modernHeaderBackgroundColor;

    protected function init()
    {
        $this->modernDarkMode = $this->createModernModernSetting();
        // $this->modernDarkMode->setIsWritableByCurrentUser(false);
        $this->modernHeaderBackgroundColor = $this->createModernHeaderBackgroundColorSetting();
        // $this->modernHeaderBackgroundColor->setIsWritableByCurrentUser(false);
    }

    private function createModernModernSetting()
    {
        return $this->makeSetting('modernDarkMode', 0, FieldConfig::TYPE_INT, function (FieldConfig $field) {
            $field->title = 'Dark mode';
            $field->uiControl = FieldConfig::UI_CONTROL_RADIO;
            $field->description = 'You can select the Dark or Light appearance as a default option, or make it change automatically with sunset (Light ⇢ Dark) and sunrise (Dark ⇢ Light)';
            $field->availableValues = array('0' => 'Automatic',
                                            '1' => 'Dark theme',
                                            '2' => 'Light theme');
            $field->validate = function ($value, $setting) {
                // Clear cache
                $cache = StaticContainer::get('Matomo\Cache\Transient');
                $cache->delete('cssCacheBusterId');
            };
        });
    }

    private function createModernHeaderBackgroundColorSetting()
    {
        return $this->makeSetting('modernHeaderBackgroundColor', '', FieldConfig::TYPE_STRING, function (FieldConfig $field) {
            $field->title = 'Header background color';
            $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
            $field->description = 'Define custom header background color. HEX, rgb, rgba, hsl and hsla colors are allowed.';
            $field->validate = function ($value, $setting) {
                if($value !== "") {
                    preg_match('/^(#(?:[0-9a-f]{2}){2,4}|#[0-9a-f]{3}|(?:rgba?|hsla?)\((?:\d+%?(?:deg|rad|grad|turn)?(?:,|\s)+){2,3}[\s\/]*[\d\.]+%?\))$/', $value, $match);
                    if (empty($match)) {
                        throw new \Exception("Value $value is invalid");
                    }
                }

                // Clear cache
                $cache = StaticContainer::get('Matomo\Cache\Transient');
                $cache->delete('cssCacheBusterId');
            };
        });
    }
}
