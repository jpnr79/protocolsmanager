<?php

function plugin_protocolsmanager_install() { 
	global $DB;
	$version = plugin_version_protocolsmanager();
	$migration = new Migration($version['version']);	
	
	if (!$DB->tableExists("glpi_plugin_protocolsmanager_profiles")) {
   
		$query = "CREATE TABLE glpi_plugin_protocolsmanager_profiles (
					id int(11) NOT NULL auto_increment,
					profile_id int(11),
					plugin_conf char(1) collate utf8_unicode_ci default NULL,
					tab_access char(1) collate utf8_unicode_ci default NULL,
					make_access char(1) collate utf8_unicode_ci default NULL,
					delete_access char(1) collate utf8_unicode_ci default NULL,
					PRIMARY KEY  (`id`)
				  ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

		$DB->query($query) or die($DB->error());

		$id = $_SESSION['glpiactiveprofile']['id'];
		$query = "INSERT INTO glpi_plugin_protocolsmanager_profiles (profile_id, plugin_conf, tab_access, make_access, delete_access) VALUES ('$id','w', 'w', 'w', 'w')";

		$DB->query($query) or die($DB->error());
	}
	
	if (!$DB->tableExists('glpi_plugin_protocolsmanager_config')) {
      
		$query = "CREATE TABLE glpi_plugin_protocolsmanager_config (
				  id INT(11) NOT NULL auto_increment,
				  name VARCHAR(255),
				  title varchar(255),
				  font varchar(255),
				  fontsize varchar(255),
				  logo varchar(255),
				  content text,
				  footer text,
				  city varchar(255),
				  serial_mode int(2),
				  column1 varchar(255),
				  column2 varchar(255),
				  orientation varchar(10),
				  breakword int(2),
				  email_mode int(2),
				  upper_content text,
				  email_template int(2),
				  author_name varchar(255),
				  author_state int(2),
				  PRIMARY KEY (id)
			   ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
			   
		$DB->queryOrDie($query, $DB->error());
	  
		$query2 = "INSERT INTO glpi_plugin_protocolsmanager_config (
					name, title, font, fontsize, content, footer, city, serial_mode, orientation, breakword, email_mode, author_name, author_state)
					VALUES ('Equipment report',
							'Certificate of delivery of {owner}',
							'Roboto',
							'9',
							'User: \n I have read the terms of use of IT equipment in the Example Company.',
							'Example Company \n Example Street 21 \n 01-234 Example City',
							'Example city',
							1,
							'Portrait',
							1,
							2,
							'Test Division',
							1)";

		$query4 = "INSERT INTO glpi_plugin_protocolsmanager_config (
					name, title, font, fontsize, content, footer, city, serial_mode, orientation, breakword, email_mode, author_name, author_state)
					VALUES ('Equipment report 2',
							'Certificate of delivery of {owner}',
							'Roboto',
							'9',
							'User: \n I have read the terms of use of IT equipment in the Example Company.',
							'Example Company \n Example Street 21 \n 01-234 Example City',
							'Example city',
							1,
							'Portrait',
							1,
							2,
							'Test Division',
							1)";
							
		$DB->queryOrDie($query2, $DB->error());
		$DB->queryOrDie($query4, $DB->error());
	}
	
	/**
	* UPDATES CONFIG TABLE FOR SCALABILITY
	*/

	//update config table if upgrading before 1.5.0
	if (!$DB->FieldExists('glpi_plugin_protocolsmanager_config', 'author_name')) {
		
		$query = "ALTER TABLE glpi_plugin_protocolsmanager_config
					ADD author_name varchar(255)
						AFTER email_template";
		
		$DB->queryOrDie($query, $DB->error());
	}
	
	if (!$DB->FieldExists('glpi_plugin_protocolsmanager_config', 'author_state')) {
		
		$query = "ALTER TABLE glpi_plugin_protocolsmanager_config
					ADD author_state int(2)
						AFTER author_name";
		
		$DB->queryOrDie($query, $DB->error());
	}
	//update config table if upgrading before 1.5.2
	if (!$DB->FieldExists('glpi_plugin_protocolsmanager_config', 'title')) {
		
		$query = "ALTER TABLE glpi_plugin_protocolsmanager_config
					ADD title varchar(255)
						AFTER name";
		
		$DB->queryOrDie($query, $DB->error());
	}
	
	if (!$DB->tableExists('glpi_plugin_protocolsmanager_emailconfig')) {
		
		$query = "CREATE TABLE glpi_plugin_protocolsmanager_emailconfig (
					id INT(11) NOT NULL auto_increment,
					tname varchar(255),
					send_user int(2),
					email_content text,
					email_subject varchar(255),
					email_footer varchar(255),
					recipients varchar(255),
					PRIMARY KEY (id)
					) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
					
		$DB->queryOrDie($query, $DB->error());

	}

	if($DB->tableExists('glpi_plugin_protocolsmanager_emailconfig'))
	{
		$query3 = "INSERT INTO glpi_plugin_protocolsmanager_emailconfig (
			tname, send_user, email_content, email_subject, recipients)
			VALUES ('Email default',
					2,
					'Testmail',
					'Testmail',
					'Testmail')";
					
		$DB->queryOrDie($query3, $DB->error());
	}
	
	if (!$DB->tableExists('glpi_plugin_protocolsmanager_protocols')) {
      
		$query = "CREATE TABLE glpi_plugin_protocolsmanager_protocols (
                  id INT(11) NOT NULL auto_increment,
                  name VARCHAR(255),
				  user_id int(11),
				  gen_date datetime,
				  author varchar(255),
				  document_id int(11),
				  document_type varchar(255),
				  PRIMARY KEY (id)
               ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
			   
		$DB->queryOrDie($query, $DB->error());
	}
	
	if ($DB->tableExists('glpi_plugin_protocolsmanager_config')) {
		return true;
	}	
	
	if ($DB->tableExists('glpi_plugin_protocolsmanager_protocols')) {
		return true;
	}

	//execute the whole migration
	$migration->executeMigration();
	
	return true; 
}
 

function plugin_protocolsmanager_uninstall() { 

	global $DB;
	
	$tables = array("glpi_plugin_protocolsmanager_protocols", "glpi_plugin_protocolsmanager_config", "glpi_plugin_protocolsmanager_profiles", "glpi_plugin_protocolsmanager_emailconfig");

	foreach($tables as $table) 
		{$DB->query("DROP TABLE IF EXISTS `$table`;");}
	
	return true; 
	
	}

?>