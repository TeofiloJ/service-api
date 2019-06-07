<?php
use Luracast\Restler\RestException;

require_once("./cfg/localData.php");
class templacement
{
    private $bd;
    static $CHAMPS = array('type', 'prix');
    function __construct()
    {
        $this->bd = new PDO(DNS, USER, MDP);
    }
    function head()
    {
        echo "ici";
        $tab[]="id";
        foreach (templacement::$CHAMPS as $champ) {
      //on indique tous les champs attendus pour la table templacement
            $tab[]=$champ;
        }
        echo "ici";
        return "toto";
    }
    function get($id = null)
    {
		$id=htmlentities($id);
        if ($id != null) {
            $req="select * from type_emplacements where id=?";
            $prep=$this->bd->prepare($req);
            $prep->bindParam(1, $id);
            $prep->execute();
            if (empty($templacement = $prep->fetchObject()) && $id != null) {
                $langage=null;
                if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                    $langage = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
                }
                switch ($langage) {
                    case "fr":
                        $message="Cet ID n'existe pas";
                        break;
                    case "de":
                        $message="Diese ID existiert nicht";
                        break;
                    case "es":
                        $message="Este ID no existe";
                        break;
                    case "it":
                        $message="Questo ID non esiste";
                        break;
                    default:
                        $message="This ID doesn't exist";
                }
                throw new RestException(400, $message);
            }
            $templacement->id=intval($templacement->id);
            $retour=$templacement;
        } else {
            $req="select * from type_emplacements";
            $resultat = $this->bd->query($req);
            $indiv=new templacement();
            while ($templacement=$resultat->fetchObject()) {
                $templacement->id=intval($templacement->id);
                $retour[]=$templacement;
            }
        }
        return $retour;
    }
    function post($request_data = null)
    {
        $templacement=$this->_validation($request_data);
        $req="INSERT INTO type_emplacements (`type`, prix) VALUES (?,?)";
        $prep=$this->bd->prepare($req);
        $type=$templacement["type"];
        $prix=$templacement["prix"];
        $prep->bindParam(1, $type);
        $prep->bindParam(2, $prix);
        $prep->execute();
        return $this->get($this->bd->lastInsertId());
    }
    //modification partielle
    function patch($id, $request_data = null)
    {
        $templacement=$this->_rempli($id, $request_data);
    //$templacement=$this->_validation($request_data);
        $req="update type_emplacements set type=?, prix=? where id=?";
        $type=$templacement["type"];
        $prix=$templacement["prix"];
        $prep=$this->bd->prepare($req);
        $prep->bindParam(1, $type);
        $prep->bindParam(2, $prix);
        $prep->bindParam(3, $id);
        $prep->execute();
        return $this->get($id);//$request_data;//$this->dp->update($id, $this->_validate($request_data));
    }
    function put($id, $request_data = null)
    {
        $templacement=$this->_validation($request_data);
    //$templacement=$request_data;//$this->_rempli($request_data);
        $req="update type_emplacements set type=?, prix=? where id=?";
        $type=$templacement["type"];
        $prix=$templacement["prix"];
        $prep=$this->bd->prepare($req);
        $prep->bindParam(1, $type);
        $prep->bindParam(2, $prix);
        $prep->bindParam(3, $id);
        $prep->execute();
        return $this->get($id);//$request_data;//$this->dp->update($id, $this->_validate($request_data));
    }
    function delete($id)
    {
        //nous verifions avant si l'templacement existe
        $retour = $this->get($id);
        if (!$retour) {
            return false;
        } else {
            try {
                $req="delete from type_emplacements where id=?;";
                $prep =$this->bd->prepare($req);
                $prep->bindParam(1, $id);
                //on exécute la requête sql
                $prep->execute();
            } catch (PDOException $e) {
                return false;
                die();
            }
        }
           return $retour;
    }
    private function _validation($data)
    {
          //  $templacement = array();
        foreach (templacement::$CHAMPS as $champ) {
    //on commence par valider les données reçues
            if (!isset($data[$champ])) {
                throw new RestException(400, "$champ field missing");
            }
        }
        // on construit un $tabtemplacement par rapport aux données reçues
        foreach (templacement::$CHAMPS as $champ) {
            $tabtemplacement[$champ]=htmlentities($data[$champ]);
        }
        return $tabtemplacement;
    }
    private function _rempli($id, $data)
    {
        $templacement=$this->get($id);
        // on transforme l'objet templacement en tableau
        $tabtemplacement=get_object_vars($templacement);
          //  $templacement = array();
        foreach (templacement::$CHAMPS as $champ) {
    //on commence rempli le reste du tableau par les champs reçus
            if (isset($data[$champ])) {
                $tabtemplacement[$champ]=htmlentities($data[$champ]);
            }
        }
        return $tabtemplacement;
    }
}
