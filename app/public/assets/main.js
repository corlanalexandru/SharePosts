

const WS1_HOST = 'http://localhost';
const WS1_PORT = '5001';

const WS2_HOST = 'http://localhost';
const WS2_PORT = '5002';


const socket = new WebSocket('ws://localhost:5005');

function transmitMessage(message) {
    socket.send(message);
}



$(document).on('submit', '#RegisterForm', function(e) {
    showLoader();
    $('.mandatory').css('display','none').html('');
    let prefix = 'register_';
    let $form = $(this);
    let username = $form.find('#'+prefix+'username').val();
    let password = $form.find('#'+prefix+'password').val();
    let repeatPassword = $form.find('#'+prefix+'repeat_password').val();
    let name = $form.find('#'+prefix+'name').val();
    let data = {
        'username' : username,
        'password' : password,
        'repeat_password' : repeatPassword,
        'name' : name,
    }
    fetch(`${WS1_HOST}:${WS1_PORT}/api/register`, { 
        method: 'post', 
        headers: new Headers({
          'Content-Type': 'application/json'
        }), 
        body: JSON.stringify(data)
    }).then(response => {
        return response.json();
    }).then(response => {
        if(typeof response.jwt !== 'undefined') {
            document.cookie = "username="+response.user.username+';expires='+(new Date(response.expiresAt)).toUTCString();
            document.cookie = "user="+response.user.name+';expires='+(new Date(response.expiresAt)).toUTCString();
            document.cookie = "token="+response.jwt+';expires='+(new Date(response.expiresAt)).toUTCString();
            alert('Register successful!');
            setTimeout(() => {window.location.href="/"},1000)   
        }
        if(typeof response.errors !== 'undefined') {
            for(let error in response.errors) {
                if($('#mandatory_'+prefix+error).length > 0) {
                    $('#mandatory_'+prefix+error).css('display', 'block').html(response.errors[error]);
                }
                else {
                    $('#mandatory_'+prefix+'general_errors').css('display', 'block').append(response.errors[error]);
                }
            }
        }
    }).finally(()=>{hideLoader()});
    e.preventDefault();
    return false;
});

$(document).on('submit', '#LoginForm', function(e) {
    showLoader();
    $('.mandatory').css('display','none').html('');
    let prefix = 'login_';
    let $form = $(this);
    let username = $form.find('#'+prefix+'username').val();
    let password = $form.find('#'+prefix+'password').val();
    let data = {
        'username' : username,
        'password' : password,
    }
    fetch(`${WS1_HOST}:${WS1_PORT}/api/login`, { 
        method: 'post', 
        headers: new Headers({
          'Content-Type': 'application/json'
        }), 
        body: JSON.stringify(data)
    }).then(response => {
        return response.json();
    }).then(response => {
        if(typeof response.jwt !== 'undefined') {
            document.cookie = "username="+response.user.username+';expires='+(new Date(response.expiresAt)).toUTCString();
            document.cookie = "user="+response.user.name+';expires='+(new Date(response.expiresAt)).toUTCString();
            document.cookie = "token="+response.jwt+';expires='+(new Date(response.expiresAt)).toUTCString();
            alert('Login successful!');
            setTimeout(() => {window.location.href="/";},1000)   
        }
        if(typeof response.errors !== 'undefined') {
            for(let error in response.errors) {
                if($('#mandatory_'+prefix+error).length > 0) {
                    $('#mandatory_'+prefix+error).css('display', 'block').html(response.errors[error]);
                }
                else {
                    $('#mandatory_'+prefix+'general_errors').css('display', 'block').append(response.errors[error]);
                }
            }
        }
    }).finally(()=>{hideLoader()});
    e.preventDefault();
    return false;
});


$(document).on('submit', '#AddNewPostForm', function(e) {
    showLoader();
    $('.mandatory').css('display','none').html('');
    let prefix = 'post_';
    let $form = $(this);
    let title = $form.find('#'+prefix+'title').val();
    let content = $form.find('#'+prefix+'content').val();
    let token = getCookie('token');
    let data = {
        'title' : title,
        'content' : content,
    }
    fetch(`${WS2_HOST}:${WS2_PORT}/api/posts/new`, { 
        method: 'post', 
        headers: new Headers({
            'Authorization': `Bearer ${token}`
        }), 
        body: JSON.stringify(data)
    }).then(response => {
        return response.json();
    }).then(response => {
        if(typeof response.errors !== 'undefined') {
            for(let error in response.errors) {
                if($('#mandatory_'+prefix+error).length > 0) {
                    $('#mandatory_'+prefix+error).css('display', 'block').html(response.errors[error]);
                }
                else {
                    $('#mandatory_'+prefix+'general_errors').css('display', 'block').append(response.errors[error]);
                }
            }
        }
        else {
            transmitMessage('New post was added by '+getCookie('user')+'!');
            alert('Post successful!');
            setTimeout(() => {window.location.href="/";},1000);
        }
    }).finally(()=>{hideLoader()});
    e.preventDefault();
    return false;
});


