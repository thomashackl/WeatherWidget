<?php

/**
 * WeatherWidgetPlugin.class.php
 *
 * Widget that shows you the Weather
 *
 * @author  Florian Bieringer <florian.bieringer@uni-passau.de>
 * @version 1.0
 * @author Anna Kirpichnikova <a.kirpichnikova@stud.hs-wismar.de> and Jakob Diel <jakob.diel@hs-wismar.de>
 * @version 1.1
 */
class WeatherWidgetPlugin extends StudIPPlugin implements PortalPlugin {

    const APIKEY = '02f7c0bdaae418cfad0f061298b3f8c3';
    const URL = "http://api.openweathermap.org/data/2.5/weather?q=";
    const FORECAST_URL = "http://api.openweathermap.org/data/2.5/forecast/daily?q=";
    const LOCATION = "wismar";
    const LANGUAGE = "&lang=";
    const CACHENAME = "plugin/weatherwidget";

    public static $weather;
    public static $forecast;

            public function __construct()
    {
        parent::__construct();
        bindtextdomain('wetter', __DIR__ . '/locale');
    }

    public function getWeather() {
        // Dynamische Sprachwahl
        $locale = (function_exists('get_locale')) ? get_locale() : ($_SESSION['_language'] ?? 'de_DE');
        $lang = (strpos($_SESSION['_language'] ?? 'de_DE', 'de') === 0) ? 'de' : 'en';

        // Check class cache
        if (self::$weather != null) {
            return json_decode(self::$weather);
        }

        // Check application cache
        $cache = StudipCacheFactory::getCache();
        $cacheKey = self::CACHENAME . "/current_" . $lang;
        if (!$data = $cache->read($cacheKey)) {
            ini_set('default_socket_timeout', 2);
            $url = self::URL . self::LOCATION . self::LANGUAGE . $lang . '&APPID=' . self::APIKEY;
            $handle = fopen($url, "r");
            if ($handle) {
                $data = fgets($handle);
            }
            if ($data) {
                $cache->write($cacheKey, $data, 300);
                self::$weather = $data;
            }
        }
        return json_decode($data);
    }

    public function getForecast() {
        bindtextdomain('wetter', $this->getPluginPath()."/locale");

        // Dynamische Sprachwahl
        $locale = (function_exists('get_locale')) ? get_locale() : ($_SESSION['_language'] ?? 'de_DE');
        $lang = (strpos($_SESSION['_language'] ?? 'de_DE', 'de') === 0) ? 'de' : 'en';

        // Check class cache
        if (self::$forecast != null) {
            return json_decode(self::$forecast);
        }

        // Check application cache
        $cache = StudipCacheFactory::getCache();
        $cacheKey = self::CACHENAME . "/forecast_" . $lang;
        if (!$data = $cache->read($cacheKey)) {
            ini_set('default_socket_timeout', 2);
            $url = self::FORECAST_URL . self::LOCATION . self::LANGUAGE . $lang . '&APPID=' . self::APIKEY;
            $handle = fopen($url, "r");
            if ($handle) {
                $data = fgets($handle);
            }
            if ($data) {
                $cache->write($cacheKey, $data, 300);
                self::$weather = $data;
            }
        }
        return json_decode($data);
    }

    public function getPluginName() {
        return dgettext('wetter','Wetter in Wismar')/*self::getWeather()->name*/;
    }

    public function getPortalTemplate() {
        $templatefactory = new Flexi_TemplateFactory(__DIR__ . "/templates");
        $template = $templatefactory->open("index.php");

        $template->set_attribute("data", self::getWeather());
        $template->set_attribute("forecast", self::getForecast());
        $template->set_attribute("iconUrl", $this->getPluginURL() . '/icons/');
        return $template;
    }
    public function getMetadata()
    {
        $metadata = parent::getMetadata();
        $metadata['pluginname'] = dgettext('wetter', "Wetter in Wismar");
        $metadata['displayname'] = dgettext('wetter', "Wetter in Wismar");
        $metadata['descriptionlong'] = dgettext('wetter', "Dieses Widget zeigt das aktuelle Wetter sowie eine Wettervorhersage für die nächsten Tage in Wismar an.");
        $metadata['summary'] = dgettext('wetter', "Wetter in Wismar.");
        return $metadata;   
    }
}
