const express = require('express');
const path = require('path');
const cookieParser = require('cookie-parser')
const app = express();
app.use(cookieParser());


const {getLoginPage, getRegisterPage, getHomepage, logout, getMyPosts} = require('./src/Controller/MainController');
const port = 5000;

// configure middleware
app.set('port', process.env.port || port); // set express to use this port
app.set('views', __dirname + '/views'); // set express to look in this folder to render our view
app.set('view engine', 'ejs'); // configure template engine
app.use(express.static(path.join(__dirname, 'public/assets'))); // configure express to use public folder

app.get('/', getHomepage);
app.get('/login', getLoginPage);
app.get('/register', getRegisterPage);
app.get('/logout', logout);
app.get('/my-posts', getMyPosts);

// set the app to listen on the port
app.listen(port, () => {
    console.log(`Server running on port: ${port}`);
});