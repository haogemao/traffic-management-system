<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Person {

    private $database_medoo;

    function __construct($database_medoo) {
        $this->database_medoo = $database_medoo;
    }

    //have report list
    function GetPersonList($pageSize, $currentPage, $person_search_keywords) {
        //Check  parameters
        if (empty($pageSize) || empty($currentPage) || $pageSize == 0) {
            $data['status'] = 0;
            $data['message'] = 'Failed to get owner list,the parameters are incomplete or incorrect';
            return $data;
        }
        //Process keywords
        $person_search_keywords = "%$person_search_keywords%";
        //Pagination
        $begin = $pageSize * ($currentPage - 1);
        //List of all owners
        $list = $this->database_medoo->select('Person', [
            "Person_id",
            "Person_name",
            "Person_address",
            "Person_birth",
            "Person_license",
            "Person_points"
                ], [
            "AND" => [
                "OR" => [
                    "Person_name[~]" => $person_search_keywords,
                    "Person_address[~]" => $person_search_keywords,
                    "Person_birth[~]" => $person_search_keywords,
                    "Person_license[~]" => $person_search_keywords,
                    "Person_points[~]" => $person_search_keywords
                ],
            ],
            "ORDER" => ["Person_add_time" => "DESC"],
            "LIMIT" => [$begin, $pageSize]
        ]);
        //count
        $total = $this->database_medoo->count('Person', [
            "AND" => [
                "OR" => [
                    "Person_name[~]" => $person_search_keywords,
                    "Person_address[~]" => $person_search_keywords,
                    "Person_birth[~]" => $person_search_keywords,
                    "Person_license[~]" => $person_search_keywords,
                    "Person_points[~]" => $person_search_keywords
                ],
            ]
        ]);
        //Deal with self-incrementing id
        $i = 0;
        foreach ($list as $key => $value) {
            $list[$key]["id"] = $i + $begin;
            $i++;
        }
        //return
        $data['status'] = 1;
        $data['message'] = 'successfully get the person list ';
        $data['data'] = $list;
        $data['total'] = $total;
        return $data;
    }

    //添加车主
    function AddPerson($Person_name, $Person_address, $Person_birth, $Person_license, $Person_points) {
        //检查参数
        if (empty($Person_name) || empty($Person_address) || empty($Person_birth) || empty($Person_license) || empty($Person_points)) {
            $data['status'] = 0;
            $data['message'] = 'Failed to add person information,the parameters are incomplete or incorrect';
            return $data;
        }
        //检查有无重复
        $result = $this->database_medoo->select("Person", ["Person_id"], ["Person_license" => $Person_license]);
        if (!empty($result)) {
            $data['status'] = 0;
            $data['message'] = 'Failed to add owner information,the person information already exists';
            return $data;
        }
        $this->database_medoo->insert("Person", [
            "Person_name" => $Person_name,
            "Person_address" => $Person_address,
            "Person_birth" => $Person_birth,
            "Person_license" => $Person_license,
            "Person_points" => $Person_points,
            "Person_add_time" => date("Y-m-d-H-i-s")
        ]);
        //return
        $data['status'] = 1;
        $data['message'] = 'Successfully added person information ';
        return $data;
    }

    //Edit owner information
    function EditPerson($Person_id, $Person_name, $Person_address, $Person_birth, $Person_license, $Person_points) {
        //Check the parameters
        if (empty($Person_id) || empty($Person_name) || empty($Person_address) || empty($Person_birth) || empty($Person_license) || empty($Person_points)) {
            $data['status'] = 0;
            $data['message'] = 'Failed to edit person information,the parameters are incomplete or incorrect';
            return $data;
        }
        //operation
        $this->database_medoo->update("Person", [
            "Person_name" => $Person_name,
            "Person_address" => $Person_address,
            "Person_birth" => $Person_birth,
            "Person_license" => $Person_license,
            "Person_points" => $Person_points
                ], [
            "Person_id" => $Person_id
        ]);
        //return
        $data['status'] = 1;
        $data['message'] = 'Successfully edit person information';
        return $data;
    }

    //Delete owner information
    function DeletePerson($Person_id) {
        //Check the parameters
        if (empty($Person_id)) {
            $data['status'] = 0;
            $data['message'] = 'Failed to delete person information,the parameters are incomplete or incorrect';
            return $data;
        }
        //operation
        $this->database_medoo->delete("Person", [
            "AND" => [
                "Person_id" => $Person_id
            ]
        ]);
        $this->database_medoo->delete("Reports_Person", [
            "AND" => [
                "Person_id" => $Person_id
            ]
        ]);
        //return
        $data['status'] = 1;
        $data['message'] = 'Successfully delete person information';
        return $data;
    }

    //Gets a list of vehicles owned by the owner
    function GetPersonVehicleList($Person_id) {
        //Check the parameters
        if (empty($Person_id)) {
            $data['status'] = 0;
            $data['message'] = 'Failed to get list of vehicles owned by person,the parameters are incomplete or incorrect';
            return $data;
        }
        //operation
        $list = $this->database_medoo->select("Vehicles", [
            "Vehicle_id",
            "Vehicle_make",
            "Vehicle_model",
            "Vehicle_color",
                ], [
            "Person_id" => $Person_id,
            "ORDER" => ["Vehicle_add_time" => "DESC"],
        ]);
        //Deal with self-incrementing id
        $i = 0;
        foreach ($list as $key => $value) {
            $list[$key]["id"] = $i;
            $i++;
        }
        //return
        $data['status'] = 1;
        $data['message'] = 'Successfully retrieve list of vehicles owned by person ';
        $data['data'] = $list;
        return $data;
    }

    //Deletes the vehicle owned by the owner
    function DeletePersonVehicle($Vehicle_id) {
        //Check the parameters
        if (empty($Vehicle_id)) {
            $data['status'] = 0;
            $data['message'] = 'Fail to delete,parameter is incomplete or incorrect';
            return $data;
        }
        //operatiob
        $this->database_medoo->update("Vehicles", [
            "Person_id" => -1
                ], [
            "Vehicle_id" => $Vehicle_id
        ]);
        //return
        $data['status'] = 1;
        $data['message'] = 'Successfully deleted';
        return $data;
    }

    //Gets a list of vehicles that have no owner
    function GetPartVehicleList() {
        //operation
        $list = $this->database_medoo->select("Vehicles", [
            "Vehicle_id",
            "Vehicle_make",
            "Vehicle_model",
            "Vehicle_color"
                ], [
            "Person_id" => -1
        ]);
        //return
        $data['status'] = 1;
        $data['message'] = 'Successfully retrieve list of unowned vehicles ';
        $data['data'] = $list;
        return $data;
    }

    //Add a vehicle for the owner
    function SetPersonVehicleList($Person_id, $selected_vehicle_list) {
        //Check the parameters
        if (!isset($selected_vehicle_list) || empty($Person_id)) {
            $data['status'] = 0;
            $data['message'] = 'Fail to add,the parameters are incomplete or incorrect';
            return $data;
        }
        if (empty($selected_vehicle_list)) {
            $data['status'] = 1;
            $data['message'] = 'Set nothing';
            return $data;
        }
        //operation
        foreach ($selected_vehicle_list as $key => $value) {
            $this->database_medoo->update("Vehicles", [
                "Person_id" => $Person_id
                    ], [
                "Vehicle_id" => $value["Vehicle_id"]
            ]);
        }
        //return
        $data['status'] = 1;
        $data['message'] = 'Successfully added';
        return $data;
    }

}
