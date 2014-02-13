<?php

/**
 * Configure App specific behavior for 
 * Maestrano SSO
 */
class MnoSsoUser extends MnoSsoBaseUser
{
  /**
   * Database connection
   * @var PDO
   */
  public $connection = null;
  
  
  /**
   * Extend constructor to inialize app specific objects
   *
   * @param OneLogin_Saml_Response $saml_response
   *   A SamlResponse object from Maestrano containing details
   *   about the user being authenticated
   */
  public function __construct(OneLogin_Saml_Response $saml_response, &$session = array(), $opts = array())
  {
    // Call Parent
    parent::__construct($saml_response,$session);
    
    // Assign new attributes
    $this->connection = $opts['db_connection'];
  }
  
  
  /**
   * Sign the user in the application. 
   * Parent method deals with putting the mno_uid, 
   * mno_session and mno_session_recheck in session.
   *
   * @return boolean whether the user was successfully set in session or not
   */
  protected function setInSession()
  {
    $user = GO_Base_Model_User::model()->findSingleByAttribute('username', $this->uid);
    GO::session()->start();
		GO::session()->setCurrentUser($user->id);
		GO::language()->setLanguage($user->language);
    return true;
  }
  
  
  /**
   * Used by createLocalUserOrDenyAccess to create a local user 
   * based on the sso user.
   * If the method returns null then access is denied
   *
   * @return the ID of the user created, null otherwise
   */
  protected function createLocalUser()
  {
    $lid = null;
    
    if ($this->accessScope() == 'private') {
      
      // Ignore permissions
      GO::$ignoreAclPermissions = true;
      
      // Build the user attributes first
      $user_data = $this->buildLocalUser();
      $group_list = $this->buildPermissionGroupList();
      
      // Create user
      $user = GO_Base_Model_User::newInstance($user_data, $group_list, array(), true);
      var_dump($user->id);
      $lid = $user->id;
    }
    
    return $lid;
  }
  
  /**
   * Build user hash for creation
   *
   * @return the user hash
   */
  protected function buildLocalUser()
  {
    # Password must contain at least a capital
    # letter and a special character
    $password = $this->generatePassword();
    $password .= 'A8!';
    
    $user_data = array(
      'username' => $this->uid,
      'password' => $password,
      'passwordConfirm' => $password,
      'email' => $this->email,
      'first_name' => $this->name,
      'last_name' => $this->surname,
      'enabled' => 1,
    );
    
    return $user_data;
  }
  
  /**
   * Build permission group array for creation
   *
   * @return the permission group array
   */
  protected function buildPermissionGroupList()
  {
    $group_list = ['Everyone','Internal'];
    $admin_role = false;
      
    if ($this->app_owner) {
      $admin_role = true;
    } else {
      foreach ($this->organizations as $organization) {
        if ($organization['role'] == 'Admin' || $organization['role'] == 'Super Admin') {
          $admin_role = true;
        } else {
          $admin_role = false;
        }
      }
    }
    
    // Add Admins group is user is considered
    // admin
    if ($admin_role) {
      $group_list[] = 'Admins';
    }
    
    return $group_list;
  }
  
  /**
   * Get the ID of a local user via Maestrano UID lookup
   *
   * @return a user ID if found, null otherwise
   */
  protected function getLocalIdByUid()
  {
    $result = $this->connection->query("SELECT id FROM go_users WHERE mno_uid = {$this->connection->quote($this->uid)} LIMIT 1")->fetch();
    
    if ($result && $result['id']) {
      return $result['id'];
    }
    
    return null;
  }
  
  /**
   * Get the ID of a local user via email lookup
   *
   * @return a user ID if found, null otherwise
   */
  protected function getLocalIdByEmail()
  {
    $result = $this->connection->query("SELECT id FROM go_users WHERE email = {$this->connection->quote($this->email)} LIMIT 1")->fetch();
    
    if ($result && $result['id']) {
      return $result['id'];
    }
    
    return null;
  }
  
  /**
   * Set all 'soft' details on the user (like name, surname, email)
   * Implementing this method is optional.
   *
   * @return boolean whether the user was synced or not
   */
   protected function syncLocalDetails()
   {
     if($this->local_id) {
       $upd = $this->connection->query("UPDATE go_users 
        SET first_name = {$this->connection->quote($this->name)},
        last_name = {$this->connection->quote($this->surname)}, 
        email = {$this->connection->quote($this->email)} WHERE id = $this->local_id");
       
       return $upd;
     }
     
     return false;
   }
  
  /**
   * Set the Maestrano UID on a local user via id lookup
   *
   * @return a user ID if found, null otherwise
   */
  protected function setLocalUid()
  {
    if($this->local_id) {
      $upd = $this->connection->query("UPDATE go_users SET mno_uid = {$this->connection->quote($this->uid)} WHERE id = $this->local_id");
      return $upd;
    }
    
    return false;
  }
}