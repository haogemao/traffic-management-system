<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Report {

    private $database_medoo;

    function __construct($database_medoo) {
        $this->database_medoo = $database_medoo;
    }

    //have report list 
    function GetReportList($pageSize, $currentPage, $report_search_keywords) {
        //Check  parameters
        if (empty($pageSize) || empty($currentPage) || $pageSize == 0) {
            $data['status'] = 0;
            $data['message'] = 'Failed to get the report list ,the parameters are incomplete or wrong';
            return $data;
        }
        //Process keywords
        $report_search_keywords = "%$report_search_keywords%";
        //Pagination
        $begin = $pageSize * ($currentPage - 1);
        //operation
        $result = $this->database_medoo->select("Reports", [
            "[>]User" => ["Police_id" => "User_id"]
                ], [
            "Reports.Report_id",
            "Reports.Report_description",
            "User.User_username",
            "Reports.Submit_time",
            "Reports.Finalfine"
                ], [
            "AND" => [
                "OR" => [
                    "Reports.Report_description[~]" => $report_search_keywords,
                    "User.User_username[~]" => $report_search_keywords,
                    "Reports.Submit_time[~]" => $report_search_keywords
                ],
            ],
            "ORDER" => ["Reports.Submit_time" => "DESC"],
            "LIMIT" => [$begin, $pageSize]
        ]);
        //offence list
        foreach ($result as $key => $value) {
            $list = $this->database_medoo->select("Reports_Offence", [
                "[>]Offences" => ["Offence_id" => "Offence_id"]
                    ], [
                "Offences.Offence_id",
                "Offences.Offence_description",
                "Offences.Offence_fine",
                "Offences.Offence_point"
                    ], [
                "Reports_Offence.Report_id" => $value["Report_id"]
            ]);
            $result[$key]["offence_list"] = $list;
        }
        //person list
        foreach ($result as $key => $value) {
            $list = $this->database_medoo->select("Reports_Person", [
                "[>]Person" => ["Person_id" => "Person_id"]
                    ], [
                "Person.Person_id",
                "Person.Person_name",
                "Person.Person_address",
                "Person.Person_birth",
                "Person.Person_license",
                "Person.Person_points",
                    ], [
                "Reports_Person.Report_id" => $value["Report_id"]
            ]);
            $result[$key]["person_list"] = $list;
        }
        //vehicle list
        foreach ($result as $key => $value) {
            $list = $this->database_medoo->select("Reports_Vehicles", [
                "[>]Vehicles" => ["Vehicle_id" => "Vehicle_id"]
                    ], [
                "Vehicles.Vehicle_id",
                "Vehicles.Vehicle_make",
                "Vehicles.Vehicle_model",
                "Vehicles.Vehicle_color"
                    ], [
                "Reports_Vehicles.Report_id" => $value["Report_id"]
            ]);
            $result[$key]["vehicle_list"] = $list;
        }
        //count
        $total = $this->database_medoo->count("Reports", [
            "[>]User" => ["Police_id" => "User_id"]
                ], "*", [
            "OR" => [
                "Reports.Report_description[~]" => $report_search_keywords,
                "User.User_username[~]" => $report_search_keywords,
            ],
        ]);
        //Process self-incremented id
        $i = 0;
        foreach ($result as $key => $value) {
            $result[$key]["id"] = $i + $begin;
            $i++;
        }
        //return
        $data['status'] = 1;
        $data['message'] = 'Successfully have the list of reports';
        $data['data'] = $result;
        $data['total'] = $total;
        return $data;
    }

    //have the list of person
    function GetAllPersonList() {
        //the list of person
        $list = $this->database_medoo->select('Person', [
            "Person_id",
            "Person_name",
            "Person_address",
            "Person_birth",
            "Person_license",
            "Person_points"
                ], [
            "ORDER" => ["Person_add_time" => "DESC"],
        ]);
        //Handle Self-increasing id
        $i = 0;
        foreach ($list as $key => $value) {
            $list[$key]["id"] = $i;
            $i++;
        }
        //return
        $data['status'] = 1;
        $data['message'] = 'Successfully have the list of person';
        $data['data'] = $list;
        $data['total'] = $total;
        return $data;
    }

    //Get the list of all vehicles
    function GetAllVehicleList() {
        //operation
        $list = $this->database_medoo->select("Vehicles", [
            "Vehicle_id",
            "Vehicle_make",
            "Vehicle_model",
            "Vehicle_color",
            "Person_id"
                ], [
            "ORDER" => ["Vehicle_add_time" => "DESC"]
        ]);
        //处理自增的id
        $i = 0;
        foreach ($list as $key => $value) {
            $list[$key]["id"] = $i;
            $i++;
        }
        //return
        $data['status'] = 1;
        $data['message'] = 'Successfully have the list of vehicles';
        $data['data'] = $list;
        return $data;
    }

    //Have the list of all penalties
    function GetAllOffenceList() {
        //operation
        $list = $this->database_medoo->select("Offences", [
            "Offence_id",
            "Offence_description",
            "Offence_fine",
            "Offence_point",
        ]);
        //haddle Self-increasing id
        $i = 0;
        foreach ($list as $key => $value) {
            $list[$key]["id"] = $i;
            $i++;
        }
        //return
        $data['status'] = 1;
        $data['message'] = 'Successfully have the list of penalty clause';
        $data['data'] = $list;
        return $data;
    }

    //Add report
    function AddReport($Police_id, $PersonList, $VehicleList, $OffenceList, $Submit_time, $Finalfine, $Report_description) {
        //Check the parameters
        if (empty($Police_id) || !isset($PersonList) || !isset($VehicleList) || !isset($OffenceList) || empty($Submit_time) || !isset($Report_description)) {
            $data['status'] = 0;
            $data['message'] = 'Failed to add report, The parameters are incomplete or wrong';
            return $data;
        }
        //operation
        $this->database_medoo->insert("Reports", [
            "Report_description" => $Report_description,
            "Police_id" => $Police_id,
//            "Submit_time" => date("Y-m-d-H-i-s")
            "Submit_time" => $Submit_time,
            "Finalfine" => $Finalfine
        ]);
        $insert_id = $this->database_medoo->id(); //have the inserted data id
        //Insert Reports_Person table
        foreach ($PersonList as $key => $value) {
            $this->database_medoo->insert("Reports_Person", [
                "Person_id" => $value["Person_id"],
                "Report_id" => $insert_id
            ]);
        }
        //Insert Reports_Vehicles table
        foreach ($VehicleList as $key => $value) {
            $this->database_medoo->insert("Reports_Vehicles", [
                "Vehicle_id" => $value["Vehicle_id"],
                "Report_id" => $insert_id
            ]);
        }
        //Insert Reports_Offence table
        foreach ($OffenceList as $key => $value) {
            $this->database_medoo->insert("Reports_Offence", [
                "Offence_id" => $value["Offence_id"],
                "Report_id" => $insert_id
            ]);
        }
        //return
        $data['status'] = 1;
        $data['message'] = 'Add the report successfully';
        return $data;
    }

    //Edit the report
    function EditReport($Report_id, $Police_id, $PersonList, $VehicleList, $OffenceList, $Submit_time, $Finalfine, $Report_description) {
        //Check the parameters
        if (empty($Report_id) || empty($Police_id) || !isset($PersonList) || !isset($VehicleList) || !isset($OffenceList) || empty($Submit_time) || !isset($Report_description)) {
            $data['status'] = 0;
            $data['message'] = 'Failed to edit the report,the parameters are incomplete or incorrect';
            return $data;
        }
        //operation
        $this->DeleteReport($Report_id);
        $this->AddReport($Police_id, $PersonList, $VehicleList, $OffenceList, $Submit_time, $Finalfine, $Report_description);
        //return
        $data['status'] = 1;
        $data['message'] = 'Successfully edit the report';
        return $data;
    }

    //Delete the report 
    function DeleteReport($Report_id) {
        //Check the parameters
        if (empty($Report_id)) {
            $data['status'] = 0;
            $data['message'] = 'Failed to delete report,the parameters are Incomplete or wrong ';
            return $data;
        }
        //operation
        $this->database_medoo->delete("Reports", [
            "AND" => [
                "Report_id" => $Report_id,
            ]
        ]);
        $this->database_medoo->delete("Reports_Offence", [
            "AND" => [
                "Report_id" => $Report_id,
            ]
        ]);
        $this->database_medoo->delete("Reports_Person", [
            "AND" => [
                "Report_id" => $Report_id,
            ]
        ]);
        $this->database_medoo->delete("Reports_Vehicles", [
            "AND" => [
                "Report_id" => $Report_id,
            ]
        ]);
        //return
        $data['status'] = 1;
        $data['message'] = 'Delete list successfully';
        return $data;
    }

}
