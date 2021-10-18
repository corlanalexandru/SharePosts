(function () {

   
    const postsHolder = document.querySelector('#postsList');
    const loaderEl = document.querySelector('.loader');
    

    const getPosts = async (page, limit) => {
        let token = getCookie('token');
        let options = {};
        let ownQuery = '';
        if(postsHolder.hasAttribute("data-own")) {
            ownQuery = '&restrictUser=true';
        }
        const API_URL = `${WS2_HOST}:${WS2_PORT}/api/posts/list?page=${page}&limit=${limit}${ownQuery}`;
        if(postsHolder.hasAttribute("data-own")) {
            options = { 
                method: 'get',
                headers: new Headers({
                    'Authorization': `Bearer ${token}`
                }),  
            };
        }
        const response = await fetch(API_URL, options);
        if (!response.ok) {
            throw new Error(`An error occurred: ${response.status}`);
        }
        return await response.json();
    }

    // show the posts
    const renderPosts = (posts) => {
        posts.forEach(post => {
            postElement = postTemplate(post.id, post.title, post.userName, post.content, post.userUsername, post.created_at)
            postsHolder.insertAdjacentHTML('beforeend', postElement);
        });
    };

    const hideLoader = () => {
        loaderEl.classList.add('d-none');
    };

    const showLoader = () => {
        loaderEl.classList.remove('d-none');
    };

    const hasMorePosts = (page, limit, total) => {
        const startIndex = (page - 1) * limit + 1;
        return total === 0 || startIndex < total;
    };

    const loadPosts = async (page, limit) => {
        showLoader();
        setTimeout(async () => {
            try {
                // if having more posts to fetch
                if (hasMorePosts(page, limit, total)) {
                    // call the API to get posts
                    const response = await getPosts(page, limit);
                    // show posts
                    renderPosts(response.data);
                    // update the total
                    total = response.total;
                }
            } catch (error) {
                console.log(error.message);
            } finally {
                hideLoader();
            }
        }, 500);
    };

    let currentPage = 1;
    const limit = 10;
    let total = 0;
    window.addEventListener('scroll', () => {
        const {
            scrollTop,
            scrollHeight,
            clientHeight
        } = document.documentElement;

        if (scrollTop + clientHeight >= scrollHeight - 5 &&
            hasMorePosts(currentPage, limit, total)) {
            currentPage++;
            loadPosts(currentPage, limit);
        }
    }, {
        passive: true
    });
    loadPosts(currentPage, limit);

})();