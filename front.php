<?php
class FrontController
{
	static function dispatch($controller,$action, $controller_file ,$vars_get, $vars_post, $vars_uri, $vars_arg)
	{              
		$file = "app/controllers/".$controller_file.".php";
                
		if(file_exists($file))
		{
						require($file);
                        if(class_exists($controller))
                        {
        
                            $controller_inst = new $controller();
                            if(method_exists($controller_inst,$action))
                            {
                            		$controller_inst->name = $controller;
                            		$controller_inst->action = $action;
                                    $controller_inst->setRequest($vars_get, $vars_post, $vars_uri, $vars_arg);
                                    
                                    $controller_inst->beforeAction();
                                    $generateCache = false;
                                    $avoid_call_action = false;
                                 
                                    if(isset($controller_inst->cacheActions[$action]))
                                    {   	
                                    	$avoid_call_action = $controller_inst->manageCache();	
                                    }
									
									if(!$avoid_call_action)
									{
                                    	$controller_inst->$action();
									}
                                    $controller_inst->afterAction();
                            }
                            else
                            {
                                    throw new Exception("Missing method in $controller", E_USER_ERROR);
                            }
                        }
                        else
                        {
                            throw new Exception("Class not found" ,E_USER_ERROR);
                        }
		}
		else
		{
                    throw new Exception('Missing file or action not found'.' ('.$file.')', E_USER_ERROR);
		}
	}
}