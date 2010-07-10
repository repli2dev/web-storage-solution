<?php
/**
 * Model for table with users, implements authenticator.
 */
class UsersModel extends SimpleModel implements IAuthenticator  {
	public $name = "users";
	/**
	 * @param  array
	 * @return IIdentity
	 * @throws AuthenticationException
	 */
	function authenticate(array $credentials) {
		$username = $credentials[self::USERNAME];
		$password = sha1($credentials[self::PASSWORD]);

		$row = $this->findByName($username)->fetch();
		if ($row == FALSE) {
			throw new AuthenticationException('Unknown user', self::IDENTITY_NOT_FOUND);
		}
		if ($row->password !== $password) {
			throw new AuthenticationException('Password not match', self::INVALID_CREDENTIAL);
		}
		return new Identity($row->username, $row->role, array('id' => $row->id)); // zde je důležité právě předání rolí
	}

	function findByName($name){
		return dibi::query("SELECT * FROM ".$this->name." WHERE username=%s LIMIT 1", $name);
	}

}