function getCookie(cname) {
    let name = cname + "=";
    let ca = document.cookie.split(';');
    for(let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
}

function postTemplate(id, title, userName, content, userUsername, createdAt) {
    let username = getCookie('username');
    let edit = '';
    if(username === userUsername) {
        edit += `<a style="float: right;" href="javascript:void(0)" data-post="${id}" class="delete-post btn btn-danger">Delete post</a><a data-post="${id}" style="float: right; margin-right:10px;" href="javascript:void(0)" class="btn edit-post btn-primary">Edit post</a>`;
    }
    let template = `<div class="card mb-3">
        <div class="card-header">
            <span id="postTitle${id}">${title}</span> ${edit}
        </div>
        <div class="card-body">
        <blockquote class="blockquote mb-0">
            <p id="postContent${id}">${content}</p>
            <footer class="blockquote-footer">Added by <cite title="Source Title">${userName}, ${createdAt}</cite></footer>
        </blockquote>
        <div class="mandatory alert alert-danger" id="mandatory_post${id}_general_errors" role="alert"></div>
        <div id="editPostForm${id}"></div>
        </div>
    </div>`
  return template;
}


$(document).on('click', '.delete-post', function(e) {
    showLoader();
    let target = $(this).attr('data-post');
    let token = getCookie('token');
    let prefix = 'post'+target;
    fetch(`${WS2_HOST}:${WS2_PORT}/api/posts/delete/${target}`, { 
        method: 'delete', 
        headers: new Headers({
            'Authorization': `Bearer ${token}`
        }), 
    }).then(response => {
        return response.json();
    }).then(response => {
        if(typeof response.errors !== 'undefined') {
            for(let error in response.errors) {
                if($('#mandatory_'+prefix+error).length > 0) {
                    $('#mandatory_'+prefix+error).css('display', 'block').html(response.errors[error]);
                }
                else {
                    $('#mandatory_'+prefix+'general_errors').css('display', 'block').append(response.errors[error]);
                }
            }
        }
        else {
            alert('Delete successful!');
            setTimeout(() => {location.reload();},1000)   
        }
    }).finally(()=>{hideLoader()});
})



$(document).on('click', '.edit-post', function(e) {
    let target = $(this).attr('data-post');
    let $postBody = $('#editPostForm'+target);
    let postContent = $('#postContent'+target).html();
    let postTitle = $('#postTitle'+target).html();
    if($postBody.length > 0) {
        $postBody.html(postEditTemplate(target, postTitle, postContent));
    }
});

function postEditTemplate(id, title, content) {
    let form = `<form data-post="${id}" class="editPostForm" id="EditPost${id}">
        <hr>
        <p style="font-weight:bold; margin-top:20px;">Edit post</p>
        <div class="mandatory alert alert-danger" id="mandatory_post_edit_${id}_general_errors" role="alert"></div>
        <input type="text" placeholder="Enter post title" value="${title}" name="title" id="post_edit_${id}_title" required>
        <div class="mandatory alert alert-danger" id="mandatory_post_edit_${id}_title" role="alert"></div>
        <textarea class="form-control mb-2" rows="3" placeholder="What are you doing today?" name="content" id="post_edit_${id}_content" required>${content}</textarea>
        <div class="mandatory alert alert-danger" id="mandatory_post_edit_${id}_content" role="alert"></div>
        <button type="submit" class="registerbtn">Save</button>
        </form>`
    return form;
}

$(document).on('submit','.editPostForm', function(e) {
    showLoader();
    let target = $(this).attr('data-post');
    $('.mandatory').css('display','none').html('');
    let prefix = 'post_edit_'+target+'_';
    let $form = $(this);
    let title = $form.find('#'+prefix+'title').val();
    let content = $form.find('#'+prefix+'content').val();
    let token = getCookie('token');
    let data = {
        'title' : title,
        'content' : content,
    }
    fetch(`${WS2_HOST}:${WS2_PORT}/api/posts/edit/${target}`, { 
        method: 'put', 
        headers: new Headers({
            'Authorization': `Bearer ${token}`
        }), 
        body: JSON.stringify(data)
    }).then(response => {
        return response.json();
    }).then(response => {
        if(typeof response.errors !== 'undefined') {
            for(let error in response.errors) {
                if($('#mandatory_'+prefix+error).length > 0) {
                    $('#mandatory_'+prefix+error).css('display', 'block').html(response.errors[error]);
                }
                else {
                    $('#mandatory_'+prefix+'general_errors').css('display', 'block').append(response.errors[error]);
                }
            }
        }
        else {
            alert('Post update successul!');
            setTimeout(() => {location.reload()},1000)   
        }
    }).finally(()=>{hideLoader()});
    e.preventDefault();
    return false;
});

function fetchHomepagePosts($postsList) {
    showLoader();
    fetch(`${WS2_HOST}:${WS2_PORT}/api/posts/list`, { 
        method: 'get', 
    }).then(response => {
        return response.json();
    }).then(response => {
        $postsList.html('');
        for(post in response) {
            $postsList.append(postTemplate(response[post].id, response[post].title, response[post].userName, response[post].content, response[post].userUsername, response[post].created_at));
        }
    }).finally(()=>{hideLoader()});
}

function showLoader() {
    $.blockUI({ css: { backgroundColor: 'none', color: '#fff', 'border':'none'}, message : '<div class="loader"></div>' });
}

function hideLoader() {
    return $.unblockUI();
}

// Create a new WebSocket.
socket.onmessage = function(e) {
    alert(e.data);
    let $postsList = $('#postsList');
    if($postsList.length > 0 && $postsList.hasClass('refreshOnMessage')) {
        location.reload();
    }
}