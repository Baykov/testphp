<?php

/**
 * Created by PhpStorm.
 * User: 12
 * Date: 15.02.2018
 * Time: 17:37
 */
class Functions
{

    /**
     * @param $task
     * @param null $params
     */
    public function actionMethod($task, $params = null)
    {
        $this->_actionMethod($task, $params);
    }

    /**
     * @param $task
     * @param $params
     */
    private function _actionMethod($task, $params)
    {
        $result = $this->{'get'.ucfirst($task)}($params);
        $this->echoResponse(200, $result);
    }

    /**
     * get Regions list for select list
     * params - params
     */
    public function getRegions($params = null){
        $db = new Db();

       return $db->getRegions($params);

    }

    /**
     * @param null $params
     * @return array
     */
    public function getCities($params = null){
        $db = new Db();

        return $db->getCities($params);

    }

    /**
     * @param null $params
     * @return array
     */
    public function getDistricts($params = null){
        $db = new Db();

        return $db->getDistricts($params);

    }

    /**
     * @param null $params
     * @return mixed
     */
    public function getValidate($params = null){
        $db = new Db();

        return $db->getValidate($params);

    }

    /**
     * @param null $params
     * @return mixed
     */
    public function getDeleteuser($params = null){
        $db = new Db();

        return $db->getDeleteuser($params);

    }

    /**
     * @param null $params
     * @return mixed
     */
    public function getUser($params = null){
        $db = new Db();

        return $db->getUser($params);

    }

    /**
     * @param null $params
     * @return mixed
     */
    public function getRegistration($params = null){
        $db = new Db();

        return $db->getRegistration($params);

    }

    /**
     * @param null $params
     * @return mixed
     */
    public function getUsers($params = null){
        $db = new Db();

        return $db->getUsers($params);

    }

    /**
     * Echo Response
     * params - code, response
     */
    function echoResponse($code, $response) {

        $http_status_codes = array(200 => "OK", 204 => "No Content");

        header("Content-Type: text/html;charset=UTF-8");
        header("HTTP/1.0 $code $http_status_codes[$code]");

        echo json_encode($response, JSON_UNESCAPED_UNICODE);

        die();
    }

}