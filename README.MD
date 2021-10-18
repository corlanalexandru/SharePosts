START ws1 : php -S localhost:5001 -t ws1 ws1/public/index.php
START ws2 : php -S localhost:5002 -t ws2 ws2/public/index.php
START app : npm i && npm start

# **Running project instructions**

For starting the frontend application
- ###### `cd app/ && npm i && nodemon app.js`

For starting the WS_1
- ###### `cd ws1/ && composer install && php -S localhost:5001 -t ws1 ws1/public/index.php`

For starting the WS_2
- ###### `cd ws2/ && composer install && php -S localhost:5002 -t ws2 ws2/public/index.php`

For starting the SOCKET connection to get realtime notification for new posts
- ###### `cd ws2/ php public/socket.php`

Requirements for the application to run

- ###### `Free ports 5000/5001/5002/5005`
- ###### `Node, PHP, MySQL and Composer installed on local machine`
- ###### `Database file for import can be found in root folder, under the name database.sql`
- ###### `Credentials for connection to MySQL can be found in ws1/.env and ws2/.env`