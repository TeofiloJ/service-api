<?php
use Luracast\Restler\RestException;

require_once("./cfg/localData.php");
class resemplacements
{
    private $db;
    static $CHAMPS = array('id_reservation','id_emplacement', 'nb_personnes', 'prix', 'date_debut', 'date_fin');
    function __construct()
    {
        $this->db = new PDO(DNS, USER, MDP);
    }
    function head()
    {
        echo "ici";
        $tab[]="id";
        foreach (reservations_emplacements::$CHAMPS as $champ) {
      //on indique tous les champs attendus pour la table reservations_emplacements
            $tab[]=$champ;
        }
        echo "ici";
        return "toto";
    }
    function get($id = null)
    {
		$id=htmlentities($id);
        if ($id != null) {
            $req="select * from reservations_emplacements where id=?";
            $prep=$this->db->prepare($req);
            $prep->bindParam(1, $id);
            $prep->execute();
            if (empty($reservations_emplacements = $prep->fetchObject()) && $id != null) {
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
            $reservations_emplacements->id=intval($reservations_emplacements->id);
            $retour=$reservations_emplacements;
        } else {
            $req="select * from reservations_emplacements";
            $resultat = $this->db->query($req);
            $indiv=new resemplacements();
            while ($reservations_emplacements=$resultat->fetchObject()) {
                $reservations_emplacements->id=intval($reservations_emplacements->id);
                $retour[]=$reservations_emplacements;
            }
        }
        return $retour;
    }
    function post($request_data = null)
    {
        $reservations_emplacements=$this->_validation($request_data);
        $req="INSERT INTO reservations_emplacements (id_reservation, id_emplacement, nb_personnes, prix, date_debut, date_fin) VALUES (?,?,?,?,?,?)";
        $prep=$this->db->prepare($req);
        $id_reservation=$reservations_emplacements["id_reservation"];//
        $id_emplacement=$reservations_emplacements["id_emplacement"];
        $nb_personnes=$reservations_emplacements["nb_personnes"];
        $prix=$reservations_emplacements["prix"];
        $date_debut=$reservations_emplacements["date_debut"];
        $date_fin=$reservations_emplacements["date_fin"];
        $prep->bindParam(1, $id_reservation);
        $prep->bindParam(2, $id_emplacement);
        $prep->bindParam(3, $nb_personnes);
        $prep->bindParam(4, $prix);
        $prep->bindParam(5, $date_debut);
        $prep->bindParam(6, $date_fin);
        $prep->execute();
        return $this->get($this->db->lastInsertId());
    }
    //modification partielle
    function patch($id, $request_data = null)
    {
        $reservations_emplacements=$this->_rempli($id, $request_data);
    //$reservations_emplacements=$this->_validation($request_data);
        $req="update reservations_emplacements set id_reservation=?, id_emplacement=?, nb_personnes=?, prix=?, date_debut=?, date_fin=? where id=?";
        $prep=$this->db->prepare($req);
        $id_reservation=$reservations_emplacements["id_reservation"];//
        $id_emplacement=$reservations_emplacements["id_emplacement"];
        $nb_personnes=$reservations_emplacements["nb_personnes"];
        $prix=$reservations_emplacements["prix"];
        $date_debut=$reservations_emplacements["date_debut"];
        $date_fin=$reservations_emplacements["date_fin"];
        $prep->bindParam(1, $id_reservation);
        $prep->bindParam(2, $id_emplacement);
        $prep->bindParam(3, $nb_personnes);
        $prep->bindParam(4, $prix);
        $prep->bindParam(5, $date_debut);
        $prep->bindParam(6, $date_fin);
        $prep->bindParam(7, $id);
        $prep->execute();
        return $this->get($id);//$request_data;//$this->dp->update($id, $this->_validate($request_data));
    }

    function delete($id)
    {
        //nous verifions avant si l'reservations_emplacements existe
        $retour = $this->get($id);
        if (!$retour) {
            return false;
        } else {
            try {
                $req="delete from reservations_emplacements where id=?;";
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
          //  $reservations_emplacements = array();
        foreach (resemplacements::$CHAMPS as $champ) {
    //on commence par valider les données reçues
            if (!isset($data[$champ])) {
                throw new RestException(400, "$champ field missing");
            }
        }
        // on construit un $tabreservations_emplacements par rapport aux données reçues
        foreach (resemplacements::$CHAMPS as $champ) {
            $tabreservations_emplacements[$champ]=htmlentities($data[$champ]);
        }
        return $tabreservations_emplacements;
    }
    private function _rempli($id, $data)
    {
        $reservations_emplacements=$this->get($id);
        // on transforme l'objet reservations_emplacements en tableau
        $tabreservations_emplacements=get_object_vars($reservations_emplacements);
          //  $reservations_emplacements = array();
        foreach (reservations_emplacements::$CHAMPS as $champ) {
    //on commence rempli le reste du tableau par les champs reçus
            if (isset($data[$champ])) {
                $tabreservations_emplacements[$champ]=htmlentities($data[$champ]);
            }
        }
        return $tabreservations_emplacements;
    }
}
