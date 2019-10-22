<?php

namespace Nwdo\VueJs\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;

class Layout extends ComponentBase {

    public function componentDetails() {
        return [
           'name' => 'Layout Component',
           'description' => 'Allocate a layout for use with VueJS.',
        ];
    }

    public function defineProperties() {
        return [
           'root_element_id' => [
              'type' => 'string',
              'default' => '#app',
              'title' => 'nwdo.vuejs::layout.properties.el.title',
              'description' => 'nwdo.vuejs::layout.properties.el.description',
           ],
           'enable_routing' => [
              'type' => 'checkbox',
              'default' => false,
              'title' => 'nwdo.vuejs::layout.properties.routing.title',
              'description' => 'nwdo.vuejs::layout.properties.routing.description'
           ],
           'router_options' => [
              'type' => 'dictionary',
              'default' => [
                 "hashbang" => "true",
                 "linkActiveClass" => "v-link-active",
              ],
              'title' => 'nwdo.vuejs::layout.properties.router_options.title',
              'description' => 'nwdo.vuejs::layout.properties.router_options.description'
           ]
        ];
    }

//    public function init() {
//
//        $this->addJs('vue', 'assets/vendor/vue/vue.js');
//        if ($this->property('enable_routing')) {
//            $this->addJs('vue-router', 'assets/vendor/vue/vue-router.js');
//        }
//    }

    public function onRun() {

        if (!(
             \Request::ajax()
             )) {


            $this->addJs('assets/vendor/vue/vue.js');
            if ($this->property('enable_routing')) {
                $this->addJs('assets/vendor/vue/vue-router.js');
            }
        }


        $this->page['pages'] = Page::all();
        $this->page['withRouting'] = $this->property('enable_routing');
        $this->page['el'] = $this->property('root_element_id');
        $this->page['router_options'] = $this->property('router_options');
        $this->page['baseUrl'] = \Request::path();

        //dd($this->controller);

        $page = $this->controller->getPage();
        $vueComponents = [];
        foreach ($page->components as $component) {
            if (
                 property_exists($component, 'vueComponents') &&
                 is_array($component->vueComponents)
            ) {
                $vueComponents_chunk = $component->vueComponents;
                foreach ($vueComponents_chunk as $tag => $vcName) {
                    if (is_integer($tag)) {
                        $tag = str_slug($vcName, "-");
                    }
                }

                $vueComponents = array_merge($vueComponents, [
                   $tag => $vcName
                ]);
            }
        }

        $this->page['vueComponents'] = $vueComponents;
    }

}
