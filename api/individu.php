<?php
use Luracast\Restler\RestException;

require_once("./cfg/localData.php");
class individu
{
    private $bd;
    static $CHAMPS = array('prenom','nom', 'email');
    function __construct()
    {
        $this->bd = new PDO(DNS, USER, MDP);
    }
    function head()
    {
        echo "ici";
        $tab[]="id";
        foreach (individu::$CHAMPS as $champ) {
      //on indique tous les champs attendus pour la table individu
            $tab[]=$champ;
        }
        echo "ici";
        return "toto";
    }
    function get($id = null)
    {
		$id=htmlentities($id);
        if ($id != null) {
            $req="select * from Individu where id=?";
            $prep=$this->bd->prepare($req);
            $prep->bindParam(1, $id);
            $prep->execute();
            if (empty($individu = $prep->fetchObject()) && $id != null) {
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
            $individu->id=intval($individu->id);
            $retour=$individu;
        } else {
            $req="select * from Individu";
            $resultat = $this->bd->query($req);
            $indiv=new Individu();
            while ($individu=$resultat->fetchObject()) {
                $individu->id=intval($individu->id);
                $retour[]=$individu;
            }
        }
        return $retour;
    }
    function post($request_data = null)
    {
        var_dump($request_data);
        $individu=$this->_validation($request_data);
        $req="INSERT INTO Individu (prenom, nom, email) VALUES (?,?,?)";
        $prep=$this->bd->prepare($req);
        $prenom=$individu["prenom"];//
        $nom=$individu["nom"];
        $email=$individu["email"];
        $prep->bindParam(1, $prenom);
        $prep->bindParam(2, $nom);
        $prep->bindParam(3, $email);
        $prep->execute();
        return $this->get($this->bd->lastInsertId());
    }
    //modification partielle
    function patch($id, $request_data = null)
    {
        $individu=$this->_rempli($id, $request_data);
    //$individu=$this->_validation($request_data);
        $req="update Individu set prenom=?, nom=?, email=? where id=?";
        $prenom=$individu["prenom"];//
        $nom=$individu["nom"];
        $email=$individu["email"];
        $prep=$this->bd->prepare($req);
        $prep->bindParam(1, $prenom);
        $prep->bindParam(2, $nom);
        $prep->bindParam(3, $email);
        $prep->bindParam(4, $id);
        $prep->execute();
        return $this->get($id);//$request_data;//$this->dp->update($id, $this->_validate($request_data));
    }
    function put($id, $request_data = null)
    {
        $individu=$this->_validation($request_data);
    //$individu=$request_data;//$this->_rempli($request_data);
        $req="update Individu set prenom=?, nom=?, email=? where id=?";
        $prenom=$individu["prenom"];//
        $nom=$individu["nom"];
        $email=$individu["email"];
        $prep=$this->bd->prepare($req);
        $prep->bindParam(1, $prenom);
        $prep->bindParam(2, $nom);
        $prep->bindParam(3, $email);
        $prep->bindParam(4, $id);
        $prep->execute();
        return $this->get($id);//$request_data;//$this->dp->update($id, $this->_validate($request_data));
    }
    function delete($id)
    {
        //nous verifions avant si l'individu existe
        $retour = $this->get($id);
        if (!$retour) {
            return false;
        } else {
            try {
                $req="delete from Individu where id=?;";
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
          //  $individu = array();
        foreach (individu::$CHAMPS as $champ) {
    //on commence par valider les données reçues
            if (!isset($data[$champ])) {
                throw new RestException(400, "$champ field missing");
            }
        }
        // on construit un $tabIndividu par rapport aux données reçues
        foreach (individu::$CHAMPS as $champ) {
            $tabIndividu[$champ]=htmlentities($data[$champ]);
        }
        return $tabIndividu;
    }
    private function _rempli($id, $data)
    {
        $individu=$this->get($id);
        // on transforme l'objet individu en tableau
        $tabIndividu=get_object_vars($individu);
          //  $individu = array();
        foreach (individu::$CHAMPS as $champ) {
    //on commence rempli le reste du tableau par les champs reçus
            if (isset($data[$champ])) {
                $tabIndividu[$champ]=htmlentities($data[$champ]);
            }
        }
        return $tabIndividu;
    }
}
