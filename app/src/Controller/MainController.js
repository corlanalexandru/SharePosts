module.exports = {
    getLoginPage: (req, res) => {
        res.render('login.ejs', {
            title: "Login",
            message: ''
        });
    },
    getRegisterPage: (req, res) => {
        res.render('register.ejs', {
            title: "Register",
            message: ''
        });
    },
    getHomepage: (req, res) => {
        res.render('home.ejs', {
            title: "Homepage",
            user: req.cookies.user,
            isAuthenticated: req.cookies.token ? true : false
        });
    },
    getMyPosts: (req, res) => {
        if(!req.cookies.token) {
            res.redirect('/login');
        }
        res.render('my-posts.ejs', {
            title: "My posts",
            user: req.cookies.user,
            isAuthenticated: req.cookies.token ? true : false
        });
    },
    logout: (req, res) => {
        res.cookie(`username`, null,{
            expires: new Date('01 12 1900'),
        });
        res.cookie(`user`, null,{
            expires: new Date('01 12 1900'),
        });
        res.cookie(`token`, null,{
            expires: new Date('01 12 1900'),
        });
        res.redirect('/');
    },
};
