<?php
/** 
* class
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年5月24日下午2:10:39
* @source AccountInfosTable.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

namespace App\Model\Table;

//use Cake\Event\Event;
//use Cake\ORM\Entity;
class AccountInfosTable extends SobeyTable
{
    public function initialize( array $config){
        parent::initialize($config);
        
        $this->addBehavior('Timestamp',[
            'events'=>[
                'Model.beforeSave'=>[
                    'created_time'=>'new',
                    'modified_time'=>'always'
                ],
            ],
        ]);
        
    }
    /*
    public function beforeSave(Event $event,Entity $entity, array $options){
        $isNew = $entity->isNew();
        
    }
    */
    
   
    
  
}