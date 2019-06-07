<?php
use Luracast\Restler\RestException;

require_once("./cfg/localData.php");
class resactivites
{
    private $db;
    static $CHAMPS = array('id_reservation','date', 'id_sport', 'nb_unit', 'prix_unit');
    function __construct()
    {
        $this->db = new PDO(DNS, USER, MDP);
    }
    function head()
    {
        echo "ici";
        $tab[]="id";
        foreach (reservations_activites::$CHAMPS as $champ) {
      //on indique tous les champs attendus pour la table reservations_activites
            $tab[]=$champ;
        }
        echo "ici";
        return "toto";
    }
    function get($id = null)
    {
		$id=htmlentities($id);
        if ($id != null) {
            $req="select * from reservations_activites where id=?";
            $prep=$this->db->prepare($req);
            $prep->bindParam(1, $id);
            $prep->execute();
            if (empty($reservations_activites = $prep->fetchObject()) && $id != null) {
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
            $reservations_activites->id=intval($reservations_activites->id);
            $retour=$reservations_activites;
        } else {
            $req="select * from reservations_activites";
            $resultat = $this->db->query($req);
            $indiv=new resactivites();
            while ($reservations_activites=$resultat->fetchObject()) {
                $reservations_activites->id=intval($reservations_activites->id);
                $retour[]=$reservations_activites;
            }
        }
        return $retour;
    }
    function post($request_data = null)
    {
        $reservations_activites=$this->_validation($request_data);
        $req="INSERT INTO reservations_activites (id_reservation, date, id_sport, nb_unit, prix_unit) VALUES (?,?,?,?,?)";
        $prep=$this->db->prepare($req);
        $id_reservation=$reservations_activites["id_reservation"];//
        $date=$reservations_activites["date"];
        $id_sport=$reservations_activites["id_sport"];
        $nb_unit=$reservations_activites["nb_unit"];
        $prix_unit=$reservations_activites["prix_unit"];

        $prep->bindParam(1, $id_reservation);
        $prep->bindParam(2, $date);
        $prep->bindParam(3, $id_sport);
        $prep->bindParam(4, $nb_unit);
        $prep->bindParam(5, $prix_unit);
        $prep->execute();
        return $this->get($this->db->lastInsertId());
    }
    //modification partielle
    function patch($id, $request_data = null)
    {
        $reservations_activites=$this->_rempli($id, $request_data);
    //$reservations_activites=$this->_validation($request_data);
        $req="update reservations_activites set id_reservation=?, date=?, id_sport=?, nb_unit=?, prix_unit=? where id=?";
        $prep=$this->db->prepare($req);
        $id_reservation=$reservations_activites["id_reservation"];//
        $date=$reservations_activites["date"];
        $id_sport=$reservations_activites["id_sport"];
        $nb_unit=$reservations_activites["nb_unit"];
        $prix_unit=$reservations_activites["prix_unit"];

        $prep->bindParam(1, $id_reservation);
        $prep->bindParam(2, $date);
        $prep->bindParam(3, $id_sport);
        $prep->bindParam(4, $nb_unit);
        $prep->bindParam(5, $prix_unit);

        $prep->bindParam(7, $id);
        $prep->execute();
        return $this->get($id);//$request_data;//$this->dp->update($id, $this->_validate($request_data));
    }

    function delete($id)
    {
        //nous verifions avant si l'reservations_activites existe
        $retour = $this->get($id);
        if (!$retour) {
            return false;
        } else {
            try {
                $req="delete from reservations_activites where id=?;";
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
          //  $reservations_activites = array();
        foreach (resactivites::$CHAMPS as $champ) {
    //on commence par valider les données reçues
            if (!isset($data[$champ])) {
                throw new RestException(400, "$champ field missing");
            }
        }
        // on construit un $tabreservations_activites par rapport aux données reçues
        foreach (resactivites::$CHAMPS as $champ) {
            $tabreservations_activites[$champ]=htmlentities($data[$champ]);
        }
        return $tabreservations_activites;
    }
    private function _rempli($id, $data)
    {
        $reservations_activites=$this->get($id);
        // on transforme l'objet reservations_activites en tableau
        $tabreservations_activites=get_object_vars($reservations_activites);
          //  $reservations_activites = array();
        foreach (reservations_activites::$CHAMPS as $champ) {
    //on commence rempli le reste du tableau par les champs reçus
            if (isset($data[$champ])) {
                $tabreservations_activites[$champ]=htmlentities($data[$champ]);
            }
        }
        return $tabreservations_activites;
    }
}
