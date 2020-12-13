<?php
namespace App\Services;

class Hateoas
{
    public function getHateoasToAllUsers($data,$customerId)
    {
        $data = json_decode($data);
        foreach($data as $key => $field){
            $data[$key]->_links['item'] = '/api/users/'.$field->id.'/customers/'.$customerId;
        }
        return json_encode($data);
    }

    public function getHateoasToOneUser($data)
    {
        $data = json_decode($data);
        $data = (array)$data;
        $data['_links']['allUsers'] = '/api/users/customers/'.$data['customer']->id;
        return json_encode($data);
    }

    public function getHateoasAllPhones($data)
    {
        $data = json_decode($data);
        foreach($data as $key => $field){
            $data[$key]->_links['item'] = '/api/phones/'.$field->id;
        }
        return json_encode($data);
    }

    public function getHateoasOnePhone($data)
    {
        $data = json_decode($data);
        $data = (array)$data;
        $data['_links']['allPhones'] = '/api/phones';
        return json_encode($data);
    }
}