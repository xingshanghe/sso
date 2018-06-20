<?php
/** 
* SobeyFormAuthenticate
* 
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年6月3日下午2:05:53
* @source SobeyFormAuthenticate.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 
namespace App\Auth;

use Cake\Auth\FormAuthenticate;
use Cake\ORM\TableRegistry;
use Cake\Network\Request;
use Cake\Network\Response;


class SobeyFormAuthenticate extends FormAuthenticate
{
    
    protected $_defaultConfig = [
        'fields' => [
            'username' => 'username',
            'password' => 'password'
        ],
        'userModel' => 'Users',
        'scope' => [],
        'contain' => null,
        'passwordHasher' => 'Default',
        'returnFields' => null
    ];
    
    /**
     * Authenticates the identity contained in a request. Will use the `config.userModel`, and `config.fields`
     * to find POST data that is used to find a matching record in the `config.userModel`. Will return false if
     * there is no post data, either username or password is missing, or if the scope conditions have not been met.
     *
     * @param \Cake\Network\Request $request The request that contains login information.
     * @param \Cake\Network\Response $response Unused response object.
     * @return mixed False on login failure.  An array of User data on success.
     */
    public function authenticate(Request $request, Response $response)
    {
        $fields = $this->_config['fields'];
        if (!$this->_checkFields($request, $fields)) {
            return false;
        }
        return $this->_findUser(
            $request->data[$fields['username']],
            $request->data[$fields['password']],
            $this->_config['returnFields']
        );
    }
    
    
    /**
     * Find a user record using the username and password provided.
     *
     * Input passwords will be hashed even when a user doesn't exist. This
     * helps mitigate timing attacks that are attempting to find valid usernames.
     *
     * @param string $username The username/identifier.
     * @param string|null $password The password, if not provide password checking is skipped
     *   and result of find is returned.
     * @return bool|array Either false on failure, or an array of user data.
     */
    protected function _findUser($username, $password = null,$returnFields = null)
    {
        $userModel = $this->_config['userModel'];
        list(, $model) = pluginSplit($userModel);
        $fields = $this->_config['fields'];
    
        $conditions = [$model . '.' . $fields['username'] => $username];
    
        $scope = $this->_config['scope'];
        if ($scope) {
            $conditions = array_merge($conditions, $scope);
        }
    
        $table = TableRegistry::get($userModel)->find('all');
    
        $contain = $this->_config['contain'];
        if ($contain) {
            $table = $table->contain($contain);
        }
    
        $result = is_null($this->_config['returnFields'])?
                    $table->where($conditions)->first():$table->select($this->_config['returnFields'])->where($conditions)->first();
        if (empty($result)) {
            return false;
        }
    
        if ($password !== null) {
            $hasher = $this->passwordHasher();
            //设置salt
            $hasher->setSalt($result->get('salt'));
            $hashedPassword = $result->get($fields['password']);
            if (!$hasher->check($password, $hashedPassword)) {
                return false;
            }
    
            $this->_needsPasswordRehash = $hasher->needsRehash($hashedPassword);
            $result->unsetProperty($fields['password']);
        }
        return $result->toArray();
    }
}


