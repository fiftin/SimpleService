<?php

error_reporting(E_ALL);
//header('Content-Type: application/json; charset=utf-8', true, 200);
//header('Content-Type: text/html; charset=utf-8', true,200);
require_once 'SimpleServiceException.php';
class SimpleService {
	
	const METHOD_NOT_FOUND = 1;
	const METHOD_PARAMETER_NOT_FOUND = 2;
	const METHOD_PARAMETER_INVALID_NAME = 3;
	const ACTION_PARAMETER_INVALID_VALUE = 4;
	const METHOD_PARAMETER_INVALID_TYPE = 5;
	const ACTION_REQUIRED_PARAMETER_NOT_FOUND = 6;

	function exec($array = null) {
        if (!isset($array)) {
            $array = $_REQUEST;
        }
        try {

            if (key_exists('action', $array))
                $action = $array['action'];
            if (isset($action)) {
                $action = iconv('utf-8', 'windows-1251', $action);
                $action_method_name = "{$action}Action";
            }
            else
                $action_method_name = 'action';

            //$action_method_name = "remove_newsAction";
            if (!method_exists($this, $action_method_name)) {
                throw new SimpleServiceException("Не найден метод '$action_method_name' реализующий действие '$action'".get_class($this), SimpleService::METHOD_NOT_FOUND);
            }


            $action_method = new ReflectionMethod($this, $action_method_name);

            $args = array();

            foreach ($action_method->getParameters() as $param) {
                $name = $param->getName();
                if (preg_match('/^([a-z][a-z]?)_([a-z_0-9]+)$/i', $name, $tmp) == 0) {
                    if (preg_match('/^[a-z][a-z0-9][a-z0-9][a-z_0-9]+$/i', $name) == 0) {
                        throw new SimpleServiceException("Имя параметра '$name' метода '$action_method_name' имеет неверный формат", SimpleService::METHOD_PARAMETER_INVALID_NAME);
                    }
                    $param_type = 's';
                    $param_name = $name;
                }
                else {
                    $param_type = $tmp[1];
                    $param_name = $tmp[2];
                }
                
                if ($param_name == 'action')
                    throw new SimpleServiceException("Параметр метода '$action_method_name' имеет надопустимое имя '$name'", SimpleService::METHOD_PARAMETER_INVALID_NAME);
                    
                if (key_exists($param_name, $array)) {
                    $param_value = $array[$param_name];
                    switch ($param_type) {
                    case 's':
                        $param_type = (string)$param_type;
                        $param_value = urldecode($param_value);
                        $param_value = iconv('utf-8', 'windows-1251', $param_value);
                        break;
                    case 'u':
                        if (preg_match('/^[0-9]+$/', $param_value) == 0)
                            throw new SimpleServiceException("", SimpleService::ACTION_PARAMETER_IVALID_VALUE);
                        $param_value = (int)$param_value;
                        break;
                    case 'i':
                        if (preg_match('/^-?[0-9]+$/', $param_value) == 0)
                            throw new SimpleServiceException("", SimpleService::ACTION_PARAMETER_INVALID_VALUE);
                        $param_value = (int)$param_value;
                        break;
                    case 'j':
                        $tmp = json_decode($param_value); 
                        if ($tmp === false)
                            throw new SimpleServiceException("", SimpleService::ACTION_PARAMETER_INVALID_VALUE);                       
                        $param_value = $tmp;
                        break;
                    default:
                        throw new SimpleServiceException("", SimpleService::METHOD_PARAMETER_INVALID_TYPE);
                    }
                }
                elseif (!$param->isOptional()) {
                    throw new SimpleServiceException("", SimpleService::ACTION_REQUIRED_PARAMETER_NOT_FOUND);
                }
                if (isset($param_value)) {
                    $args[] = $param_value;
                }
            }
            
            $value = $action_method->invokeArgs($this, $args);
            
            $result = array(
            	'status' => 'success',
                'value' => (string)$value,
            	// для совместимости
            	'result'=>'success',
            );
            echo json_encode($result);
        }
        catch (Exception $ex) {
            echo json_encode(array(
                'status' => 'error',
                'value' => array(
                	'message' => iconv('windows-1251', 'utf-8', $ex->getMessage()),
                	'code' => $ex->getCode()
            	),
            	// для совместимости
            	'result'=>'error',
            	'errmsg'=>$ex->getMessage(),
            	'errcode'=>$ex->getCode()
            	));
        }
        
    }
}