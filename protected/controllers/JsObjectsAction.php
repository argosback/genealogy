<?php
class JsObjectsAction extends CAction
{

    public function prepareJSobjects($t, $q)
    {
        if (strlen ( $q ) < 3)
            return json_encode ( array () );
        
        $list = [ ];
        
        if (strstr ( $q, ' ' ) === FALSE)
            $qlike = "%$q%";
        else
        {
            foreach ( explode ( ' ', $q ) as $v )
                $qa [] = $v . '%';
            $qlike = implode ( ' ', $qa );
        }
        
        switch ($t)
        {
            case 'r' : // 201704121429:gurgaon:thevikas:adding person
                {
                    $list = array_values ( 
                            CHtml::listData ( 
                                    Person::model ()->findAll ( 
                                            "firstname like :qlike or lastname like :qlike or t.cid=:q", 
                                            array (
                                                    'q' => $q,
                                                    'qlike' => $qlike 
                                            ) ), 'id_person', 
                                    function ($data)
                                    {
                                        return array (
                                                'label' => $data->name . " (id:{$data->cid}) " . $data->age . 'yrs',
                                                'value' => $data->cid 
                                        );
                                    } ) );
                    break;
                }            
        }
        
        return $list;
    }

    public function run($t, $q)
    {
        if (strlen ( $q ) < 3)
            return json_encode ( array () );
        $list = $this->prepareJSobjects ( $t, $q );
        $this->controller->layout = false;
        @header ( 'Content-Type: text/plain' );
        // 201604271747:vikas:#459:Gurgaon:improving jsobjects, to search two
        // incomplete words
        
        global $wp_super_cache_comments;
        $wp_super_cache_comments = false;
        
        $this->controller->render ( 'jsobjects', [ 
                'list' => $list 
        ] );
    }
}
