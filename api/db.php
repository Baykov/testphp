<?php

/**
 * Created by PhpStorm.
 * User: 12
 * Date: 15.02.2018
 * Time: 17:34
 */
class Db
{

    /**
     * Db constructor.
     */
    function __construct() {
        require_once dirname(__FILE__) . '/../lib/php/FluentPDO/FluentPDO.php';
        // opening db connection
        $this->pdo = new PDO("mysql:dbname=testphp", "root", "", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $this->fpdo = new FluentPDO($this->pdo);

        $this->fpdo->debug = function($BaseQuery) {
            echo "query: " . $BaseQuery->getQuery(false) . "\n";
            echo "parameters: " . implode(', ', $BaseQuery->getParameters()) . "\n";
            echo "rowCount: " . $BaseQuery->getResult()->rowCount() . "\n";
            // time is impossible to test (each time is other)
            echo $BaseQuery->getTime() . "\n";
        };
        $this->fpdo->debug = null;

    }

    /**
     * @param $params
     * @return array
     */
    public function getRegions($params = null)
    {
        $query = $this
            ->fpdo
            ->from('t_koatuu_tree')
            ->select(null)
            ->select('ter_id, ter_pid, ter_name, ter_address, ter_type_id, ter_level, ter_mask, reg_id')
            ->where('ter_level', 1)
            ->where('ter_type_id', 0);

        $result = $query->fetchAll();

        return $result;

    }
 
    /**
     * @param null $params
     * @return array
     */
    public function getCities($params = null)
    {
       // return $params['ter_id'];
        $query = $this
            ->fpdo
            ->from('t_koatuu_tree')
            ->select(null)
            ->select('ter_id, ter_pid, ter_name, ter_address, ter_type_id, ter_level, ter_mask, reg_id');
            if(isset($params['ter_id']) && !empty($params['ter_id'])){
                //$query->where('ter_level', 2);
                $query->where('ter_pid', $params['ter_id']);
            }

        $result = $query->fetchAll();

        return $result;

    }

    /**
     * @param null $params
     * @return array
     */
    public function getDistricts($params = null)
    {
        $query = $this
            ->fpdo
            ->from('t_koatuu_tree')
            ->select(null)
            ->select('ter_id, ter_pid, ter_name, ter_address, ter_type_id, ter_level, ter_mask, reg_id');
            if(isset($params['city_id']) && !empty($params['city_id'])){
                $query->where('ter_pid', $params['city_id']);
            }

        $result = $query->fetchAll();

        return $result;

    }

    /**
     * @param $params
     * @return mixed
     */
    public function getValidate($params = null)
    {
        $query = $this
            ->fpdo
            ->from('users')
            ->select(null)
            ->select('id, name, email, t_koatuu_tree.ter_address as territory');
            if(isset($params['email']) && !empty($params['email'])){
                $query->where('email', $params['email']);
                $query->leftJoin('t_koatuu_tree  ON t_koatuu_tree.ter_id = users.territory');
            }

        $user = $query->fetch();

        $result['valid'] = 'true';

        if($user){
            $result['valid'] = 'false';
        }
        $result['user'] = $user;

        return $result;

    }

    /**
     * @param $params
     * @return mixed
     */
    public function getUsers($params)
    {
        $query = $this
            ->fpdo
            ->from('users')
            ->select(null)
            ->leftJoin('t_koatuu_tree  ON t_koatuu_tree.ter_id = users.territory')
            ->select('id, name, email,  t_koatuu_tree.ter_address as territory');

        $result['data'] = $query->fetchAll();

        return $result;

    }

    /**
     * @param $params
     * @return mixed
     */
    public function getRegistration($params)
    {
        $values = array();
        $result = array();

        $result['reg'] = false;

        if(is_array($params)){
            foreach ($params as $param){
                if($param['name'] == 'select_districts'){
                    $values['territory'] =  $param['value'];
                } elseif($param['name'] == 'select_cities'){
                    $values['territory'] =  $param['value'];
                }elseif($param['name'] == 'select_regions'){
                    $values['territory'] =  $param['value'];
                } else {
                    $values[$param['name']] = $param['value'];
                }
            }

            if(isset($values['id']) && !empty($values['id'])){
                $last_insert_id = $values['id'];
                $query = $this->fpdo->update('users')->set($values)->where('id', $values['id']);
                $query->execute();
            } else{
                $query = $this
                    ->fpdo
                    ->insertInto('users')
                    ->values($values);

                $last_insert_id = $query->execute();

            }

            if($last_insert_id){
                $result['id'] = $last_insert_id;
                $result['reg'] = true;

            } else {
                $result['reg'] = false;
            }

        } else {
            $result['reg'] = false;
        }

        return $result;

    }

    /**
     * @param $params
     * @return array
     */
    public function getDeleteuser($params)
    {
        if(!empty($params['id'])){
            $query = $this
                ->fpdo
                ->deleteFrom('users')
                ->where('id', $params['id']);
            $query->execute();

            return array('status'=>200);
        } else {
            return array('status'=>204);
        }
    }

    /**
     * @param $params
     * @return array
     */
    public function getUser($params)
    {
        $result = array();

        if(!empty($params['id'])){
            $query = $this
                ->fpdo
                ->from('users u')
                ->select(null)
                ->leftJoin('t_koatuu_tree as t ON t.ter_id = u.territory')
                ->where('u.id', $params['id'])
                ->select('u.name, u.email, u.territory, t.ter_id, t.ter_pid, t.ter_level');

            $user = $query->fetch();

            $result['user'] = $user;

            if($user['ter_level'] > 2){
                $query = $this
                    ->fpdo
                    ->from('t_koatuu_tree')
                    ->select(null)
                   // ->leftJoin('t_koatuu_tree as par ON par.ter_id = child.ter_pid')
                    ->where('ter_id', $user['ter_pid'])
                    ->select('ter_id, ter_pid');
                $territorypp = $query->fetch();
                $result['city'] = $territorypp;
            }

            return $result;

        } else {
            return array('status'=>204);
        }
    }
}