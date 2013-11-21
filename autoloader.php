<?php
/**
 * 
 */
class Autoloader
{
	private static $objectTypes = array(
		'Model' => 'models',
		'Controller' => 'controllers',
		'Config' => 'config',
		'Component' => 'components',
		'Helper'	=> 'helpers'
	);
    
    /**
     *
     * @param String $class_name class name to load
     */
    public static function loadFile($class_name, $type, $file)
    {

       if(array_key_exists($type, self::$objectTypes))
        {

	        $fileToSearch = strtolower("app".DS.self::$objectTypes[$type].DS.$file.'.php');

	        if(file_exists($fileToSearch))
	        {
		        require($fileToSearch);
		        return true;
	        }
	        else if($type == 'Controller')
	        {
		        foreach(Paths::$controllers as $dir)
		        {
			        $directory = "app".DS.self::$objectTypes[$type].DS.$dir.DS;
			        $fileToSearch = strtolower($directory.$file.'.php');
			        if(file_exists($fileToSearch))
			        {
				        require($fileToSearch);
				        return true;
			        }
		        }
		        throw new Exception("File $file not found: Error loading $class_name class. <br>Look at config/controllers.php and define your paths", E_USER_ERROR);
	        }
        }
        else
        {
        		$directory = "app".DS.'classes'.DS;
        		$fileToSearch = strtolower($directory.$file.'.php');
        		if(file_exists($fileToSearch))
	            {
	                require($fileToSearch);
	                return true;
	            }
        }
        throw new Exception("File $file not found: Error loading $class_name class. Unknown type $type", E_USER_ERROR);
    }
    
    public static function autoLoadFile($class_name)
    {
        if($name_type = self::getTypeFromName($class_name))
        {
            return self::loadFile($class_name, $name_type[1], $name_type[0]);
        }
        else
        {
            return self::loadFile($class_name, null, $class_name);
        }
    }
    
    private static function getTypeFromName($class_name)
    {
       $name_array = preg_split('/(?<=\\w)(?=[A-Z])/',$class_name);
       
        if(count($name_array) == 2)
        {
            if(array_key_exists($name_array[1], self::$objectTypes))
            {
                return $name_array;
            }
        }
        return false;
    }
    
}
