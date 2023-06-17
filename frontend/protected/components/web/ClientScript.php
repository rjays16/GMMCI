<?php

/**
 * ClientScript.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016,
 */

namespace SegHEIRS\components\web;

use SegHEIRS\components\AssetManager;

/**
 * Description of ClientScript
 */
class ClientScript extends \CClientScript
{
    /**
     * @var array
     */
    public $ajaxBlacklistedPackages = array(
        'jquery',
        'jquery-ui',
        'jquery-migrate',
        'bbq',
        'bootstrap.js',
    );

    /**
     * @var array
     */
    public $ajaxBlackListPatterns = array(
        //        'jquery.yiigridview.js'
    );

    /**
     *
     */
    public function init()
    {
        parent::init();
    }

    /**
     *
     */
    public function renderCoreScripts()
    {
        if (\Yii::app()->getRequest()->isAjaxRequest) {
            // Disable packages
            foreach ($this->coreScripts as $name => $package) {
                if (in_array($name, $this->ajaxBlacklistedPackages)) {
                    $this->coreScripts[$name] = false;
                }
            }

            $patterns = $this->ajaxBlackListPatterns;
            foreach ($this->scriptFiles as $position => $scripts) {
                array_walk_recursive($scripts, function (&$scriptFile) use ($patterns) {
                    if (is_string($scriptFile)) {
                        foreach ($patterns as $pattern) {
                            if ($pattern && preg_match('/' . $pattern . '/i', $scriptFile)) {
                                $scriptFile = null;
                                return;
                            }
                        }
                    }
                });

                $this->scriptFiles[$position] = array_filter($scripts);
            }
        }

        parent::renderCoreScripts();
    }

    /**
     * @param string $scriptPattern
     */
    public function ignoreOnAjaxRequest($pattern)
    {
        if (!in_array($pattern, $this->ajaxBlackListPatterns)) {
            $this->ajaxBlackListPatterns[] = $pattern;
        }
    }

    /**
     * Overrides Yii's remapScript function to inject cache-busting mechanism
     * to build files. The script first detects for the hashed version based
     * on the rev-manifest.json file. The script then
     *
     */
    protected function remapScripts()
    {
        parent::remapScripts();

        /** @var AssetManager $assetManger */
        $assetManger = \Yii::app()->getComponent('assetManager');
        $assetBaseUrl = $assetManger->getBaseUrl() . '/build/';

        $manifest = \CMap::mergeArray(
            [],
            $this->readRevManifest()
        );

        foreach ($this->scriptFiles as $position => $scripts) {
            foreach ($scripts as $url => $options) {
                if (strpos($url, $assetBaseUrl) !== false) {
                    $manifestKey = str_replace($assetBaseUrl, '', $url);
                    if (isset($manifest[$manifestKey])) {
                        unset($this->scriptFiles[$position][$url]);
                        $newUrl = $assetBaseUrl . $manifest[$manifestKey];
                        $this->scriptFiles[$position][$newUrl] = $options;
                    }
                }
            }
        }

        foreach ($this->cssFiles as $url => $screen) {
            if (strpos($url, $assetBaseUrl) !== false) {
                $manifestKey = str_replace($assetBaseUrl, '', $url);
                if (isset($manifest[$manifestKey])) {
                    $newUrl = $assetBaseUrl . $manifest[$manifestKey];
                    unset($this->cssFiles[$url]);
                    $this->cssFiles[$newUrl] = $screen;
                }
            }
        }

        $assetBaseUrl = $assetManger->getBaseUrl() . '/';
        $manifest = \CMap::mergeArray(
            [],
            $this->readBundleManifest(),
            $this->readDllManifest()
        );

        foreach ($this->scriptFiles as $position => $scripts) {
            foreach ($scripts as $url => $options) {
                if (strpos($url, $assetBaseUrl) !== false) {
                    $manifestKey = str_replace($assetBaseUrl, '', $url);
                    if (isset($manifest[$manifestKey])) {
                        unset($this->scriptFiles[$position][$url]);
                        $newUrl = $assetBaseUrl . $manifest[$manifestKey];
                        $this->scriptFiles[$position][$newUrl] = $options;
                    }
                }
            }
        }
    }

