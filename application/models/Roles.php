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
				//TODO: AGREGAR REST
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
				//TODO: AGREGAR REST
				$token = 123;
				$aux = $this->rest->callAPI("GET",REST_BPM."/roles/".$token);
				$aux =json_decode($aux["data"]);
				return $aux->payload;				
		}

		/**
		* Crea membership en BPM
		* @param array con datos de usr
		* @return string stats de respuesta del servicio
		*/
		function guardarMembershipBPM($membershipBPM)
		{     
			log_message('INFO','#TRAZA|| >> ');
			
			// trae info de usuario en BPM
			$info_bpm = $this->getInfoBPM();

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
		* @return 
		*/
		function getInfoBPM(){

			$info_bpm = '{
									"last_connection":"",
									"created_by_user_id":"-1",
									"creation_date":"2014-12-01 10:39:55.177",
									"id":"21",
									"icon":"/default/icon_user.png",
									"enabled":"true",
									"title":"Mrs",
									"professional_data":{
										"fax_number":"484-302-0430",
										"building":"70",
										"phone_number":"484-302-5430",
										"website":"",
										"zipcode":"19108",
										"state":"PA",
										"city":"Philadelphia",
										"country":"United States",
										"id":"21",
										"mobile_number":"",
										"address":"Renwick Drive",
										"email":"giovanna.almeida@acme.com",
										"room":""
									},
									"manager_id":{
										"last_connection":"",
										"created_by_user_id":"-1",
										"creation_date":"2014-12-01 10:39:55.136",
										"id":"17",
										"icon":"/default/icon_user.png",
										"enabled":"true",
										"title":"Mrs",
										"manager_id":"1",
										"job_title":"Vice President of Sales",
										"userName":"daniela.angelo",
										"lastname":"Angelo",
										"firstname":"Daniela",
										"password":"",
										"last_update_date":"2014-12-01 10:39:55.136"
									},
									"job_title":"Account manager",
									"userName":"giovanna.almeida",
									"lastname":"Almeida",
									"firstname":"Giovanna",
									"password":"",
									"last_update_date":"2014-12-01 10:39:55.177"
								} ';
			return json_decode($info_bpm);					
		}
}