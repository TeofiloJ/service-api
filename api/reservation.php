<?php
use Luracast\Restler\RestException;

require_once("./cfg/localData.php");
class reservation
{
    private $db;
    static $CHAMPS = array('id_client', 'date_debut', 'date_fin');
    function __construct()
    {
        $this->db = new PDO(DNS, USER, MDP);
    }
    function head()
    {
        echo "ici";
        $tab[]="id";
        foreach (reservation::$CHAMPS as $champ) {
      //on indique tous les champs attendus pour la table reservation
            $tab[]=$champ;
        }
        echo "ici";
        return "toto";
    }
    function get($id = null)
    {
		$id=htmlentities($id);
        if ($id != null) {
            $req="select * from reservation where id=?";
            $prep=$this->db->prepare($req);
            $prep->bindParam(1, $id);
            $prep->execute();
            if (empty($reservation = $prep->fetchObject()) && $id != null) {
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
            $reservation->id=intval($reservation->id);
            $retour=$reservation;
        } else {
            $req="select * from reservation";
            $resultat = $this->db->query($req);
            $indiv=new reservation();
            while ($reservation=$resultat->fetchObject()) {
                $reservation->id=intval($reservation->id);
                $retour[]=$reservation;
            }
        }
        return $retour;
    }
    function post($request_data = null)
    {
        $reservation=$this->_validation($request_data);
        $req="INSERT INTO reservation (id_client, date_debut, date_fin) VALUES (?,?,?)";
        $prep=$this->db->prepare($req);
        $id_client=$reservation["id_client"];//
        $date_debut=$reservation["date_debut"];
        $date_fin=$reservation["date_fin"];
        $prep->bindParam(1, $id_client);
        $prep->bindParam(2, $date_debut);
        $prep->bindParam(3, $date_fin);
        $prep->execute();
        return $this->get($this->db->lastInsertId());
    }
    //modification partielle
    function patch($id, $request_data = null)
    {
        $reservation=$this->_rempli($id, $request_data);
        $reservation=$this->_validation($request_data);
        $req="update reservation set id_client=?, date_debut=?, date_fin=? where id=?";
        $id_client=$reservation["id_client"];//
        $date_debut=$reservation["date_debut"];
        $date_fin=$reservation["date_fin"];
        
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
        $reservation=$this->_validation($request_data);
    //$reservation=$request_data;//$this->_rempli($request_data);
        $req="update reservation set id_client=?, date_debut=?, date_fin=? where id=?";
        $id_client=$reservation["id_client"];//
        $date_debut=$reservation["date_debut"];
        $date_fin=$reservation["date_fin"];
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
        //nous verifions avant si l'reservation existe
        $retour = $this->get($id);
        if (!$retour) {
            return false;
        } else {
            try {
                $req="delete from reservation where id=?;";
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
          //  $reservation = array();
        foreach (reservation::$CHAMPS as $champ) {
    //on commence par valider les données reçues
            if (!isset($data[$champ])) {
                throw new RestException(400, "$champ field missing");
            }
        }
        // on construit un $tabreservation par rapport aux données reçues
        foreach (reservation::$CHAMPS as $champ) {
            $tabreservation[$champ]=htmlentities($data[$champ]);
        }
        return $tabreservation;
    }
    private function _rempli($id, $data)
    {
        $reservation=$this->get($id);
        // on transforme l'objet reservation en tableau
        $tabreservation=get_object_vars($reservation);
          //  $reservation = array();
        foreach (reservation::$CHAMPS as $champ) {
    //on commence rempli le reste du tableau par les champs reçus
            if (isset($data[$champ])) {
                $tabreservation[$champ]=htmlentities($data[$champ]);
            }
        }
        return $tabreservation;
    }
}
