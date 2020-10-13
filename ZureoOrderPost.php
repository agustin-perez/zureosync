<?php

declare(strict_types=1);

use PrestaShop\Module\ZureoOrderPost\Install\InstallerFactory;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__.'/vendor/autoload.php';

class ZureoOrderPost extends Module
{
    public function __construct()
    {
        $this->name = 'ZureoOrderPost';
        $this->author = 'PrestaShop';
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = [
            'min' => '1.7.7.0', 
            'max' => _PS_VERSION_
        ];
        parent::__construct();
        $this->displayName = $this->l('ZureoOrderPost');
        $this->description = $this->l('Módulo para envío de Ordenes a los servicios web de Zureo.');
        $this->confirmUninstall = $this->l('Está ud. seguro que quiere desinstalar?', 'ZureoOrderPost');
    }

    public function install()
    {
        if (!parent::install()
            OR !$this->registerHook('displayOrderConfirmation'))
                return false;
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall())
            return false;
        return true;
    }

    public function hookDisplayOrderConfirmation($params)
    {
        $IdOrden = $params['order']->id;
        $BaseUrl = _PS_BASE_URL_ . __PS_BASE_URI__;
        echo "<script>console.log('Orden: " . $IdOrden . " URL: " . $BaseUrl . "' );</script>";
    }

}