    /**
     * Parses the webpack manifest file
     * @return []
     */
    protected function readBundleManifest()
    {
        /** @var AssetManager $assetManger */
        $assetManger = \Yii::app()->getComponent('assetManager');
        $basePath = $assetManger->getBasePath() . '/bundle/';

        $manifestPath = $basePath . 'webpack-manifest.json';
        if (file_exists($manifestPath)) {
            $rawManifest = json_decode(file_get_contents($manifestPath), true);
            $manifest = [];
            if (isset($rawManifest['assetsByChunkName']) && is_array($rawManifest['assetsByChunkName'])) {
                foreach ($rawManifest['assetsByChunkName'] as $chunkName => $fileName) {
                    if (is_string($fileName)) {
                        $fileName = [$fileName];
                    }
                    array_walk($fileName, function ($value) use (&$manifest) {
                        $matches = [];
                        $hasMatch = preg_match("/(.*)\-([0-9a-f]+)\.(\w+)/", $value, $matches);
                        if ($hasMatch) {
                            $manifest["bundle/{$matches[1]}.{$matches[3]}"] = 'bundle/' . $matches[0];
                        }
                    });
                }
            }
            return $manifest;
        } else {
            return [];
        }
    }

    /**
     * Parses the webpack manifest file
     * @return []
     */
    protected function readDllManifest()
    {
        /** @var AssetManager $assetManger */
        $assetManger = \Yii::app()->getComponent('assetManager');
        $basePath = $assetManger->getBasePath() . '/bundle/dll/';
        $manifestPath = $basePath . 'webpack-dll-manifest.json';
        if (file_exists($manifestPath)) {
            $rawManifest = json_decode(file_get_contents($manifestPath), true);
            $manifest = [];
            if (isset($rawManifest['assetsByChunkName']) && is_array($rawManifest['assetsByChunkName'])) {
                foreach ($rawManifest['assetsByChunkName'] as $chunkName => $fileName) {
                    if (is_string($fileName)) {
                        $fileName = [$fileName];
                    }
                    array_walk($fileName, function ($value) use (&$manifest) {
                        $matches = [];
                        $hasMatch = preg_match("/(.*)\-([0-9a-f]+)\.(\w+)/", $value, $matches);
                        if ($hasMatch) {
                            $manifest["bundle/dll/{$matches[1]}.{$matches[3]}"] = 'bundle/dll/' . $matches[0];
                        }
                    });
                }
            }
            return $manifest;
        } else {
            return [];
        }
    }

    /**
     * @return []
     */
    public function readRevManifest()
    {
        /** @var AssetManager $assetManger */
        $assetManger = \Yii::app()->getComponent('assetManager');
        $basePath = $assetManger->getBasePath() . '/build/';

        // Read the manifest
        $manifestPath = $basePath . 'rev-manifest.json';
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
        } else {
            $manifest = [];
        }

        return $manifest;
    }

    /**
     * Same logic from parent super method but instead of mapping coreSripts,
     * this uses packages.<br><br>
     * Returns the base URL for a registered package with the specified name.
     * If needed, this method may publish the assets of the package and returns the published base URL.
     * @param string $name the package name
     * @return string the base URL for the named package. False is returned if the package is not registered yet.
     * @see registerPackage
     * @since 1.1.8
     */
    public function getPackageBaseUrl($name)
    {
        $coreScriptUrl = parent::getPackageBaseUrl($name);
        if ($coreScriptUrl !== false) {
            return $coreScriptUrl;
        }

        if (!isset($this->packages[$name]))
            return false;
        $package = $this->packages[$name];
        if (isset($package['baseUrl'])) {
            $baseUrl = $package['baseUrl'];
            if ($baseUrl === '' || $baseUrl[0] !== '/' && strpos($baseUrl, '://') === false)
                $baseUrl = \Yii::app()->getRequest()->getBaseUrl() . '/' . $baseUrl;
            $baseUrl = rtrim($baseUrl, '/');
        } elseif (isset($package['basePath']))
            $baseUrl = \Yii::app()->getAssetManager()->publish(\Yii::getPathOfAlias($package['basePath']));
        else
            $baseUrl = $this->getCoreScriptUrl();

        return $this->packages[$name]['baseUrl'] = $baseUrl;
    }
}
