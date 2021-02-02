<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class User {

    private $database_medoo;

    function __construct($database_medoo) {
        $this->database_medoo = $database_medoo;
    }

    //User login
    function Login($username, $password) {
        $result = $this->database_medoo->select('User', [
            "[>]Role" => ["User_roleid" => "Role_id"],
                ], [
            "User.User_id(userid)",
            "User.User_username",
            "User.User_roleid",
            "User.User_password",
            "Role.Role_name"
                ], [
            "User_username" => $username,
            "User_password" => $password
        ]);

        if (empty($result)) {
            $data['status'] = 0;
            $data['message'] = 'Login failed, user does not exist or password is wrong';
            return $data;
        }
        $token = $this->CreateToken($result[0]['userid']);

        //Return success message
        $data['status'] = 1;
        $data['code'] = 200;
        $data['userid'] = $result[0]['User_id'];
        $data['username'] = $result[0]['User_username'];
        $data['roleid'] = $result[0]['User_roleid'];
        $data['rolename'] = $result[0]['Role_name'];
        $data['token'] = $token;
        $data['message'] = 'login successful';
        return $data;
    }

    //Get a list of users
    function GetUserList($pageSize, $currentPage, $user_search_keywords) {
        //Check the parameters
        if (empty($pageSize) || empty($currentPage) || $pageSize == 0) {
            $data['status'] = 0;
            $data['message'] = 'Failed to get the user list, the parameters are incomplete or wrong';
            return $data;
        }
        //Keyword processing
        $user_search_keywords = "%$user_search_keywords%";
        //Paging processing
        $begin = $pageSize * ($currentPage - 1);
        //All user lists
        $list = $this->database_medoo->select('User', [
            "[>]Role" => ["User_roleid" => "Role_id"]
                ], [
            "User.User_id",
            "User.User_username",
            "User.User_password",
            "User.User_name",
            "Role.Role_name",
            "Role.Role_id"
                ], [
            "AND" => [
                "OR" => [
                    "User.User_username[~]" => $user_search_keywords,
                    "User.User_password[~]" => $user_search_keywords,
                    "User.User_name[~]" => $user_search_keywords,
                    "Role.Role_name[~]" => $user_search_keywords
                ],
            ],
            "LIMIT" => [$begin, $pageSize]
        ]);
        //count
        $total = $this->database_medoo->count('User', [
            "[>]Role" => ["User_roleid" => "Role_id"]
                ], "*", [
            "OR" => [
                "User.User_username[~]" => $user_search_keywords,
                "User.User_password[~]" => $user_search_keywords,
                "User.User_name[~]" => $user_search_keywords,
                "Role.Role_name[~]" => $user_search_keywords
            ],
        ]);
        //Deal with self-incrementing id
        $i = 0;
        foreach ($list as $key => $value) {
            $list[$key]["id"] = $i + $begin;
            $i++;
        }
        //return
        $data['status'] = 1;
        $data['message'] = 'Get the user list successfully';
        $data['data'] = $list;
        $data['total'] = $total;
        return $data;
    }

    //add users
    function AddUser($Role_id, $User_username, $User_password, $User_name) {
        //Check the parameters
        if (empty($Role_id) || empty($User_username) || empty($User_password) || empty($User_name)) {
            $data['status'] = 0;
            $data['message'] = 'Failed to add user, parameter is incomplete or wrong';
            return $data;
        }
        //operation
        $this->database_medoo->insert("User", [
            "User_roleid" => $Role_id,
            "User_username" => $User_username,
            "User_password" => $User_password,
            "User_name" => $User_name
        ]);
        //return
        $data['status'] = 1;
        $data['message'] = 'User added successfully';
        return $data;
    }

    //Delete User
    function DeleteUser($User_id) {
        //Check the parameters
        if (empty($User_id)) {
            $data['status'] = 0;
            $data['message'] = 'Failed to delete user, parameter is incomplete or wrong';
            return $data;
        }
        //operation
        $this->database_medoo->delete("User", [
            "User_id" => $User_id
        ]);
        //return
        $data['status'] = 1;
        $data['message'] = 'User deleted successfully';
        return $data;
    }

    //Edit User
    function EditUser($User_id, $Role_id, $User_username, $User_password, $User_name) {
        //Check the parameters
        if (empty($User_id) || empty($Role_id) || empty($User_username) || empty($User_password) || empty($User_name)) {
            $data['status'] = 0;
            $data['message'] = 'Failed to edit user, incomplete or wrong parameters';
            return $data;
        }
        //operation
        $this->database_medoo->update("User", [
            "User_roleid" => $Role_id,
            "User_username" => $User_username,
            "User_password" => $User_password,
            "User_name" => $User_name
                ], [
            "User_id" => $User_id
        ]);
        //return
        $data['status'] = 1;
        $data['message'] = 'Edit user successfully';
        return $data;
    }

    //Gets the current user information
    function GetNowUser($User_id) {
        //Check the parameters
        if (empty($User_id)) {
            $data['status'] = 0;
            $data['message'] = 'Failed to obtain user information, the parameters are incomplete or wrong';
            return $data;
        }
        //opration
        $list = $this->database_medoo->select('User', [
            "User_id",
            "User_username",
            "User_name",
                ], [
            "User_id" => $User_id
        ]);
        //return
        $data['status'] = 1;
        $data['message'] = 'Get user information successfully';
        $data['data'] = $list[0];
        return $data;
    }

    //Change the user's current information
    function SetNowUserInfo($User_id, $User_password, $Re_user_password) {
        //Check the parameters
        if (empty($User_id) || empty($User_password) || empty($Re_user_password)) {
            $data['status'] = 0;
            $data['message'] = 'Failed to modify user information, the parameters are incomplete or wrong';
            return $data;
        }
        if ($User_password != $Re_user_password) {
            $data['status'] = 0;
            $data['message'] = 'Failed to modify user information, the password is inconsistent';
            return $data;
        }
        //opration
        $this->database_medoo->update("User", [
            "User_password" => $User_password
                ], [
            "User_id" => $User_id
        ]);
        //return
        $data['status'] = 1;
        $data['message'] = 'Successfully modify user information';
        return $data;
    }

    // generate token
    public function CreateToken($userid) {
        $time = time();
        $end_time = time() + 86400;
        $info = $userid . '.' . $time . '.' . $end_time; //Set the token expiration time to one day
        //enerate the signature based on the above information (the key is SIASQR)
        $signature = hash_hmac('md5', $info, SIGNATURE);
        //Finally, the two parts are concatenated to get the final Token string
        return $token = $info . '.' . $signature;
    }

    //check token
    public function CheckToken($token) {
        if (!isset($token) || empty($token)) {
            $data['code'] = '400';
            $data['message'] = 'Illegal request';
            return $data;
        }
        //contrast token
        $explode = explode('.', $token); //Take the split token as an array
        if (!empty($explode[0]) && !empty($explode[1]) && !empty($explode[2]) && !empty($explode[3])) {
            $info = $explode[0] . '.' . $explode[1] . '.' . $explode[2]; //information part
            $true_signature = hash_hmac('md5', $info, SIGNATURE); //Correct signature
            if (time() > $explode[2]) {
                $data['code'] = '401';
                $data['message'] = 'Token expired, please login again';
                return $data;
            }
            if ($true_signature == $explode[3]) {
                $data['code'] = '200';
                $data['message'] = 'Token lega';
                return $data;
            } else {
                $data['code'] = '400';
                $data['message'] = 'Token illegal';
                return $data;
            }
        } else {
            $data['code'] = '400';
            $data['message'] = 'Token illegal';
            return $data;
        }
    }

    //Get userid based on token
    public function GetUserInfoByToken($token) {
        $explode = explode('.', $token);
        $result = $this->database_medoo->select("user", ["username"], ["id" => $explode[0]]);
        $data = array(
            "userid" => $explode[0],
            "username" => $result[0]["username"]
        );
        return $data;
    }

    //Email check
    private function CheckMail($mail) {
        $pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";
        preg_match($pattern, $mail, $matches);
        $flag = false;
        if (!empty($matches)) {
            $flag = true;
        }
        return $flag;
    }

}
