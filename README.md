# 5Things
This is a simple php site that can be used to share the last 5 things added to the site between computers.

## Goal
This simple webpage is intended to be used like a cross-platform clipboard. As a web app it can be accessed by several browsers. Just like a clipboard it is intended to only store a few things. THe preset is 5, but that can be configured to as long of a list as desired.

## Installation
1. Create your SQL database .
2. Use the 5things.sql file to setup the database.
3. configure your config.php file based on the config.example.php template provided. (refer to the config setup for details on the parameters.
4. Ready to use.

### Config Setup
- Refer to your PHP mySQL/PDO documentation for appropriate DB setup
  - $db_host_data; Database details; DB_HOST/DB_NAME/DB_CHARSET
  - $db_user; Database user
  - $db_password; Database password
  - $db_name; Database name
- $address; Type the address including subdomains and path leading to the index of 5things
- $disp_address; type the pretty print version of the address, displayed in the footer
- $logo; type the path to a logo file which will appear in the header, or leave blank
- $PW_req; specifies whether a password is required to add/clear things, default TRUE
- $num_things; Spewcifies the length of the things list, default 5 (future capability)
- $login_attempt_limit; Specifies the number of failed login attempts duting the locout period, default 5
- $login_lockout_duration; Specifies the locout period, the duration from the first failed login attempt to the point where the limit will be triggered, default 300 (5 minutes)

### Password Setup
- If $PW_req is set to true the application will request a password on first use. This will be the password.
- The lockout function will watch login attempts by counting the failed attempts within the login lockout duration
- Each failed attempt will reset the counter, so wiht a 5 minute time and a 5 attempt limit, it would be possible to fail to login 5 times spreading out each attempt by 5 minutes and you will still be lockced out after the 5th attempt.

## Future Improvements
- Dark & Light Modes
- Recognition of URLs to create links
- Correct all SQL Queries
- SQL Query bleed
- Replace all CDN Files with static files
- Auto setup of databases without a sql file
- Change password
- Logoff all users
- Background process to check if new things added without refreshing