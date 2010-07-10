<?php
/**
 * This class sets users permissions
 */
class UsersPermission extends Object implements IAuthorizator {
	private $acl;
	public function __construct(){
		$this->acl = new Permission();
		
		// Adding resources
			// Administration
			$this->acl->addResource('users');
			$this->acl->addResource('files');

		// Adding of user's role
		$this->acl->addRole('normal');
		$this->acl->addRole('master');	// Main admin
		$this->acl->addRole('guest');

		// Settings of allowed/denied resources
			// Master admin (can do everything)
			$this->acl->allow('master','users');
			$this->acl->allow('master','files');
			// Ordinary client in partner section
			$this->acl->allow("normal","files");
			// Guest - can do nothing
			$this->acl->deny('guest');
	}

	public function isAllowed($role = self::ALL, $resource = self::ALL, $privilege = self::ALL) {
		return $this->acl->isAllowed($role, $resource, $privilege);
        }


}