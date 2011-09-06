<?php
/**
 * Description of DoctrineController
 *
 * @author willianmk
 */
class Admin_DoctrineController extends Base_Controller_Action {

    public function createModelsAction() {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->getHelper('layout')->disableLayout();

        $connectionName = $this->config->connection->db->connName;

        try {
            echo "<pre>Generated Models:\n";
            print_r(Doctrine_Core::generateModelsFromDb(APPLICATION_PATH . "/models", array($connectionName), array("generateTableClasses" => false)));
            echo "</pre>";
        } catch (Doctrine_Manager_Exception $e) {
            echo "Error: <br />" . $e->getMessage();
        }
    }

    public function createFormsAction() {
        $this->getHelper('layout')->disableLayout();

        $modelsPath = APPLICATION_PATH . "/models";
        $targerPath = APPLICATION_PATH . "/modules/" . $this->moduleName . "/views/forms";

        $connectionName = $this->config->connection->connName;

        $tables = Doctrine_Core::loadModels($modelsPath);
        $this->view->tables = array();
        $this->view->definitions = array();
        $this->view->forms = array();
        $definitions = array();

        foreach ($tables as $item=>$table) {
            $form = new Zend_Form();
            $form->setName("form_" . $table);
            $form->setMethod("post");
            
            $this->view->tables[] = $table;
            $doctrineTable = Doctrine_Core::getTable($table);
            $fieldNames = $doctrineTable->getFieldNames();
            
            $definitions[$table] = array();
            
            foreach ($fieldNames as $fieldName) {
                $definition = $doctrineTable->getDefinitionOf($fieldName);
                $definition["name"] = $fieldName;
                array_push($definitions[$table], $definition);

                $component = new Zend_Form_Element($fieldName);
                
                $component->setAttrib("maxlength", $definition["length"]);
                $component->setAttrib("dojoType", "dijit.form.ValidationTextBox");
                $component->setName($fieldName);
                $component->setLabel($fieldName);

                if (isset($definition["notnull"]))
                    $component->setRequired(true);

                $form->addElement($component);
            }
            $botao = new Zend_Form_Element_Button("Envia");

            $form->addElement($botao);
            $this->view->forms[] = $form->render();

            unset($form);
        }
        $this->view->definitions = $definitions;
    }

}

?>