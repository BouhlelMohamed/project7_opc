<?php
namespace App\Services;


class Hateoas
{

    public function buildHateoas($data)
    {
        $data = json_decode($data);


        return json_encode($data);
    }

    public function getHateoasToAllUsers($data)
    {
        foreach($data as $key => $field){
            $data[$key]->_links['self'] = '';
            $data[$key]->_links['item'] = 'rrr';
        }
    }

}