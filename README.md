# Web Storage Solution

This is small web application which can handle storing and automatic deletion
of this files after certain period of time.

Just upload file, set expiry, give links to others... and leave it ;-)

## TABLE OF CONTENTS of this README

1. Usage
2. Requirements
3. Installation
4. Licence
5. Contact

## 1. USAGE

1. Login into system from index page.
2. Manage files
	a. Add file by clicking on link Add above the table.
		Choose the file to upload, or select it from "incoming" dir
		(files in this dir are uploaded through ftp, sftp, ssh)
		Select date of expiration.
		Save file (if the file is large, be patient)
	b. Delete file - click on red cross in the row
	c. Edit expiration of file - click on yellow pencil in the row.
		Edit expiration and save with clicking on button.
3. Manage users - switch to Users page (menu is on top of page)
	a. Add new user by clicking on link Add above the table.
		Then fill in all fields. Assign role (master vs. normal user).
		(Master users can create users).
		Submit with button.
	b. Delete user - click on red cross in the row. User with ID 1 cannot be
		deleted.
	c. Edit user - click on yellow pencil in the row.
		After edition, submit by button.
4. Change your password - click on Change password (menu is on top of page).
	Fill the form, be careful! Submit by button.
5. Logout - click on Logout (menu is on top of page).
    
## 2. REQUIREMENTS

* Apache
* PHP >= 5.3
* mod_rewrite
* Composer for managing dependencies

## 3. INSTALLATION

Please read INSTALL file

## 4. License

Please read LICENSE file

## 5. CONTACT

If you encounter any problems do not hesitate to contact me!
Preferred way is to open new issue on GitHub repository.
