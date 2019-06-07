<?php
use Luracast\Restler\RestException;

require_once("./cfg/localData.php");
class activites
{
    private $bd;
    static $CHAMPS = array('tarif','nom', 'duree');
    function __construct()
    {
        $this->bd = new PDO(DNS, USER, MDP);
    }
    function head()
    {
        echo "ici";
        $tab[]="id";
        foreach (activites::$CHAMPS as $champ) {
      //on indique tous les champs attendus pour la table activites
            $tab[]=$champ;
        }
        echo "ici";
        return "toto";
    }
    function get($id = null)
    {
		$id=htmlentities($id);
        if ($id != null) {
            $req="select * from activites where id=?";
            $prep=$this->bd->prepare($req);
            $prep->bindParam(1, $id);
            $prep->execute();
            if (empty($activites = $prep->fetchObject()) && $id != null) {
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
            $activites->id=intval($activites->id);
            $retour=$activites;
        } else {
            $req="select * from activites";
            $resultat = $this->bd->query($req);
            $indiv=new activites();
            while ($activites=$resultat->fetchObject()) {
                $activites->id=intval($activites->id);
                $retour[]=$activites;
            }
        }
        return $retour;
    }
    function post($request_data = null)
    {
        $activites=$this->_validation($request_data);
        $req="INSERT INTO activites (tarif, nom, duree) VALUES (?,?,?)";
        $prep=$this->bd->prepare($req);
        $tarif=$activites["tarif"];//
        $nom=$activites["nom"];
        $duree=$activites["duree"];
        $prep->bindParam(1, $tarif);
        $prep->bindParam(2, $nom);
        $prep->bindParam(3, $duree);
        $prep->execute();
        return $this->get($this->bd->lastInsertId());
    }
    //modification partielle
    function patch($id, $request_data = null)
    {
        $activites=$this->_rempli($id, $request_data);
    //$activites=$this->_validation($request_data);
        $req="update activites set tarif=?, nom=?, duree=? where id=?";
        $tarif=$activites["tarif"];//
        $nom=$activites["nom"];
        $duree=$activites["duree"];
        $prep=$this->bd->prepare($req);
        $prep->bindParam(1, $tarif);
        $prep->bindParam(2, $nom);
        $prep->bindParam(3, $duree);
        $prep->bindParam(4, $id);
        $prep->execute();
        return $this->get($id);//$request_data;//$this->dp->update($id, $this->_validate($request_data));
    }
    function put($id, $request_data = null)
    {
        $activites=$this->_validation($request_data);
    //$activites=$request_data;//$this->_rempli($request_data);
        $req="update activites set tarif=?, nom=?, duree=? where id=?";
        $tarif=$activites["tarif"];//
        $nom=$activites["nom"];
        $duree=$activites["duree"];
        $prep=$this->bd->prepare($req);
        $prep->bindParam(1, $tarif);
        $prep->bindParam(2, $nom);
        $prep->bindParam(3, $duree);
        $prep->bindParam(4, $id);
        $prep->execute();
        return $this->get($id);//$request_data;//$this->dp->update($id, $this->_validate($request_data));
    }
    function delete($id)
    {
        //nous verifions avant si l'activites existe
        $retour = $this->get($id);
        if (!$retour) {
            return false;
        } else {
            try {
                $req="delete from activites where id=?;";
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
          //  $activites = array();
        foreach (activites::$CHAMPS as $champ) {
    //on commence par valider les données reçues
            if (!isset($data[$champ])) {
                throw new RestException(400, "$champ field missing");
            }
        }
        // on construit un $tabactivites par rapport aux données reçues
        foreach (activites::$CHAMPS as $champ) {
            $tabactivites[$champ]=htmlentities($data[$champ]);
        }
        return $tabactivites;
    }
    private function _rempli($id, $data)
    {
        $activites=$this->get($id);
        // on transforme l'objet activites en tableau
        $tabactivites=get_object_vars($activites);
          //  $activites = array();
        foreach (activites::$CHAMPS as $champ) {
    //on commence rempli le reste du tableau par les champs reçus
            if (isset($data[$champ])) {
                $tabactivites[$champ]=htmlentities($data[$champ]);
            }
        }
        return $tabactivites;
    }
}
