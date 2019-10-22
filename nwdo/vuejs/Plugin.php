<?php

namespace Nwdo\Vuejs;

use Event;
use Response;
use System\Classes\PluginBase;
use Xneda\B4s\Components\Fn;
use Cms\Pages\Classes\Page;
use RainLab\Pages\Classes\Page as staticPage;

class Plugin extends PluginBase {

    public function pluginDetails() {
        return [
           'name' => 'nwdo.vuejs::lang.plugin.name',
           'description' => 'nwdo.vuejs::lang.plugin.description',
           'author' => 'Xneda Team',
           'icon' => 'icon-code',
           'homepage' => 'https://github.com/avocable/octvuespa'
        ];
    }

    public function registerSettings() {
        
    }

    public function registerComponents() {
        return [
           '\Nwdo\VueJs\Components\Layout' => 'vueLayout',
        ];
    }

    public function registerMarkupTags() {
        return [
           'filters' => [
              'json' => 'json_encode',
           ]
        ];
    }

    public function boot() {


        \Event::listen('cms.page.display', function($controller, $url, $page, $result) {
            //dd($controller);
            //check if page requested by ajax and no handler
            if (
                 \Request::ajax() &&
                 $controller->getAjaxHandler() === null
            ) {

                //dd($controller);
                //current page
//                dd($url, $page);

                $assets = $controller->getAssetPaths();
                $content = "";
                //add Vue.js root div to the content
                //$content = '<!-- Page ' . $page->title . ' Start -->';
//                $content .= '<div class = "main-wrapper-inner">';
                $content .= (!empty($page->apiBag['staticPage']) ? $controller->renderPartial('static-page-partials/default') : $controller->renderPage()) ?: '<!--No content -->';
//                $content .= '</div>';
                //$content .= '<!-- Page ' . $page->title . ' End -->';


                $vueComponents = [];
                foreach ($page->components as $component) {
                    if (
                         property_exists($component, 'vueComponents') &&
                         is_array($component->vueComponents)
                    ) {

                        //dd($component->getPath());
                        $vueComponents_chunk = $component->vueComponents;
                        foreach ($vueComponents_chunk as $tag => $vcName) {
                            // full vue components options
                            if (is_integer($tag)) {
                                $tag = str_slug($vcName, "-");
                            }
                        }

                        $vueComponents = array_merge($vueComponents, [
                           $tag => $vcName
                        ]);
                    }
                }

                return [
                   'template' => $content,
                   'assets' => $assets,
                   'components' => $vueComponents,
                   'pageObj' => (isset($page->viewBag) ? $page->viewBag : null)
                ];
            }
        });
    }

}
