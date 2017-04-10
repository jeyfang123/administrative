<?php
class Template
{
    private $templates;
    private $__vars;

    public function __construct() {
        $this->templates = array();
        $this->__vars = array();
    }

    public function render() {
        $result = "";
//      ob_start();
            foreach ($this->__vars as $k => & $v) {
                $$k = $v;
            }
            foreach ($this->templates as $template) {
                include 'default/template/' . $template;
            }
//          $result = ob_get_contents();
//      ob_get_clean();
//      echo $result;
        return $result;
    }
    public function addTemplate($templatefile) {
        if (!in_array($templatefile, $this->templates))
            $this->templates[] = $templatefile;
    }
    public function assign($name, $value) {
        $this->__vars[$name] = $value;
    }
}
?>
