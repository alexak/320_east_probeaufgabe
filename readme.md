

##Installation:

1. setup webserver (see attached .conf file as example)
2. launch install composer install (composer needs to be installed on system)
3. change constants in Helper/DatabaseHelper according to your database connection
4. change constant in Tests/ServDbTest according to your web server config

##Improvements / known issues / todos..
- The demo includes server site only, no frontend. 
- Configs should be handeled in a seperate config file instead of hardcoded in file
- Routing/Autoloader/Database should be handled by framework
- Database could be handeled by doctrine or similar to handle object manipulations avoiding direct sql usages
- unitTests should be independent (they are actually more integration tests than unitTests)
