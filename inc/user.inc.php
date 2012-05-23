<?php
	if (!defined('INCLUDE_SECURE')) hack();

	class User extends ActiveMongo{
		public $email;	# Email
		public $pwd;	# Password
		public $name;	# Name
		public $Tcr;	# Time Created
		public $priv;	# Array of Privileges 
		
		function __construct(){
			$this->addIndex(array('email'=>'1'), array('unique'=>'true'));
		}
		protected function getCollectionName(){
			return 'user';
		}
		
		public static function getAllSorted($sorted='_id',$direction=1)
		{
			$_user = new User;
			$col = $_user->_getCollection();
			$query = $col->find();
			$query->sort(array($sorted => $direction));
			$_user->setCursor($query);
			return $_user;
		}
		
		public static function get_by_email($e){
			$u = new User;
			$u->email = $e;
			return $u->find();
		}
		public static function authenticate($e, $p){
			$u = new User;
			$u->email = $e;
			$u->pwd = $p;
			$u->find();
			if ($u->count() == 0 || !isset($u->pwd) ) return false;
			if ($u->pwd == $p && $u->email == $e){
				return (string)$u;
			}else{
				return false;
			}
		}
		public function add_priv($p){
			if ($this->key()){
				$priv = $this->priv;
				if (!isset($priv) || !is_array($priv)) $priv = array();
				if (!in_array($p, $priv)){
					$priv[] = $p;
				}
				$this->priv = $priv;
				
				$this->save();
			}
			return $this;
		}
		public static function get_by_id($id){
				$_u = new user;
				$_u->find( new MongoId($id) );
				return $_u;
		}
		public static function login($id){
			$_SESSION['user_id'] = $id;
			
			$_u = new user;
			$_u->find(new MongoId($id));
			
			event::log('User Login', $_u->email);
		}
		public static function logout(){
			unset($_SESSION['user_id']);
			unset($_SESSION['ADMIN_ALL']);
		}
		
		
		/* Validators */
		
		function email_filter($value, $past_value){
			if (!preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i',$value)){
				throw new ActiveMongo_FilterException("Invalid field email");
			}			
		}
		function pwd_filter($value, $past_value){
			if (!is_null($value)){
			if (!preg_match('/^.{3,}$/i',$value)){
				throw new ActiveMongo_FilterException ("Invalid field pwd");
			}
			}
		}
		function name_filter($value, $past_value){
			if (!preg_match('/^.{3,}$/i',$value)){
				throw new ActiveMongo_FilterException ("Invalid field name");
			}						
		}
		function before_save($op, $document){
			if (!isset($document['Tcr']) || !$document['Tcr']) $document['Tcr'] = time();
			if ($op == 'create'){
				if (!isset($document['email'])) throw new ActiveMongo_FilterException ("Missing field email");
			}
		}
		
		public function is_admin(){
			if (isset($this->priv) && is_array($this->priv)){
				if (in_array('ADMIN_ALL',$this->priv)) {
					return true;
				}
			}
			return false;
		}

	}
	class Session extends ActiveMongo {
		public $uid; # User id
		public $Tin; # Time in
		public $Tla; # Time last active
		public $Pat; # page at
		public $Uat; # url at
		
		public static function create($uid, $time = false){
			if ($time === false) $time = time();
			$s = new Session;
			$s->uid = $uid;
			$s->Tin = $s->Tla = $time;
			
			return $s;
		}
	}

	class preregemail extends ActiveMongo {
		var $email;
		var $Tcr;
		
		static public function send_invite($email, $notify = true){
			$_u = new user;
				$_u->email = $email;
				$_u->find();
			if ($_u->count() == 0){
				$_u->email = $email;
				$_u->save();
			}else if(isset($_u->pwd)){
				return;
			}

			$_inv = new preregemail;
				$_inv->email = $email;
				$_inv->find();

			if ($_inv->count() == 0){
				
			}else if(!isset($_inv->invitee)){
				if ($notify){
					$x = new mail_engine('invite-email');
						$x->send( $email,
							array(
								  'link'=>'<a href="'.makelink('invite','activate',(string)$_u).'">here</a>'
								 )
						);
				}
			}else{
				$_u2 = new User;
					$_u2->email = $_inv->invitee;
					$_u2->find();
				$name = isset($_u2->name) ? formatText($_u2->name) : 'Fireplace.sg';
				
				if ($notify){
					$x = new mail_engine('invite-send');
					$x->send( $email,
						array(
							  'invitor'=>$name,
							  'link'=>'<a href="'.makelink('invite','activate',(string)$_u).'">here</a>'
							 )
					);
				}
			}
		}
    }
?>