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

			//log_message('DEBUG','#TRAZA|ROLES|deleteMembershipBPM($membershipBPM, $userNick): $membershipBPM >> '.json_encode($membershipBPM));
			//log_message('DEBUG','#TRAZA|ROLES|deleteMembershipBPM($membershipBPM, $userNick): $userNick >> '.json_encode($userNick));

			// trae info de usuario en BPM
			$info_bpm = $this->getInfoBPM($userNick);
			log_message('DEBUG','#TRAZA|ROLES|deleteMembershipBPM($membershipBPM, $userNick): $info_bpm >> '.json_encode($info_bpm));

			//TODO: SACAR HARDCODEO ACA
			$session = '"X-Bonita-API-Token=658fcd51-ef8b-48c3-9606-1d89a88cf3e5;JSESSIONID=BCDEA4A05749709F4DFBDCBB58A527E8;bonita.tenant=1;"';
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

			//log_message('DEBUG','#TRAZA|ROLES|guardarMembershipBPM($membershipBPM, $userNick): $membershipBPM >> '.json_encode($membershipBPM));
			//log_message('DEBUG','#TRAZA|ROLES|guardarMembershipBPM($membershipBPM, $userNick): $userNick >> '.json_encode($userNick));

			// trae info de usuario en BPM
			$info_bpm = $this->getInfoBPM($userNick);
			log_message('DEBUG','#TRAZA|ROLES|guardarMembershipBPM($membershipBPM, $userNick): $info_bpm >> '.json_encode($info_bpm));

			//TODO: SACAR HARDCODEO ACA
			$session = '"X-Bonita-API-Token=658fcd51-ef8b-48c3-9606-1d89a88cf3e5;JSESSIONID=BCDEA4A05749709F4DFBDCBB58A527E8;bonita.tenant=1;"';
			$datos["user_id"] = $info_bpm->id; // id de usuario en bpm
			$datos["group_id"] = $membershipBPM['group_id'];
			$datos["role_id"] =  $membershipBPM['role_id'];
			$post["session"] = $session;
			$post["payload"] = $datos;

			$aux = $this->rest->callAPI("POST",REST_BPM."/memberships", $post); 
			$aux =json_decode($aux["data"]);
			return $aux;
		}

        /**
		* Traer info de usuario de BPM
		* @param
		* @return array con info de usuario en BPM
		*/
		function getInfoBPM($usrNick){

			//TODO: DESHARDCODEAR SESSION
			$session = "X-Bonita-API-Token%3D7e2dbb6d-2261-4571-809e-d2b55144a75d%3BJSESSIONID%3DD82EE7AD27E388E1624433D7BE30BA07%3Bbonita.tenant%3D1%3B";
			$url = REST_BPM."/users/".$usrNick."/session/".$session;
			$aux = $this->rest->callAPI("GET", REST_BPM."/users/".$usrNick."/session/".$session);
			$aux =json_decode($aux["data"]);

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