<?php
use Luracast\Restler\RestException;

require_once("localData.php");
class facture
{
    private $db;
    static $CHAMPS = array('id','id_client', 'date_debut', 'date_fin');
    function __construct()
    {
        $this->db = new PDO(DNS, USER, MDP);
    }
    function head()
    {
        echo "ici";
        $tab[]="id";
        foreach (facture::$CHAMPS as $champ) {
      //on indique tous les champs attendus pour la table facture
            $tab[]=$champ;
        }
        echo "ici";
        return "toto";
    }
    function get($id = null)
    {
		$id=htmlentities($id);
        if ($id != null) {
            $req="select * from facture where id=?";
            $prep=$this->db->prepare($req);
            $prep->bindParam(1, $id);
            $prep->execute();
            if (empty($facture = $prep->fetchObject()) && $id != null) {
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
            $facture->id=intval($facture->id);
            $retour=$facture;
        } else {
            $req="select * from facture";
            $resultat = $this->db->query($req);
            $indiv=new facture();
            while ($facture=$resultat->fetchObject()) {
                $facture->id=intval($facture->id);
                $retour[]=$facture;
            }
        }
        return $retour;
    }
    function post($request_data = null)
    {
        $facture=$this->_validation($request_data);
        $req="INSERT INTO facture (id_client, date_debut, date_fin) VALUES (?,?,?)";
        $prep=$this->db->prepare($req);
        $id_client=$facture["id_client"];//
        $date_debut=$facture["date_debut"];
        $date_fin=$facture["date_fin"];
        $prep->bindParam(1, $id_client);
        $prep->bindParam(2, $date_debut);
        $prep->bindParam(3, $date_fin);
        $prep->execute();
        return $this->get($this->db->lastInsertId());
    }
    //modification partielle
    function patch($id, $request_data = null)
    {
        $facture=$this->_rempli($id, $request_data);
        $facture=$this->_validation($request_data);
        $req="update facture set id_client=?, date_debut=?, date_fin=? where id=?";
        $id_client=$facture["id_client"];//
        $date_debut=$facture["date_debut"];
        $date_fin=$facture["date_fin"];
        
        $prep=$this->db->prepare($req);
        $prep->bindParam(1, $id_client);
        $prep->bindParam(2, $date_debut);
        $prep->bindParam(3, $date_fin);
        $prep->bindParam(4, $id);
        $prep->execute();
        return $this->get($id);//$request_data;//$this->dp->update($id, $this->_validate($request_data));
    }
    function put($id, $request_data = null)
    {
        $facture=$this->_validation($request_data);
    //$facture=$request_data;//$this->_rempli($request_data);
        $req="update facture set id_client=?, date_debut=?, date_fin=? where id=?";
        $id_client=$facture["id_client"];//
        $date_debut=$facture["date_debut"];
        $date_fin=$facture["date_fin"];
        $prep=$this->db->prepare($req);
        $prep->bindParam(1, $id_client);
        $prep->bindParam(2, $date_debut);
        $prep->bindParam(3, $date_fin);
        $prep->bindParam(4, $id);
        $prep->execute();
        return $this->get($id);//$request_data;//$this->dp->update($id, $this->_validate($request_data));
    }
    function delete($id)
    {
        //nous verifions avant si l'facture existe
        $retour = $this->get($id);
        if (!$retour) {
            return false;
        } else {
            try {
                $req="delete from facture where id=?;";
                $prep =$this->db->prepare($req);
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
          //  $facture = array();
        foreach (facture::$CHAMPS as $champ) {
    //on commence par valider les données reçues
            if (!isset($data[$champ])) {
                throw new RestException(400, "$champ field missing");
            }
        }
        // on construit un $tabfacture par rapport aux données reçues
        foreach (facture::$CHAMPS as $champ) {
            $tabfacture[$champ]=htmlentities($data[$champ]);
        }
        return $tabfacture;
    }
    private function _rempli($id, $data)
    {
        $facture=$this->get($id);
        // on transforme l'objet facture en tableau
        $tabfacture=get_object_vars($facture);
          //  $facture = array();
        foreach (facture::$CHAMPS as $champ) {
    //on commence rempli le reste du tableau par les champs reçus
            if (isset($data[$champ])) {
                $tabfacture[$champ]=htmlentities($data[$champ]);
            }
        }
        return $tabfacture;
    }
}
