<?php

define('BASE_PATH', __DIR__);

date_default_timezone_set('PRC'); //8 hours difference between the acquisition time and the actual time

require_once BASE_PATH . '/Controller/offence.class.php';
require_once BASE_PATH . '/Controller/person.class.php';
require_once BASE_PATH . '/Controller/report.class.php';
require_once BASE_PATH . '/Controller/user.class.php';
require_once BASE_PATH . '/Controller/vehicle.class.php';
require_once BASE_PATH . '/Data/connection.php';

$Offence = new Offence($database_medoo);
$Person = new Person($database_medoo);
$Report = new Report($database_medoo);
$User = new User($database_medoo);
$Vehicle = new Vehicle($database_medoo);

$requestPayload = file_get_contents("php://input");
$requestPayload = !empty($requestPayload) ? json_decode($requestPayload, true) : array();
$function = $_GET['function']; //The program normally requests use

/*
 * All interfaces except the login interface need to verify token
 */
$thisToken = $_SERVER['HTTP_TOKEN'];
if ($function != 'Login') {
    $data = $User->CheckToken($thisToken);
    if ($data['code'] != '200') {
        echo json_encode(array(
            'status' => '0',
            'code' => $data['code'],
            'message' => $data['message']
        ));
        return;
    }
}

/*
 * Obtain the userid of the front-end user according to the token to prevent the front-end from tampering with the user id
 */
$this_userid = $User->GetUserInfoByToken($thisToken)["userid"];
$this_username = $User->GetUserInfoByToken($thisToken)["username"];

/*
 * switch case
 */
