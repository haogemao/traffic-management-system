<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Vehicle {

    private $database_medoo;

    function __construct($database_medoo) {
        $this->database_medoo = $database_medoo;
    }

    //Get a list of vehicles
    function GetVehicleList($pageSize, $currentPage, $vehicle_search_keywords) {
        //Check the parameters
        if (empty($pageSize) || empty($currentPage) || $pageSize == 0) {
            $data['status'] = 0;
            $data['message'] = 'Failed to get the vehicle list, the parameters are incomplete or wrong';
            return $data;
        }
        //Keyword processing
        $vehicle_search_keywords = "%$vehicle_search_keywords%";
        //Pagination
        $begin = $pageSize * ($currentPage - 1);
        //operation
        $list = $this->database_medoo->select("Vehicles", [
            "Vehicle_id",
            "Vehicle_make",
            "Vehicle_model",
            "Vehicle_color",
            "Person_id"
                ], [
            "AND" => [
                "OR" => [
                    "Vehicle_id[~]" => $vehicle_search_keywords,
                    "Vehicle_make[~]" => $vehicle_search_keywords,
                    "Vehicle_model[~]" => $vehicle_search_keywords,
                    "Vehicle_color[~]" => $vehicle_search_keywords
                ],
            ],
            "ORDER" => ["Vehicle_add_time" => "DESC"],
            "LIMIT" => [$begin, $pageSize]
        ]);
        //count
        $total = $this->database_medoo->count("Vehicles", [
            "AND" => [
                "OR" => [
                    "Vehicle_id[~]" => $vehicle_search_keywords,
                    "Vehicle_make[~]" => $vehicle_search_keywords,
                    "Vehicle_model[~]" => $vehicle_search_keywords,
                    "Vehicle_color[~]" => $vehicle_search_keywords
                ],
            ]
        ]);
        //Processing self-increasing id
        $i = 0;
        foreach ($list as $key => $value) {
            $list[$key]["id"] = $i + $begin;
            $i++;
        }
        //return
        $data['status'] = 1;
        $data['message'] = 'Get the vehicle list successfully';
        $data['data'] = $list;
        $data['total'] = $total;
        return $data;
    }

    //Add vehicle
    function AddVehicle($Vehicle_id, $Vehicle_make, $Vehicle_model, $Vehicle_color) {
        //check
        if (empty($Vehicle_id) || empty($Vehicle_make) || empty($Vehicle_model) || empty($Vehicle_color)) {
            $data['status'] = 0;
            $data['message'] = 'Failed to add vehicle information, the parameters are incomplete or wrong';
            return $data;
        }
        //Check for duplication
        $result = $this->database_medoo->select("Vehicles", ["Vehicle_id"], ["Vehicle_id" => $Vehicle_id]);
        if (!empty($result)) {
            $data['status'] = 0;
            $data['message'] = 'Failed to add vehicle information, vehicle information already exists';
            return $data;
        }
        //operation
        $this->database_medoo->insert("Vehicles", [
            "Vehicle_id" => $Vehicle_id,
            "Vehicle_make" => $Vehicle_make,
            "Vehicle_model" => $Vehicle_model,
            "Vehicle_color" => $Vehicle_color,
            "Person_id" => -1,
            "Vehicle_add_time" => date("Y-m-d-H-i-s")
        ]);
        //return
        $data['status'] = 1;
        $data['message'] = 'Added vehicle information successfully';
        return $data;
    }

    //Edit vehicle information
    function EditVehicle($Vehicle_id, $Vehicle_make, $Vehicle_model, $Vehicle_color) {
        //Check the parameters
        if (empty($Vehicle_id) || empty($Vehicle_make) || empty($Vehicle_model) || empty($Vehicle_color)) {
            $data['status'] = 0;
            $data['message'] = 'Failed to edit vehicle information, the parameters are incomplete or wrong';
            return $data;
        }
        //opration
        $this->database_medoo->update("Vehicles", [
            "Vehicle_make" => $Vehicle_make,
            "Vehicle_model" => $Vehicle_model,
            "Vehicle_color" => $Vehicle_color,
            "Vehicle_add_time" => date("Y-m-d-H-i-s")
                ], [
            "Vehicle_id" => $Vehicle_id,
        ]);
        //return
        $data['status'] = 1;
        $data['message'] = 'Edit vehicle information successfully';
        return $data;
    }

    //delete vehicle information
    function DeleteVehicle($Vehicle_id) {
        //Check the parameters
        if (empty($Vehicle_id) || $Vehicle_id == "") {
            $data['status'] = 0;
            $data['message'] = 'Failed to delete vehicle, incomplete or wrong parameters';
            return $data;
        }
        //opration
        $this->database_medoo->delete("Vehicles", [
            "AND" => [
                "Vehicle_id" => $Vehicle_id
            ]
        ]);
        //opration
        $this->database_medoo->delete("Reports_Vehicles", [
            "AND" => [
                "Vehicle_id" => $Vehicle_id
            ]
        ]);
        //return
        $data['status'] = 1;
        $data['message'] = 'Delete vehicle information successfully';
        return $data;
    }

    //obtain vehicle information
    function GetOwnerInfo($Vehicle_id) {
        //Check the parameters
        if (empty($Vehicle_id) || $Vehicle_id == "") {
            $data['status'] = 0;
            $data['message'] = 'Failed to obtain vehicle owner information, the parameters are incomplete or wrong';
            return $data;
        }
        //opration
        $result = $this->database_medoo->select("Vehicles", ["Person_id"], ["Vehicle_id" => $Vehicle_id]);
        if ($result[0]["Person_id"] == -1) {
            $data['status'] = 1;
            $data['message'] = 'Successfully obtain vehicle owner information';
            $data['data']['isnull'] = true;
            return $data;
        }
        $result = $this->database_medoo->select("Vehicles", [
            "[>]Person" => ["Person_id" => "Person_id"]
                ], [
            "Person.Person_name",
            "Person.Person_address",
            "Person.Person_birth",
            "Person.Person_license",
            "Person.Person_points"
                ], [
            "Vehicles.Vehicle_id" => $Vehicle_id
        ]);
        $result[0]['isnull'] = false;
        $data['status'] = 1;
        $data['message'] = 'Successfully obtain vehicle owner information';
        $data['data'] = $result[0];
        return $data;
    }

}
