<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Roles extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function obtener($id = false)
    {
        $this->db->where('eliminado', 0);
        $query = $this->db->get('seg.roles');
        $res = $query->result();
        
        $list = [];
        foreach ($res as $o) {
            $list[$o->rol_id] = $o->nombre;
        }

        return $list;
    }

    public function guardar($data)
    {
        return $this->db->insert('seg.roles', $data);
		}
		

		//_________________ funciones para BPM ________________
		
		/**
		* Trae grupos de BPM 
		* @param 
		* @return array con grupos de BPM
		*/
		public function getBpmGroups(){

			//TODO: deshardcodear
				$token = 123;
				$aux = $this->rest->callAPI("GET",REST_BPM."/groups/".$token);
				$aux =json_decode($aux["data"]);
				return $aux->payload;
		}

		/**
		* Trae roles de BPM
		* @param
		* @return array con Roles de BPM
		*/
		function getBpmRoles(){
				//TODO: deshardcodear
				$token = 123;
				$aux = $this->rest->callAPI("GET",REST_BPM."/roles/".$token);
				$aux =json_decode($aux["data"]);
				return $aux->payload;
		}

		/**
		* Devuelve eliminacion de  un usuario de Rol
		* @param
		* @return array con depositos por establecimiento
		*/
		function deleteMembershipBPM($membershipBPM, $userNick){

			// trae info de usuario en BPM
			$info_bpm = $this->getInfoBPM($userNick);
			log_message('DEBUG','#TRAZA|ROLES|deleteMembershipBPM($membershipBPM, $userNick): $info_bpm >> '.json_encode($info_bpm));

			if ($info_bpm === null || !isset($info_bpm->id)) {
				log_message('ERROR', '#TRAZA | #TRAZ-COMP-DNATO | ROLES | deleteMembershipBPM >> Usuario no encontrado en BPM: ' . $userNick);
				return null;
			}

			$session = defined('BPM_ROLES_SESSION') ? BPM_ROLES_SESSION : '"X-Bonita-API-Token=658fcd51-ef8b-48c3-9606-1d89a88cf3e5;JSESSIONID=BCDEA4A05749709F4DFBDCBB58A527E8;bonita.tenant=1;"';
			$datos["user_id"] = $info_bpm->id; // id de usuario en bpm
			$datos["group_id"] = $membershipBPM['group_id'];
			$datos["role_id"] =  $membershipBPM['role_id'];
			$datos["session"] = $session;
			$post["payload"] = $datos;

			//log_message('DEBUG','#TRAZA|ROLES|deleteMembershipBPM($membershipBPM, $userNick): $post >> '.json_encode($datos));
			
			$resource = '/membership';
			$url = REST_BPM . $resource;
			$aux = $this->rest->callAPI("DELETE", $url, $datos);
			$aux = json_decode($aux["data"]);
			return $aux;

		}

		/**
		* Asigna membership a usuarios en BPM
		* @param array con datos de usr
		* @return string stats de respuesta del servicio
		*/
		function guardarMembershipBPM($membershipBPM, $userNick){
			// trae info de usuario en BPM
			$info_bpm = $this->getInfoBPM($userNick);
			log_message('DEBUG','#TRAZA | #TRAZ-COMP-DNATO | ROLES | guardarMembershipBPM($membershipBPM, $userNick): $info_bpm >> '.json_encode($info_bpm));

			if ($info_bpm === null || !isset($info_bpm->id)) {
				log_message('ERROR', '#TRAZA | #TRAZ-COMP-DNATO | ROLES | guardarMembershipBPM >> Usuario no encontrado en BPM: ' . $userNick);
				return null;
			}

			$session = defined('BPM_ROLES_SESSION') ? BPM_ROLES_SESSION : '"X-Bonita-API-Token=658fcd51-ef8b-48c3-9606-1d89a88cf3e5;JSESSIONID=BCDEA4A05749709F4DFBDCBB58A527E8;bonita.tenant=1;"';
			$datos["user_id"] = $info_bpm->id; // id de usuario en bpm
			$datos["group_id"] = $membershipBPM['group_id'];
			$datos["role_id"] =  $membershipBPM['role_id'];
			$post["session"] = $session;
			$post["payload"] = $datos;

			$result = $this->rest->callAPI("POST", REST_BPM."/memberships", $post);

			if (!$result || !isset($result['status']) || $result['status'] === false || (isset($result['code']) && $result['code'] >= 300)) {
				$httpCode = isset($result['code']) ? $result['code'] : 'N/A';
				$body = isset($result['data']) ? substr($result['data'], 0, 500) : 'sin respuesta';
				log_message('ERROR', '#TRAZA | #TRAZ-COMP-DNATO | ROLES | guardarMembershipBPM >> API falló. HTTP: ' . $httpCode . ' | body: ' . $body);
				return null;
			}

			$aux = json_decode($result["data"]);
			return $aux;
		}

        /**
		* Traer info de usuario de BPM
		* @param string $usrNick Nombre de usuario en BPM
		* @return object|null Objeto con id del usuario en BPM, o null si no existe o hay error
		*/
		function getInfoBPM($usrNick){
			log_message('DEBUG',"#TRAZA | #TRAZ-COMP-DNATO | ROLES | getInfoBPM($usrNick)");
			$session = defined('BPM_ROLES_SESSION_URL') ? BPM_ROLES_SESSION_URL : rawurlencode('X-Bonita-API-Token=658fcd51-ef8b-48c3-9606-1d89a88cf3e5;JSESSIONID=BCDEA4A05749709F4DFBDCBB58A527E8;bonita.tenant=1;');
			$result = $this->rest->callAPI("GET", REST_BPM."/users/".urlencode($usrNick)."/session/".$session);

			if (!$result || !isset($result['status']) || $result['status'] === false || (isset($result['code']) && $result['code'] >= 300)) {
				$httpCode = isset($result['code']) ? $result['code'] : 'N/A';
				log_message('ERROR', '#TRAZA | #TRAZ-COMP-DNATO | ROLES | getInfoBPM >> API falló. HTTP: ' . $httpCode . ' | usrNick: ' . $usrNick);
				return null;
			}

			$aux = json_decode($result["data"]);
			if (!$aux || !isset($aux->payload) || !is_array($aux->payload) || empty($aux->payload)) {
				log_message('ERROR', '#TRAZA | #TRAZ-COMP-DNATO | ROLES | getInfoBPM >> Usuario no encontrado en BPM. usrNick: ' . $usrNick);
				return null;
			}

			return $aux->payload[0];
		}

		/**
		* Devuelve depositos para asignar a usuarios de Rol deposito
		* REPENSAR EN V2.0 - RRUIZ
		* @param
		* @return array con depositos por establecimiento
		*/
/*		function obtenerDepositos()
		{
			//TODO: DESHARDCODEAR ESTABLECIMIENTO
			$esta_id = 1;
			log_message('INFO','#TRAZA|ROLES|obtenerDepositos() >> ');
			$aux = $this->rest->callAPI("GET",REST."/depositos/establecimiento/".$esta_id);
			$aux =json_decode($aux["data"]);
			return $aux->depositos->deposito;
		}
*/
		
}