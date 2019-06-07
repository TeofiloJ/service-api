<?php

use Luracast\Restler\iAuthenticate;

class auth implements iAuthenticate {
    
    const TOKEN = "";

    function getToken($table, $type){

        //get api rights
        $req="select * from api_right  where api=?";
        $prep=$this->db->prepare($req);
        $prep->bindParam(1, $table);
        $prep->execute();

        $rights = $prep->fetchObject();

        //is Auth necessary
        if ($rights->auth == true) {

            //is user logged
            if (isset($_SESSION['userid'])) {
                
                //get user rights
                $req="select * from user_right_token  where id_user=?";
                $prep=$this->db->prepare($req);
                $prep->bindParam(1, $table);
                $prep->execute();
                $user_rights = $prep->fetchObject();

                if ($_SESSION['userid'] == $user_rights->id_user) {
                    if($user_rights->power >= $rights->$type){
                        return $user_rights->token;
                    }                    
                }
            }
        }
        return "";       
        
    }

    function __isAllowed()
    {
        if (isset($_GET['token'])) {
            if()
        }
        return isset($_GET['token']) && $_GET['token'] == SimpleAuth::TOKEN ? TRUE : FALSE;
    }

    public function __getWWWAuthenticateString()
    {
        return 'Query name="token"';
    }

    function key()
    {
        return SimpleAuth::TOKEN;
    }


}



?>