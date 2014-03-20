<?php

class BaseController {
    protected $viewPath;
    protected $includesPath;
    protected $plugin;
    protected $plugin_slug;

    function __construct() {
        $this->viewPath = dirname(__FILE__) . '/../views/';
        $this->includesPath = dirname(__FILE__) . '/../includes/';
        $this->plugin = Klasse_WP_Poll_Survey::get_instance();
        $this->plugin_slug = $this->plugin->get_plugin_slug();

    }

    /**
     * @return mixed
     */
    public function getPluginSlug()
    {
        return $this->plugin_slug;
    }


}
?>