<?php

/**
 * @author Josua
 */
class Request
{

  protected $params = null;

  /**
   * it creates a new instance of Request
   *
   * @param array $params
   */
  public function __construct($params = null)
  {
    $this->setParams($params);
  }

  /**
   * set the request parameters
   *
   * @param array $params
   */
  public function setParams($params = null)
  {
    self::clearParams();
    if($params && is_array($params)){
      foreach($params as $key => $value){
        if(!$key || trim($key) == ''){
          continue;
        }

        $this->params[$key] = $value;
      }
    }
  }

  /**
   * get one parameter from the web service request
   *
   * @param string $key
   * @param mixed $default
   *
   * @return mixed
   */
  public function getParam($key, $default = null)
  {
    $value = $this->params[$key];

    if(!is_array($value) && !is_object($value) && (is_null($value) || trim($value) == '')){
      return $default;
    }

    return $value;
  }

  /**
   * add or update a parameter in the request
   *
   * @param string $key
   * @param mixed $value
   */
  public function putParam($key, $value)
  {
    $this->params[$key] = $value;
  }

  /**
   * get all request parameters
   *
   * @return array
   */
  public function getParams()
  {
    return $this->params;
  }

  /**
   * clean parameters
   */
  public function clearParams()
  {
    if(is_array($this->params)){
      array_splice($this->params, 0);
    }else{
      $this->params = array();
    }
  }

  /**
   * Is empty request check
   */
  public function isEmpty()
  {
    return (!is_array($this->params) || count($this->params) == 0);
  }

  /**
   * Loads a secondary request in, used mostly when a json request is made
   *
   * @param array $params
   * @param boolean $force
   */
  public function overwriteRequest($params, $force = false)
  {
    if(!is_array($this->params) || count($this->params) == 0 || $force){
      $this->setParams($params);
    }
  }

  /**
   * convert request to string
   */
  public function __toString()
  {
    $str = "";
    if(is_array($this->params)){
      foreach($this->params as $key => $value){
        $str .= "$key=$value\n";
      }
    }

    return $str;
  }
}

?>