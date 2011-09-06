<?php

/**
 * Description of Base_Controller_Action
 *
 * @author willianmk
 */
class Base_Controller_Action extends Zend_Controller_Action {
    protected $config;
    private $dojoScript;
    private $defaultJs;
    private $jQueryJs;

    public $moduleName;
    public $controllerName;
    public $actionName;
    public $relativeScript;
    
    /**
     * Inits the controller
     */

    public function init() {
        parent::init();

        $this->moduleName = $this->getRequest()->getModuleName();
        $this->controllerName = $this->getRequest()->getControllerName();
        $this->actionName = $this->getRequest()->getActionName();
        
        $this->relativeScript = LIB_JS . "/application/" . $this->moduleName . "/" . $this->controllerName . "/" .
                $this->actionName . ".js";
        
        $this->defaultJs = LIB_JS . "/default.js";
        $this->config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/application.ini", "production");
        
        $this->dojoScript = $this->config->layout->jslib->dojo;
        $this->jQueryJs = $this->config->layout->jslib->dojo;
        
        $arrInitCss = array(DEFAULT_MODULE => array(), 
            ADMIN_MODULE =>array($this->config->layout->csslib->dojo,
            $this->config->layout->csslib->default));
        
        $this->initAutoload();
        $this->initLayout();

        $this->view->headScript()->setAllowArbitraryAttributes(true);
        
        $arrInitJs = array(DEFAULT_MODULE => $this->jQueryJs, ADMIN_MODULE => $this->dojoScript);
        $arrInitConfig = array(DEFAULT_MODULE => null, ADMIN_MODULE => array("djConfig" => "parseOnLoad:true"));
        $this->initJs($arrInitJs[$this->moduleName], $arrInitConfig[$this->moduleName]);
        
        $this->initCss($arrInitCss[$this->moduleName]);
        //$this->initDb();
    }
    
    function initAutoload(){
        $autoload = Zend_Loader_Autoloader::getInstance();
        $autoload->setFallbackAutoloader(true);
    }
    
    function initLayout(){
        $layout = Zend_Layout::startMvc();

        $layout->setLayout("default");
        $layout->setLayoutPath(APPLICATION_PATH . "/modules/" . $this->moduleName . "/views/layout");
    }

    function initJs($module, $config) {
        $this->view->headScript()->appendFile($module, "text/javascript", $config);
        
        if (file_exists($this->relativeScript) && filesize($this->relativeScript)) {
            $this->view->headScript()->appendFile($this->relativeScript);
        }
    }

    function initCss($arrFiles) {
        foreach ($arrFiles as $css) {
            $this->view->headLink()->appendStylesheet($css);
        }
    }

    function initDb() {
        $connectionOptions = $this->config->connection;
        $dbAdapter = $connectionOptions->adapter;
        $dbOptions = $connectionOptions->db;

        $connection = Doctrine_Manager::connection($dbAdapter . "://" . $dbOptions->username . ":" . $dbOptions->password . 
                "@" . $dbOptions->host . "/" . $dbOptions->dbname, $dbOptions->connName);
        
    }
}
?>