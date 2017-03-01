# Downloader

Application for download (with auth) files from sites

<pre>
Usage: downloader.php [OPTIONS] [URL]
Options:

 -v, 	--version	Display version and exit
 -h, 	--help	Display this help message
 -d, 	--debug	Display debug information
 -u, 	--user=USERNAME	Username for login to site
 -p, 	--password=PASSWORD	Password for user
 -o, 	--out=FILE	Filename for save file
 -c, 	--config=FILE	Filename to config file for aucth params
</pre>
 
 Config file contains params
 
 * login_url - URL for login
 * username_field - Name of input for login
 * password_field - Name of input for password
 * hash_password - Flag for hash password (If set to 1 then password hashed by MD5 and send in field with name in password_hash)  
 * password_hash -Name of input for hashed password
 * need_submit - Flag for submit button (If set to 1 then submit button include to request)
 * submit_field - Name of input for submit button
 * submit_value - Value of input for submit button
 
 
 

