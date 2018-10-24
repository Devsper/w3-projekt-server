<?php

class Authentication{

    public function authenticate($tokenToAuthenticate){

        try{
            $token = JWT::decode($tokenToAuthenticate, 'secret_server_key');
            
            return true;
        }catch(Exception $e){
    
            $res = array(
                "Exception" => $e->getMessage()
            );
    
            echo json_encode($res);
            return false;
        }
    }

    public function createToken($employee_Id){

        $token = array();
        $token['id'] = $employee_Id;
        
        return JWT::encode($token, 'secret_server_key');
    }
}