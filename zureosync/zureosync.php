<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Subclase para Zureosync, bajo la superclase de cualquier módulo de PrestaShop.
 */
class Zureosync extends Module
{
    protected $config_form = false;

    /**
     * Constructor por defecto para cualquier módulo de PrestaShop.
     */
    public function __construct()
    {
        $this->name = 'zureosync';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'Mega S.A.';
        $this->need_instance = 1;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('ZureoSync');
        $this->description = $this->l('Módulo de sincronización con los WebServices de Zureo.');
        $this->confirmUninstall = $this->l('');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Función encargada de Instalar el módulo.
     */
    public function install()
    {
        Configuration::updateValue('ZUREOSYNC_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            //$this->registerHook('actionPaymentConfirmation') &&
            $this->registerHook('displayOrderConfirmation');
    }

    /**
     * Función encargada de realizar un Handler al momento de desinstalar el módulo.
     */
    public function uninstall()
    {
        Configuration::deleteByName('ZUREOSYNC_LIVE_MODE');
        return parent::uninstall();
    }

    /**
     * Función encargada de tomar el Hook de DisplayOrderConfirmation, al momento de confirmar la orden (Indiferentemente de si la misma fue paga o no).
     */
    public function hookDisplayOrderConfirmation($params)
    {
        $url="http://megasa.zureodns.com:8811/hook.php"; //URL a la cual se va a hacer post.
        $orden = $params['order'];
        $productos=json_encode($orden->getProducts());
        $link = "$_SERVER[HTTP_HOST]";

        //Armado de array de productos
        $productosAProcesar=json_decode($productos);
        $clave = array_keys(get_object_vars($productosAProcesar))[0];
        $arrayProd = array();
        foreach ($productosAProcesar as $clave=>$valor)
        {
            array_push($arrayProd, $productosAProcesar->$clave);
        }
        $objetoOrden = [ "Orden" => $orden, "Productos" => $arrayProd ];
        $ordenAEnviar = json_encode($objetoOrden);
        
        //Debug a consola
        echo "<script>console.log('Orden: " . $orden->id . " URL: " . $link . "' );</script>";

        //Armado de CURL para enviar los datos por POST
        $curlInit = curl_init($url);
        curl_setopt($curlInit, CURLOPT_POST, true);
        curl_setopt($curlInit, CURLOPT_POSTFIELDS, $ordenAEnviar);
        curl_setopt($curlInit, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);
        $respuesta = curl_exec($curlInit);
        curl_close($curlInit);
    }
}
