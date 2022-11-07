<?php
namespace TM;

class Controller
{
    private $replace = [];

    public function __construct(array $options = [])
    {
        foreach($options as $key => $value){
            $this->replace[$key] = $value;
        }
    }

    private function setTpl($file_name)
    {
        $tpl = SERVER_DIR['HTML'].$file_name.TPL_EXT;
        if(!file_exists($tpl)){
            $tpl = SERVER_DIR['HTML'].'404'.TPL_EXT;
        }
        $this->replace['add_script'] = [];
        $js = str_replace('/', '_', $file_name);
        if(file_exists(SERVER_DIR['JS'].$js.'.js') && $file_name != 404){
            array_push($this->replace['add_script'], $this->replace['PUBLIC_URL']['JS'].$js.'.js');
        }
        $this->replace['tpl'] = $tpl;
    }

    private function resourceReplace($url)
    {
        $source = file_get_contents($url);
        if($source){
            preg_match_all('/\{.*?\}/u', $source, $matches);
            if($matches[0]){
                foreach($matches[0] as $m){
                    $val = ltrim($m, '{');
                    $val = rtrim($val, '}');
                    $val = explode(',', $val);
                    $this->replace['callFunction'][$val[0]] = $val[1] ?? '';
                }
            }
        }
    }

    public function api()
    {
        $method = $this->replace['method'];
        if($method && $this->replace['type'] && $this->replace['options']){
            (new Api($this->replace))->$method();
        }
    }

    public function _404()
    {
        $this->setTpl('404');
        $this->resourceReplace($this->replace['tpl']);
        $this->replace = (new Model($this->replace))->index();
        (new View($this->replace))->display();
    }

    public function index()
    {
        try {
            $method = $this->replace['method'];
            $this->setTpl($method);
            $this->resourceReplace($this->replace['tpl']);
            $this->replace = (new Model($this->replace))->$method();
            if($this->replace['status_code'] != 200){
                $this->_404();
            }
            (new View($this->replace))->display();
        } catch (\Throwable $e) {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location:" . $this->replace['PUBLIC_URL']['URL'].'install');
            exit;
        }
    }

    public function category()
    {
        try {
            $method = $this->replace['method'];
            $this->setTpl($method);
            $this->resourceReplace($this->replace['tpl']);
            $this->replace = (new Model($this->replace))->$method();
            if($this->replace['status_code'] != 200){
                $this->_404();
            }
            (new View($this->replace))->display();
        } catch (\Throwable $e) {
            echo $e->getMessage();
        }
    }

    public function content()
    {
        try {
            $method = $this->replace['method'];
            $this->setTpl($method);
            $this->resourceReplace($this->replace['tpl']);
            $this->replace = (new Model($this->replace))->$method();
            if($this->replace['status_code'] != 200){
                $this->_404();
            }
            (new View($this->replace))->display();
        } catch (\Throwable $e) {
            echo $e->getMessage();
        }
    }

    public function admin()
    {
        try {
            $page = $this->replace['method'] ?? 'index';
            $sub = '';
            $this->replace['id'] = null;
            if(isset($this->replace['category_page']) && $this->replace['category_page']){
                $sub = '_'.$this->replace['category_page'][0];
                $this->replace['id'] = $this->replace['category_page'][1] ?? null;
            }
            $this->replace['category_page'] = $page;
            $method = 'admin_'.$page.$sub;
            $this->setTpl('admin/'.$page.$sub);
            $this->resourceReplace($this->replace['tpl']); 
            
            $this->replace = (new Model($this->replace))->$method();
            
            if($this->replace['status_code'] != 200){
                $this->_404();
            }
            (new View($this->replace))->display();
        } catch (\Throwable $e) {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location:" . $this->replace['PUBLIC_URL']['URL'].'install');
            exit;
        }
    }
}
