<?php

class Authentication{

    /**
     * Authenticates JSON Web Token from logged in employee
     * 
     * @param string $tokenToAuthenticate Token from client
     * @throws Exception Exception if invalid token 
     * @return boolean Authentication status
     */
    public function authenticate($tokenToAuthenticate){

        try{
        	  // Authenticates sent token from client through JWT class
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

    /**
     * Creates JSON Web Token from employee password
     * 
     * @param string $employeePass Employee password to hash in to token
     * @return string Authentication token
     */
    public function createToken($employeePass){
        
        return JWT::encode($employeePass, 'secret_server_key');
    }
}
