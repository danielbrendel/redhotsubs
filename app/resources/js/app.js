/**
 * app.js
 * 
 * Put here your application specific JavaScript implementations
 */

 import './../sass/app.scss';

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Chart from 'chart.js/auto';

 window.vue = new Vue({
    el: '#main',

    data: {
        appName: '',
        defaultSub: '',
        bShowAgeConsent: false,
        bShowFavToken: false,
        bShowImportFavs: false,
        bScrollAutoLoad: false
    },

    methods: {
        initNavbar: function() {
            const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

            if ($navbarBurgers.length > 0) {
                $navbarBurgers.forEach( el => {
                    el.addEventListener('click', () => {
                        const target = el.dataset.target;
                        const $target = document.getElementById(target);

                        el.classList.toggle('is-active');
                        $target.classList.toggle('is-active');
                    });
                });
            }
        },

        ajaxRequest: function (method, url, data = {}, successfunc = function(data){}, finalfunc = function(){}, config = {})
        {
            let func = window.axios.get;
            if (method == 'post') {
                func = window.axios.post;
            } else if (method == 'patch') {
                func = window.axios.patch;
            } else if (method == 'delete') {
                func = window.axios.delete;
            }

            func(url, data, config)
                .then(function(response){
                    successfunc(response.data);
                })
                .catch(function (error) {
                    console.log(error);
                })
                .finally(function(){
                        finalfunc();
                    }
                );
        },

        handleCookieConsent: function () {
            let cookies = document.cookie.split(';');
            let foundCookie = false;
            for (let i = 0; i < cookies.length; i++) {
                if (cookies[i].indexOf('cookieconsent') !== -1) {
                    foundCookie = true;
                    break;
                }
            }

            if (foundCookie === false) {
                document.getElementById('cookie-consent').style.display = 'inline-block';
            }
        },

        clickedCookieConsentButton: function () {
            let expDate = new Date(Date.now() + 1000 * 60 * 60 * 24 * 365);
            document.cookie = 'cookieconsent=1; path=/; expires=' + expDate.toUTCString() + ';';

            document.getElementById('cookie-consent').style.display = 'none';
        },

        markAgeConsent: function() {
            let expDate = new Date(Date.now() + 1000 * 60 * 60 * 24 * 365);
            document.cookie = 'ageconsent=1; path=/; expires=' + expDate.toUTCString() + ';';

            this.bShowAgeConsent = false;
        },

        isAgeConsentMarked: function() {
            let cookies = document.cookie.split(';');

            for (let i = 0; i < cookies.length; i++) {
                if (cookies[i].indexOf('ageconsent=1') !== -1) {
                    return true;
                }
            }

            return false;
        },

        getPostSorting: function () {
            let cookies = document.cookie.split(';');

            for (let i = 0; i < cookies.length; i++) {
                if (cookies[i].indexOf('post_sorting') !== -1) {
                    return cookies[i].substr(cookies[i].indexOf('=') + 1);
                }
            }

            return 'hot';
        },

        setPostSorting: function(value) {
            let expDate = new Date(Date.now() + 1000 * 60 * 60 * 24 * 365);
            document.cookie = 'post_sorting=' + value + '; path=/; expires=' + expDate.toUTCString() + ';';
        },

        getSubSelection: function () {
            /*let result = '';
            let cookies = document.cookie.split(';');

            for (let i = 0; i < cookies.length; i++) {
                if (cookies[i].indexOf('sub_selection') !== -1) {
                    result = cookies[i].substr(cookies[i].indexOf('=') + 1);
                    break;
                }
            }*/

            let result = window.currentSelectedSub;
            
            if (typeof result === 'undefined') {
                result = this.defaultSub;
            }

            let selLabel = document.getElementById('current-sub');
            if (selLabel) {
                selLabel.innerHTML = result.substr(0, result.length - 1);
            }

            return result;
        },

        setSubSelection: function(value) {
            /*let expDate = new Date(Date.now() + 1000 * 60 * 60 * 24 * 365);
            document.cookie = 'sub_selection=' + value + '; path=/; expires=' + expDate.toUTCString() + ';';*/
            window.currentSelectedSub = value;
        },

        setSortingUnderline: function(ident) {
            document.getElementsByClassName('link-sorting-hot')[0].style.textDecoration = 'unset';
            document.getElementsByClassName('link-sorting-top')[0].style.textDecoration = 'unset';
            document.getElementsByClassName('link-sorting-new')[0].style.textDecoration = 'unset';

            document.getElementsByClassName('link-sorting-hot')[1].style.textDecoration = 'unset';
            document.getElementsByClassName('link-sorting-top')[1].style.textDecoration = 'unset';
            document.getElementsByClassName('link-sorting-new')[1].style.textDecoration = 'unset';

            document.getElementsByClassName('link-sorting-' + ident)[0].style.textDecoration = 'underline';
            document.getElementsByClassName('link-sorting-' + ident)[1].style.textDecoration = 'underline';
        },

        fetchPosts: function(sub, sorting, target) {
            if (document.getElementById('loadmore')) {
                document.getElementById('loadmore').remove();
            }

            let refresh = document.getElementById('media-settings');
            if (refresh) {
                if (refresh.classList.contains('is-hidden')) {
                    refresh.classList.remove('is-hidden');
                }
            }

            target.innerHTML += '<div id="spinner"><center><br/><i class="fas fa-spinner fa-spin"></i><br/><br/></center></div>';

            let displaySub = (sub[sub.length - 1] === '/') ? sub.substr(0, sub.length - 1) : sub;
            document.title = this.appName + ' - ' + displaySub;
            window.history.pushState({page: displaySub}, this.appName + ' - ' + displaySub, window.location.origin + '/' + displaySub);
            window.gtag('set', 'page_path', displaySub);
            window.gtag('event', 'page_view');

            window.inFetchingProgress = true;
            
            window.vue.ajaxRequest('post', window.location.origin + '/content/fetch', { sub: sub, sorting: sorting, after: window.fetch_item_after }, function(response){
                if (response.code == 200) {
                    window.inFetchingProgress = false;

                    if (document.getElementById('spinner')) {
                        document.getElementById('spinner').remove();
                    }

                    response.data.forEach(function(elem, index){
                        let html = window.vue.renderPost(elem);

                        target.innerHTML += html;
                    });

                    window.fetch_item_after = response.data[response.data.length - 1].all.name;

                    target.innerHTML += `<div id="loadmore"><center><br/><a id="loadmore-anchor" href="javascript:void(0);" onclick="window.vue.fetchPosts(window.vue.getSubSelection(), window.vue.getPostSorting(), document.getElementById('media-content'));">Load more</a><br/><br/></center></div>`;
                    
                    if (window.vue.bScrollAutoLoad) {
                        window.onscroll = function(ev) {
                            if ((window.scrollY + window.innerHeight) >= document.body.scrollHeight - 10) {
                                if (document.getElementById('loadmore-anchor')) {
                                    if (!window.inFetchingProgress) {
                                        document.getElementById('loadmore-anchor').click();
                                    }
                                }
                            }
                        };
                    }
                }
            });
        },

        customSub: function(ident) {
            document.cookie = 'custom_sub=r/' + ident + '; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
            location.href = window.location.origin + '/r/' + ident;
        },

        customUser: function(ident) {
            location.href = window.location.origin + '/user/' + ident;
        },

        renderIFrame: function(target, src) {
            let html = `<iframe id="media-player" class="media-video" src="` + src + `"></iframe>`;

            target.innerHTML = html;
        },

        renderPost: function(elem) {
            let mediaContent = '';

            if (elem.all.domain === 'redgifs.com') {
                mediaContent = `<center>
                    <div id="media-video" class="media-video-preview is-pointer" onclick="window.vue.renderIFrame(document.getElementById('item-media-` + elem.all.id + `'), 'https://www.redditmedia.com/mediaembed/` + elem.all.id + `');" style="background-image: url('` + elem.all.thumbnail + `');">
                        <div class="media-video-preview-overlay">    
                            <div class="media-video-preview-inner">
                                <i class="fas fa-play-circle"></i>&nbsp;Play
                            </div>
                        </div>
                    </div></center>
                `;
            } else {
                mediaContent = `<a href="` + elem.media + `" target="_blank"><img src="` + elem.media + `" alt="` + elem.title + `"/></a>`;
            }

            let lnkname = elem.all.name.substr(elem.all.name.indexOf('_') + 1);
            let pltitle = elem.all.permalink.substr(elem.all.permalink.indexOf(lnkname + '/') + lnkname.length + 1);
            pltitle = pltitle.substr(0, pltitle.length - 1);

            let share = `
                <div class="dropdown is-right" id="post-share-` + elem.all.id + `">
                    <div class="dropdown-trigger">
                        <i class="fas fa-share-alt is-pointer" onclick="window.vue.toggleDropdown(document.getElementById('post-share-` + elem.all.id + `'));"></i>
                    </div>
                    <div class="dropdown-menu" role="menu">
                        <div class="dropdown-content">
                            <a onclick="window.vue.toggleDropdown(document.getElementById('post-share-` + elem.id + `'));" href="https://www.reddit.com/submit?url=` + window.location.origin + '/p/' + elem.all.subreddit + '/' + elem.all.name + '/' + pltitle + `&title=` + elem.title + `" class="dropdown-item is-color-black">
                                <i class="fab fa-reddit"></i>&nbsp;Share with Reddit
                            </a>
                            <a onclick="window.vue.toggleDropdown(document.getElementById('post-share-` + elem.id + `'));" href="whatsapp://send?text=` + window.location.origin + '/p/' + elem.all.subreddit + '/' + elem.all.name + '/' + pltitle + ` - ` + elem.title + `" class="dropdown-item is-color-black">
                                <i class="fab fa-whatsapp"></i>&nbsp;Share with WhatsApp
                            </a>
                            <a onclick="window.vue.toggleDropdown(document.getElementById(''post-share-` + elem.id + `'));" href="https://twitter.com/share?url=` + encodeURI(window.location.origin + '/p/' + elem.all.subreddit + '/' + elem.all.name + '/' + pltitle) + `&text=` + elem.title + `" class="dropdown-item is-color-black">
                                <i class="fab fa-twitter"></i>&nbsp;Share with Twitter
                            </a>
                            <a onclick="window.vue.toggleDropdown(document.getElementById(''post-share-` + elem.id + `'));" href="https://www.facebook.com/sharer/sharer.php?u=` + window.location.origin + '/p/' + elem.all.subreddit + '/' + elem.all.name + '/' + pltitle + `" class="dropdown-item is-color-black">
                                <i class="fab fa-facebook"></i>&nbsp;Share with Facebook
                            </a>
                            <a onclick="window.vue.toggleDropdown(document.getElementById(''post-share-` + elem.id + `'));" href="mailto:name@domain.com?body=` + window.location.origin + '/p/' + elem.all.subreddit + '/' + elem.all.name + '/' + pltitle + ` - ` + elem.title + `" class="dropdown-item is-color-black">
                                <i class="far fa-envelope"></i>&nbsp;Share with E-Mail
                            </a>
                            <a onclick="window.vue.toggleDropdown(document.getElementById(''post-share-` + elem.id + `'));" href="sms:000000000?body=` + window.location.origin + '/p/' + elem.all.subreddit + '/' + elem.all.name + '/' + pltitle + ` - ` + elem.title + `" class="dropdown-item is-color-black">
                                <i class="fas fa-sms"></i>&nbsp;Share with SMS
                            </a>
                            <a href="javascript:void(0)" onclick="window.vue.copyToClipboard('` + window.location.origin + '/p/' + elem.all.subreddit + '/' + elem.all.name + '/' + pltitle + ` - ` + elem.title + `'); window.vue.toggleDropdown(document.getElementById('post-share-` + elem.id + `'));" class="dropdown-item is-color-black">
                                <i class="far fa-copy"></i>&nbsp;Copy to Clipboard
                            </a>
                        </div>
                    </div>
                </div>
            `;

            let fav = `<span id="favorite-action-` + elem.all.subreddit + '-' + elem.all.name + `-add" class="` + ((elem.hasFavorited) ? 'is-hidden' : '') + `"><a href="javascript:void(0);" onclick="window.vue.addFavorite('` + elem.all.permalink + `', function(){ document.getElementById('favorite-action-` + elem.all.subreddit + '-' + elem.all.name + `-add').classList.add('is-hidden'); document.getElementById('favorite-action-` + elem.all.subreddit + '-' + elem.all.name + `-remove').classList.remove('is-hidden'); });">Add to favorites</a>&nbsp;|&nbsp;</span><span id="favorite-action-` + elem.all.subreddit + '-' + elem.all.name + `-remove" class="` + ((!elem.hasFavorited) ? 'is-hidden' : '') + `"><a href="javascript:void(0);" onclick="window.vue.removeFavorite('` + elem.all.permalink + `', function(){ document.getElementById('favorite-action-` + elem.all.subreddit + '-' + elem.all.name + `-add').classList.remove('is-hidden'); document.getElementById('favorite-action-` + elem.all.subreddit + '-' + elem.all.name + `-remove').classList.add('is-hidden'); });">Remove from favorites</a>&nbsp;|&nbsp;</span>`;

            let html = `
                <div class="item">
                    <div class="item-header">
                        <div class="item-author">
                            By <a href="` + window.location.origin + `/user/` + elem.author + `">u/` + elem.author + `</a>
                        </div>
            
                        <div class="item-right">
                            <div class="item-date">
                                ` + elem.diffForHumans + `
                            </div>

                            <div class="item-share">
                                ` + share + `
                            </div>
                        </div>
                    </div>
            
                    <div class="item-title">` + elem.title + `</div>
            
                    <div class="item-media" id="item-media-` + elem.all.id + `">
                        ` + mediaContent + `
                    </div>
            
                    <div class="item-footer">
                        <div class="item-comments"><i class="far fa-comments"></i>&nbsp;` + elem.comment_amount + `</div>
                        <div class="item-subscribers"><i class="far fa-grin-stars"></i>&nbsp;` + elem.upvote_amount + `</div>
                        <div class="item-right">
                            ` + fav + `
                            <a href="`+ elem.link + `">View post</a>
                        </div>
                    </div>
                </div>
            `;
            
            return html;
        },

        renderCardImages: function() {
            let elems = document.getElementsByClassName('media-card-item-image');

            for (let i = 0; i < elems.length; i++) {
                let sub = elems[i].title;

                if (sub.indexOf('u/') == 0) {
                    sub = sub.replace('u/', 'user/');
                }
                
                this.ajaxRequest('post', window.location.origin + '/content/sub/image', { sub: sub }, function(response) {
                    if (response.code == 200) {
                        elems[i].innerHTML = '';
                        elems[i].style.backgroundImage = 'url(\"' + response.data.image + '\")';
                    }
                });
            }
        },

        fetchNextVideo: function(target, link) {
            let cats = this.getAllEnabledVideoCategories();
            let catstr = '';

            cats.forEach(function(elem, index) {
                catstr += elem + ',';
            });
            
            this.ajaxRequest('get', window.location.origin + '/content/video?categories=' + catstr, {}, function(response) {
                if (response.code == 200) {
                    document.getElementById(target).innerHTML = '<center><iframe id="media-player" class="media-video" src="https://www.redditmedia.com/mediaembed/' + ((typeof response.data.all.crosspost_parent_list !== 'undefined') ? response.data.all.crosspost_parent_list[0].id : response.data.all.id) + '"></iframe></center>';
                    document.getElementById(link).href = response.data.link;
                    document.getElementById(link).innerHTML = response.data.title;
                } else {
                    console.log(response.msg);
                }
            });
        },

        isVideoCategoryEnabled: function(which) {
            let cookies = document.cookie.split(';');

            for (let i = 0; i < cookies.length; i++) {
                if (cookies[i].indexOf('category_' + which + '=1') !== -1) {
                    return true;
                }
            }

            return false;
        },

        toggleVideoCategoryCookie: function(which) {
            let expDate = new Date(Date.now() + 1000 * 60 * 60 * 24 * 365);

            if (!this.isVideoCategoryEnabled(which)) {
                document.cookie = 'category_' + which + '=1; path=/; expires=' + expDate.toUTCString() + ';';

                let elem = document.getElementById('vcat-' + which);
                if (elem) {
                    elem.classList.add('video-category-enabled');
                }
            } else {
                document.cookie = 'category_' + which + '=0; path=/; expires=' + expDate.toUTCString() + ';';

                let elem = document.getElementById('vcat-' + which);
                if (elem) {
                    elem.classList.remove('video-category-enabled');
                }
            }
        },

        getAllEnabledVideoCategories: function() {
            let result = [];

            let cookies = document.cookie.split(';');

            for (let i = 0; i < cookies.length; i++) {
                if (cookies[i].indexOf('category_') !== -1) {
                    if (cookies[i].indexOf('=1') !== -1) {
                        result.push(cookies[i].substr(cookies[i].indexOf('_') + 1, cookies[i].indexOf('=') - (cookies[i].indexOf('_') + 1)));
                    }
                }
            }

            return result;
        },

        fetchFavorites: function(target) {
            if (typeof window.favoritePagination === 'undefined') {
                window.favoritePagination = null;
            }

            if (document.getElementById('loadmore')) {
                document.getElementById('loadmore').remove();
            }

            document.getElementById(target).innerHTML += '<div id="spinner"><center><br/><i class="fas fa-spinner fa-spin"></i><br/><br/></center></div>';

            window.vue.ajaxRequest('post', window.location.origin + '/favorites', { paginate: window.favoritePagination }, function(response) {
                if (response.code == 200) {
                    if (document.getElementById('spinner')) {
                        document.getElementById('spinner').remove();
                    }

                    if (response.data.length > 0) {
                        response.data.forEach(function(elem, index) {
                            let content = window.vue.renderFavorite(elem.content);

                            document.getElementById(target).innerHTML += content;
                        });

                        window.favoritePagination = response.data[response.data.length - 1].id;

                        document.getElementById(target).innerHTML += `<div id="loadmore"><center><br/><a id="loadmore-anchor" href="javascript:void(0);" onclick="window.vue.fetchFavorites('` + target + `');">Load more</a><br/><br/></center></div>`;
                    } else {
                        if (window.favoritePagination === null) {
                            document.getElementById(target).innerHTML += '<p>You don\'t have added any favorites yet.</p>';
                        }
                    }
                }
            });
        },

        renderFavorite: function(elem) {
            let lnkname = elem.all.name.substr(elem.all.name.indexOf('_') + 1);
            let pltitle = elem.all.permalink.substr(elem.all.permalink.indexOf(lnkname + '/') + lnkname.length + 1);
            pltitle = pltitle.substr(0, pltitle.length - 1);

            const FAV_TITLE_MAXLEN = 20;
            let title = elem.title;
            if (title.length > FAV_TITLE_MAXLEN) {
                title = title.substr(0, FAV_TITLE_MAXLEN - 3) + '...';
            }

            let html = `
                <div id="favorite-` + elem.all.subreddit + `-` + elem.all.name + `" class="favorite-item">
                    <div class="favorite-content">
                        <a href="` + window.location.origin + '/p/' + elem.all.subreddit + '/' + elem.all.name + '/' + pltitle + `">
                            <div class="media-card-item">
                                <div class="media-card-item-title">
                                    ` + title + `
                                </div>

                                <div class="media-card-item-image" style="background-image: url('` + elem.all.thumbnail + `');"></div>
                            </div>
                        </a>
                    </div>
                    <div class="favorite-actions">
                        <a href="javascript:void(0);" onclick="if (confirm('Do you really want to remove this post?')) { window.vue.removeFavorite('` + elem.all.permalink + `', function() { document.getElementById('favorite-` + elem.all.subreddit + `-` + elem.all.name + `').remove(); }); }">Remove</a>
                    </div>
                </div>
            `;

            return html;
        },

        addFavorite: function(ident, successfunc = function(){}) {
            if (ident[0] == '/') {
                ident = ident.substr(1);
            }

            if (ident[ident.length - 1] == '/') {
                ident = ident.substr(0, ident.length - 1);
            }

            window.vue.ajaxRequest('post', window.location.origin + '/favorites/add', { ident: ident }, function(response) {
                if (response.code == 200) {
                    successfunc();
                }
            });
        },

        removeFavorite: function(ident, successfunc = function(){}) {
            if (ident[0] == '/') {
                ident = ident.substr(1);
            }

            if (ident[ident.length - 1] == '/') {
                ident = ident.substr(0, ident.length - 1);
            }

            window.vue.ajaxRequest('post', window.location.origin + '/favorites/remove', { ident: ident }, function(response) {
                if (response.code == 200) {
                    successfunc();
                }
            });
        },

        genFavToken: function(out) {
            window.vue.ajaxRequest('post', window.location.origin + '/favorites/share/generate', {}, function(response) {
                if (response.code == 200) {
                    document.getElementById(out).value = response.token;
                }
            });
        },

        importFavs: function(input) {
            window.vue.ajaxRequest('post', window.location.origin + '/favorites/share/import', { token: document.getElementById(input).value }, function(response) {
                if (response.code == 200) {
                    alert('Import has completed');
                    location.reload();
                }
            });
        },

        toggleSubsOverlay: function() {
            if ((document.getElementById('subs-overlay').style.display === 'none') || (document.getElementById('subs-overlay').style.display === '')) {
                document.getElementById('subs-overlay').style.display = 'inherit';
                document.getElementById('subs-overlay-content').style.maxHeight = window.innerHeight + 'px';
                document.getElementsByTagName('html')[0].style.overflowY = 'hidden';
            } else {
                document.getElementById('subs-overlay').style.display = 'none';
                document.getElementsByTagName('html')[0].style.overflowY = 'inherit';
            }
        },

        renderStats: function(pw, elem, start, end = '') {
            window.vue.ajaxRequest('post', window.location.origin + '/stats/query/' + pw, { start: start, end: end }, function(response){
                if (response.code == 200) {
                    document.getElementById('inp-date-from').value = response.start;
                    document.getElementById('inp-date-till').value = response.end;
                    document.getElementById('count-total').innerHTML = response.count_total;
                    document.getElementById('count-avg-day').innerHTML = Math.round(response.count_total / response.day_diff);
                    document.getElementById('count-avg-hour').innerHTML = Math.round(response.count_total / response.day_diff / 24);

                    let content = document.getElementById(elem);
                    if (content) {
                        let labels = [];
                        let data_total = [];

                        let day = 60 * 60 * 24 * 1000;
                        let dt = new Date(Date.parse(start));

                        for (let i = 0; i <= response.day_diff; i++) {
                            let curDate = new Date(dt.getTime() + day * i);
                            let curDay = curDate.getDate();
                            let curMonth = curDate.getMonth() + 1;

                            if (curDay < 10) {
                                curDay = '0' + curDay;
                            }

                            if (curMonth < 10) {
                                curMonth = '0' + curMonth;
                            }

                            labels.push(curDate.getFullYear() + '-' + curMonth + '-' + curDay);
                            data_total.push(0);
                        }

                        response.data.forEach(function(elem, index) {
                            labels.forEach(function(lblElem, lblIndex){
                                if (lblElem == elem.date) {
                                    data_total[lblIndex] = parseInt(elem.count);
                                }
                            });
                        });

                        const config = {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: 'Visitors',
                                        backgroundColor: 'rgb(163, 73, 164)',
                                        borderColor: 'rgb(163, 73, 164)',
                                        data: data_total,
                                    }
                                ]
                            },
                            options: {
                                scales: {
                                    y: {
                                        ticks: {
                                            beginAtZero: true,
                                            callback: function(value) {if (value % 1 === 0) {return value;}}
                                        }
                                    }
                                }
                            }
                        };

                        if (window.statsChart !== null) {
                            window.statsChart.destroy();
                        }
                        
                        window.statsChart = new Chart(
                            content,
                            config
                        );
                    }
                } else {
                    alert(response.msg);
                }
            });
        },

        updateOnlineCount: function(target, pw) {
            window.vue.ajaxRequest('get', window.location.origin + '/stats/query/' + pw + '/online', {}, function(response) {
                if (response.code == 200) {
                    let elem = document.getElementById(target);
                    if (elem) {
                        elem.innerHTML = response.count;
                    }
                }
            });

            setTimeout(function() { window.vue.updateOnlineCount(target, pw); }, 10000);
        },

        toggleDropdown: function(elem) {
            if (elem.classList.contains('is-active')) {
                elem.classList.remove('is-active');
            } else {
                elem.classList.add('is-active');
            }
        },

        copyToClipboard: function(text) {
            const el = document.createElement('textarea');
            el.value = text;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            alert('Item has been copied to clipboard.');
        },
    }
 });