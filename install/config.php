<?php

	/* install/config.php
	Make sure to keep this file secure! */

	// Do NOT touch this! //
	define( "__INSTALL__", true );
	define( "__TEMPLATE__", __DIR__ . "/../../template/" )

	// CTF Mode, not yet implemented. //
	define( "__CTF__", false );

	// Debug Mode. Verbose output on crash //
	define( "__DEBUG__", false );

	// Generate 64+ character random string; Read README for more info //
	define( "__HASH_SALT__", "" );

	// WeChall Info - You can leave this blank. //
	define( "__SITE_NAME__", "Stereotyped Challenges" );
	define( "__WECHALL__", "SECRET_KEY" );

	// DB Credentials //
	define( "__DB_HOST__", "localhost" );
	define( "__DB_USER__", "chall" );
	define( "__DB_PASS__", "chall" );
	define( "__DB_BASE__", "chall" );

	// Mail Credentials //
	define( "__MAIL_TYPE__", "gmail" );
	// define( "__MAIL_HOST__", "smtp.stypr.com" );
	// define( "__MAIL_PORT__", "25" );
	define( "__MAIL_USER__", "haroldie76@gmail.com" );
	define( "__MAIL_PASS__", "@@BlahBlahPass137@@" );
	define( "__MAIL_NAME__", "Harold Kim" );

	// Put your site's Base URL //
	define( "__HOST__", "https://chall.stypr.com");

