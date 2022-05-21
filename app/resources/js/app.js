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
        bShowAgeConsent: false
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
            let result = '';
            let cookies = document.cookie.split(';');

            for (let i = 0; i < cookies.length; i++) {
                if (cookies[i].indexOf('sub_selection') !== -1) {
                    result = cookies[i].substr(cookies[i].indexOf('=') + 1);
                    break;
                }
            }

            let selLabel = document.getElementById('current-sub');
            if (selLabel) {
                selLabel.innerHTML = result.substr(0, result.length - 1);
            }

            return result;
        },

        setSubSelection: function(value) {
            let expDate = new Date(Date.now() + 1000 * 60 * 60 * 24 * 365);
            document.cookie = 'sub_selection=' + value + '; path=/; expires=' + expDate.toUTCString() + ';';
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
            
            window.vue.ajaxRequest('post', window.location.origin + '/content/fetch', { sub: sub, sorting: sorting, after: window.fetch_item_after }, function(response){
                if (response.code == 200) {
                    if (document.getElementById('spinner')) {
                        document.getElementById('spinner').remove();
                    }

                    response.data.forEach(function(elem, index){
                        let html = window.vue.renderPost(elem);

                        target.innerHTML += html;
                    });

                    window.fetch_item_after = response.data[response.data.length - 1].all.name;

                    target.innerHTML += `<div id="loadmore"><center><br/><a href="javascript:void(0);" onclick="window.vue.fetchPosts(window.vue.getSubSelection(), window.vue.getPostSorting(), document.getElementById('media-content'));">Load more</a><br/><br/></center></div>`;
                }
            });
        },

        renderIFrame: function(target, src, title) {
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

            let html = `
                <div class="item">
                    <div class="item-header">
                        <div class="item-author">
                            By <a href="` + window.location.origin + `/user/` + elem.author + `">u/` + elem.author + `</a>
                        </div>
            
                        <div class="item-date">
                            ` + elem.diffForHumans + `
                        </div>
                    </div>
            
                    <div class="item-title">` + elem.title + `</div>
            
                    <div class="item-media" id="item-media-` + elem.all.id + `">
                        ` + mediaContent + `
                    </div>
            
                    <div class="item-footer">
                        <div class="item-comments"><i class="far fa-comments"></i>&nbsp;` + elem.comment_amount + `</div>
                        <div class="item-subscribers"><i class="far fa-grin-stars"></i>&nbsp;` + elem.upvote_amount + `</div>
                        <div class="item-goto"><a href="`+ elem.link + `">View post</a></div>
                    </div>
                </div>
            `;

            return html;
        },

        renderCardImages: function() {
            let elems = document.getElementsByClassName('media-card-item-image');

            for (let i = 0; i < elems.length; i++) {
                let sub = elems[i].title;

                this.ajaxRequest('post', window.location.origin + '/content/sub/image', { sub: sub }, function(response) {
                    if (response.code == 200) {
                        elems[i].innerHTML = '';
                        elems[i].style.backgroundImage = 'url(\"' + response.data.image + '\")';
                    }
                });
            }
        },

        renderStats: function(pw, elem, start) {
            window.vue.ajaxRequest('post', window.location.origin + '/stats/query/' + pw, { start: start }, function(response){
                if (response.code == 200) {
                    document.getElementById('range').innerHTML = response.start + ' - ' + response.end;
                    document.getElementById('count-new').innerHTML = response.count_new;
                    document.getElementById('count-recurring').innerHTML = response.count_recurring;

                    let content = document.getElementById(elem);
                    if (content) {
                        let labels = [];
                        let data_new = [];
                        let data_recurring = [];

                        let day = 60 * 60 * 24 * 1000;
                        let dt = new Date(Date.parse(start));

                        for (let i = 0; i <= 30; i++) {
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
                            data_new.push(0);
                            data_recurring.push(0);
                        }

                        response.data.new.forEach(function(elem, index) {
                            labels.forEach(function(lblElem, lblIndex){
                                if (lblElem == elem.date) {
                                    data_new[lblIndex] = parseInt(elem.count);
                                }
                            });
                        });

                        response.data.recurring.forEach(function(elem, index) {
                            labels.forEach(function(lblElem, lblIndex){
                                if (lblElem == elem.date) {
                                    data_recurring[lblIndex] = parseInt(elem.count);
                                }
                            });
                        });

                        const config = {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: 'New visitors',
                                        backgroundColor: 'rgb(255, 99, 132)',
                                        borderColor: 'rgb(255, 99, 132)',
                                        data: data_new,
                                    },
                                    {
                                        label: 'Recurring visitors',
                                        backgroundColor: 'rgb(163, 73, 164)',
                                        borderColor: 'rgb(163, 73, 164)',
                                        data: data_recurring,
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
                        
                        const myChart = new Chart(
                            content,
                            config
                        );
                    }
                } else {
                    alert(response.msg);
                }
            });
        },
    }
 });