<?php
class Controller extends Object
{
		protected $params = array();
        protected $layout = 'default';
        protected $generateCache = false;
		public $action = '';
        
 /**
         *
         * @param Array $get_vars
         * @param Array $post_vars
         * @param Array $url_vars 
         */
        public function setRequest($get_vars, $post_vars, $url_vars, $vars_args)
        {
        	$this->params['get'] = $get_vars;
        	$this->params['post'] = $post_vars;
        	$this->params['url'] = $url_vars;
            $this->params['args'] = $vars_args;
        }
        
        public function params($type)
        {
        	if($type == 'get' || $type == 'post' || $type == 'url')
        	{
        		return $this->params[$type];
        	}
        	return false;
        }
	
        /**
         *
         * @param string $view the view to render
         * @param string $data data for the view
         */
        protected function render($view, $data = null)
        {
            $this->beforeRender();
            if($this->layout)
            {
                $this->renderWithLayout($view, $data, $this->layout);
            }
            else
            {
                $this->renderView($view, $data);
            }
        }
        
        
        private function renderWithLayout($view, $data, $layout)
        {
        	
            ob_start();
            include('app'.DS.'views'.DS.$view.'.php');
            $view_content = ob_get_clean();
            
            ob_start();
            include('app'.DS.'views'.DS.'layouts'.DS.$layout.'.php');
            if($this->generateCache)
            {
            	//$all_content = ob_get_clean();
            	ActionCache::writeCache($this->name, $this->action);
            }
            ob_flush();
        }
        
        private function renderView($view, $data)
        {
            ob_start();
            include('app'.DS.'views'.DS.$view.'.php');
            ob_flush();
        }

        private function renderElement($element, $data = null)
        {
            ob_start();
            include('app'.DS.'views'.DS.$element.'.php');
            $all_content = ob_get_clean();
            return $all_content;
        }

        public function manageCache()
        {
        	if (defined('DISABLE_CACHE') && DISABLE_CACHE )
        	{
        		return false;
        	}
        	if(ActionCache::isCached($this->name, $this->action, $this->cacheActions[$this->action]['expiration']))
        	{
        		ActionCache::showCache($this->name, $this->action);
        		echo "<!-- cached -->";
        		return true;
        	}
        	else
        	{
        		$this->generateCache = true;
        	}
        	
        	return false;
        }

		protected function model($name)
		{
			return Registry::init()->model($name);
		}
        
        public function beforeAction()
        {    
        }
        
        public function afterAction()
        {   
        }
        
        public function beforeRender()
        {   
        }

        public function url($array)
        {

            if(empty($array['action']))
            {
                $url = $array['controller'];
            }
            else
            {
                $url = $array['controller'].'/'.$array['action'];
            }
            unset($array['controller']);
            unset($array['action']);
            $args = implode('/', $array);
            $route = '/'.$url.'/'.$args;
            if(strlen(DIR_ROOT)>0)
            {
                return '/'.DIR_ROOT.$route;
            }
            return $route;
        }
}