switch ($function) {
    /*
     * User
     */
    case 'Login': {
            $username = trim($requestPayload['username']);
            $password = trim($requestPayload['password']);
            echo json_encode($User->Login($username, $password));
        };
        break;
    case 'GetUserList': {
            $pageSize = $requestPayload['pageSize'];
            $currentPage = $requestPayload['currentPage'];
            $user_search_keywords = trim($requestPayload['user_search_keywords']);
            echo json_encode($User->GetUserList($pageSize, $currentPage, $user_search_keywords = ""));
        };
        break;
    case 'AddUser': {
            $Role_id = $requestPayload['Role_id'];
            $User_username = $requestPayload['User_username'];
            $User_password = $requestPayload['User_password'];
            $User_name = $requestPayload['User_name'];
            echo json_encode($User->AddUser($Role_id, $User_username, $User_password, $User_name));
        };
        break;
    case 'DeleteUser': {
            $User_id = $requestPayload['User_id'];
            echo json_encode($User->DeleteUser($User_id));
        };
        break;
    case 'EditUser': {
            $User_id = $requestPayload['User_id'];
            $Role_id = $requestPayload['Role_id'];
            $User_username = $requestPayload['User_username'];
            $User_password = $requestPayload['User_password'];
            $User_name = $requestPayload['User_name'];
            echo json_encode($User->EditUser($User_id, $Role_id, $User_username, $User_password, $User_name));
        };
        break;
    case 'GetNowUser': {
            $User_id = $this_userid;
            echo json_encode($User->GetNowUser($User_id));
        };
        break;
    case 'SetNowUserInfo': {
            $User_id = $this_userid;
            $User_password = $requestPayload['User_password'];
            $Re_user_password = $requestPayload['Re_user_password'];
            echo json_encode($User->SetNowUserInfo($User_id, $User_password, $Re_user_password));
        };
        break;
    /*
     * Person
     */
    case 'AddPerson': {
            $Person_name = trim($requestPayload['Person_name']);
            $Person_address = trim($requestPayload['Person_address']);
            $Person_birth = trim($requestPayload['Person_birth']);
            $Person_license = trim($requestPayload['Person_license']);
            $Person_points = $requestPayload['Person_points'];
            echo json_encode($Person->AddPerson($Person_name, $Person_address, $Person_birth, $Person_license, $Person_points));
        };
        break;
    case 'DeletePerson': {
            $Person_id = $requestPayload['Person_id'];
            echo json_encode($Person->DeletePerson($Person_id));
        };
        break;
    case 'EditPerson': {
            $Person_id = $requestPayload['Person_id'];
            $Person_name = trim($requestPayload['Person_name']);
            $Person_address = trim($requestPayload['Person_address']);
            $Person_birth = trim($requestPayload['Person_birth']);
            $Person_license = trim($requestPayload['Person_license']);
            $Person_points = $requestPayload['Person_points'];
            echo json_encode($Person->EditPerson($Person_id, $Person_name, $Person_address, $Person_birth, $Person_license, $Person_points));
        };
        break;
    case 'GetPersonList': {
            $pageSize = $requestPayload['pageSize'];
            $currentPage = $requestPayload['currentPage'];
            $person_search_keywords = trim($requestPayload['person_search_keywords']);
            echo json_encode($Person->GetPersonList($pageSize, $currentPage, $person_search_keywords));
        };
        break;
    case 'GetPersonVehicleList': {
            $Person_id = trim($requestPayload['Person_id']);
            echo json_encode($Person->GetPersonVehicleList($Person_id));
        };
        break;
    case 'DeletePersonVehicle': {
            $Vehicle_id = trim($requestPayload['Vehicle_id']);
            echo json_encode($Person->DeletePersonVehicle($Vehicle_id));
        };
        break;
    case 'GetPartVehicleList': {
            echo json_encode($Person->GetPartVehicleList());
        };
        break;
    case 'SetPersonVehicleList': {
            $Person_id = $requestPayload['Person_id'];
            $selected_vehicle_list = $requestPayload['selected_vehicle_list'];
            echo json_encode($Person->SetPersonVehicleList($Person_id, $selected_vehicle_list));
        };
        break;
    /*
     * Vehicle
     */
    case 'GetVehicleList': {
            $pageSize = $requestPayload['pageSize'];
            $currentPage = $requestPayload['currentPage'];
            $vehicle_search_keywords = trim($requestPayload['vehicle_search_keywords']);
            echo json_encode($Vehicle->GetVehicleList($pageSize, $currentPage, $vehicle_search_keywords));
        };
        break;
    case 'AddVehicle': {
            $Vehicle_id = trim($requestPayload['Vehicle_id']);
            $Vehicle_make = trim($requestPayload['Vehicle_make']);
            $Vehicle_model = trim($requestPayload['Vehicle_model']);
            $Vehicle_color = trim($requestPayload['Vehicle_color']);
            echo json_encode($Vehicle->AddVehicle($Vehicle_id, $Vehicle_make, $Vehicle_model, $Vehicle_color));
        };
        break;
    case 'EditVehicle': {
            $Vehicle_id = trim($requestPayload['Vehicle_id']);
            $Vehicle_make = trim($requestPayload['Vehicle_make']);
            $Vehicle_model = trim($requestPayload['Vehicle_model']);
            $Vehicle_color = trim($requestPayload['Vehicle_color']);
            echo json_encode($Vehicle->EditVehicle($Vehicle_id, $Vehicle_make, $Vehicle_model, $Vehicle_color));
        };
        break;
    case 'DeleteVehicle': {
            $Vehicle_id = trim($requestPayload['Vehicle_id']);
            echo json_encode($Vehicle->DeleteVehicle($Vehicle_id));
        };
        break;
    case 'GetOwnerInfo': {
            $Vehicle_id = trim($requestPayload['Vehicle_id']);
            echo json_encode($Vehicle->GetOwnerInfo($Vehicle_id));
        };
        break;
    /*
     * Reports
     */
    case 'GetReportList': {
            $pageSize = $requestPayload['pageSize'];
            $currentPage = $requestPayload['currentPage'];
            $report_search_keywords = trim($requestPayload['report_search_keywords']);
            echo json_encode($Report->GetReportList($pageSize, $currentPage, $report_search_keywords));
        };
        break;
    case 'AddReport': {
            $Police_id = $this_userid;
            $PersonList = $requestPayload['PersonList'];
            $VehicleList = $requestPayload['VehicleList'];
            $OffenceList = $requestPayload['OffenceList'];
            $Submit_time = $requestPayload['Submit_time'];
            $Finalfine = $requestPayload['Finalfine'];
            $Report_description = trim($requestPayload['Report_description']);
            echo json_encode($Report->AddReport($Police_id, $PersonList, $VehicleList, $OffenceList, $Submit_time, $Finalfine, $Report_description));
        };
        break;
    case 'EditReport': {
            $Report_id = $requestPayload["Report_id"];
            $Police_id = $this_userid;
            $PersonList = $requestPayload['PersonList'];
            $VehicleList = $requestPayload['VehicleList'];
            $OffenceList = $requestPayload['OffenceList'];
            $Submit_time = $requestPayload['Submit_time'];
            $Finalfine = $requestPayload['Finalfine'];
            $Report_description = trim($requestPayload['Report_description']);
            echo json_encode($Report->EditReport($Report_id, $Police_id, $PersonList, $VehicleList, $OffenceList, $Submit_time, $Finalfine, $Report_description));
        };
        break;
    case 'DeleteReport': {
            $Report_id = $requestPayload['Report_id'];
            echo json_encode($Report->DeleteReport($Report_id));
        };
        break;
    case 'GetAllPersonList': {
            echo json_encode($Report->GetAllPersonList());
        };
        break;
    case 'GetAllVehicleList': {
            echo json_encode($Report->GetAllVehicleList());
        };
        break;
    case 'GetAllOffenceList': {
            echo json_encode($Report->GetAllOffenceList());
        };
        break;
}