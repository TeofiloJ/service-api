<?php
use Luracast\Restler\RestException;

require_once("./cfg/localData.php");
class emplacements
{
    private $bd;
    static $CHAMPS = array('id_type','occupe', 'surface', 'nb_max');
    function __construct()
    {
        $this->bd = new PDO(DNS, USER, MDP);
    }
    function head()
    {
        echo "ici";
        $tab[]="id";
        foreach (emplacements::$CHAMPS as $champ) {
      //on indique tous les champs attendus pour la table emplacements
            $tab[]=$champ;
        }
        echo "ici";
        return "toto";
    }
    function get($id = null)
    {
		$id=htmlentities($id);
        if ($id != null) {
            $req="select * from emplacements where id=?";
            $prep=$this->bd->prepare($req);
            $prep->bindParam(1, $id);
            $prep->execute();
            if (empty($emplacements = $prep->fetchObject()) && $id != null) {
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
            $emplacements->id=intval($emplacements->id);
            $retour=$emplacements;
        } else {
            $req="select * from emplacements";
            $resultat = $this->bd->query($req);
            $indiv=new emplacements();
            while ($emplacements=$resultat->fetchObject()) {
                $emplacements->id=intval($emplacements->id);
                $retour[]=$emplacements;
            }
        }
        return $retour;
    }
    function post($request_data = null)
    {
        $emplacements=$this->_validation($request_data);
        $req="INSERT INTO emplacements (id_type, occupe, surface, nb_max) VALUES (?,?,?,?)";
        $prep=$this->bd->prepare($req);
        $id_type=$emplacements["id_type"];//
        $occupe=$emplacements["occupe"];
        $surface=$emplacements["surface"];
        $nb_max=$emplacements["nb_max"];
        $prep->bindParam(1, $id_type);
        $prep->bindParam(2, $occupe);
        $prep->bindParam(3, $surface);
        $prep->bindParam(4, $nb_max);
        $prep->execute();
        return $this->get($this->bd->lastInsertId());
    }
    //modification partielle
    function patch($id, $request_data = null)
    {
        $emplacements=$this->_rempli($id, $request_data);
    //$emplacements=$this->_validation($request_data);
        $req="update emplacements set id_type=?, occupe=?, surface=?, nb_max=? where id=?";
        $id_type=$emplacements["id_type"];//
        $occupe=$emplacements["occupe"];
        $surface=$emplacements["surface"];
        $nb_max=$emplacements["nb_max"];
        $prep=$this->bd->prepare($req);
        $prep->bindParam(1, $id_type);
        $prep->bindParam(2, $occupe);
        $prep->bindParam(3, $surface);
        $prep->bindParam(4, $nb_max);
        $prep->bindParam(5, $id);
        $prep->execute();
        return $this->get($id);//$request_data;//$this->dp->update($id, $this->_validate($request_data));
    }
    function put($id, $request_data = null)
    {
        $emplacements=$this->_validation($request_data);
    //$emplacements=$request_data;//$this->_rempli($request_data);
        $req="update emplacements set id_type=?, occupe=?, surface=?, nb_max=? where id=?";
        $id_type=$emplacements["id_type"];//
        $occupe=$emplacements["occupe"];
        $surface=$emplacements["surface"];
        $nb_max=$emplacements["nb_max"];
        $prep=$this->bd->prepare($req);
        $prep->bindParam(1, $id_type);
        $prep->bindParam(2, $occupe);
        $prep->bindParam(3, $surface);
        $prep->bindParam(4, $nb_max);
        $prep->bindParam(5, $id);
        $prep->execute();
        return $this->get($id);//$request_data;//$this->dp->update($id, $this->_validate($request_data));
    }
    function delete($id)
    {
        //nous verifions avant si l'emplacements existe
        $retour = $this->get($id);
        if (!$retour) {
            return false;
        } else {
            try {
                $req="delete from emplacements where id=?;";
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
          //  $emplacements = array();
        foreach (emplacements::$CHAMPS as $champ) {
    //on commence par valider les données reçues
            if (!isset($data[$champ])) {
                throw new RestException(400, "$champ field missing");
            }
        }
        // on construit un $tabemplacements par rapport aux données reçues
        foreach (emplacements::$CHAMPS as $champ) {
            $tabemplacements[$champ]=htmlentities($data[$champ]);
        }
        return $tabemplacements;
    }
    private function _rempli($id, $data)
    {
        $emplacements=$this->get($id);
        // on transforme l'objet emplacements en tableau
        $tabemplacements=get_object_vars($emplacements);
          //  $emplacements = array();
        foreach (emplacements::$CHAMPS as $champ) {
    //on commence rempli le reste du tableau par les champs reçus
            if (isset($data[$champ])) {
                $tabemplacements[$champ]=htmlentities($data[$champ]);
            }
        }
        return $tabemplacements;
    }
}
