<nav class="navbar is-dark {{ ((env('APP_ENABLEPWA')) ? 'is-fixed-top' : '') }}" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item navbar-item-brand is-title-font has-no-underline" href="{{ url('/') }}">
            <img src="{{ asset('img/logo.png') }}" alt="Logo"/>&nbsp;<h1>{{ env('APP_NAME') }}</h1>
        </a>

        <a id="burger-action" role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>

    <div id="navbarBasicExample" class="navbar-menu">
        <div class="navbar-start"></div>

        @if (isset($subs))
        <div class="navbar-options nav-hidden-desktop">
            <center>
                <div class="navbar-item is-navbar-button is-inline-block navbar-item-hover-color">
                    <a id="link-sorting-hot" class="link-sorting-hot button is-danger has-no-underline navbar-item-hover-color" href="javascript:void(0);" onclick="window.vue.setPostSorting('hot'); document.getElementById('media-content').innerHTML = ''; window.fetch_item_after = null; window.vue.fetchPosts(window.vue.getSubSelection(), window.vue.getPostSorting(), document.getElementById('media-content')); window.vue.setSortingUnderline('hot'); document.getElementById('burger-action').click();">
                        Hot
                    </a>
                </div>

                <div class="navbar-item is-inline-block">
                    <a id="link-sorting-top" class="link-sorting-top navbar-item has-no-underline navbar-item-hover-color" href="javascript:void(0);" onclick="window.vue.setPostSorting('top'); document.getElementById('media-content').innerHTML = ''; window.fetch_item_after = null; window.vue.fetchPosts(window.vue.getSubSelection(), window.vue.getPostSorting(), document.getElementById('media-content')); window.vue.setSortingUnderline('top'); document.getElementById('burger-action').click();">
                        <i class="fas fa-star star-color"></i>&nbsp;Top
                    </a>
                </div>

                <div class="navbar-item is-inline-block">
                    <a id="link-sorting-new" class="link-sorting-new navbar-item has-no-underline navbar-item-hover-color" href="javascript:void(0);" onclick="window.vue.setPostSorting('new'); document.getElementById('media-content').innerHTML = ''; window.fetch_item_after = null; window.vue.fetchPosts(window.vue.getSubSelection(), window.vue.getPostSorting(), document.getElementById('media-content')); window.vue.setSortingUnderline('new'); document.getElementById('burger-action').click();">
                        New
                    </a>
                </div>
            </center>
        </div>
        @endif

        <div class="navbar-end">
            @if (env('APP_ENABLEAPPPAGE'))
            <a class="navbar-item has-no-underline" href="{{ url('/getapp') }}">
                <span class="navbar-item-icon is-mobile-only"><i class="fas fa-mobile-alt"></i></span>
                <span class="navbar-item-label">App</span>
            </a>
            @endif

            <a class="navbar-item has-no-underline" href="{{ url('/video') }}">
                <span class="navbar-item-icon is-mobile-only"><i class="fas fa-video"></i></span>
                <span class="navbar-item-label">Videos</span>
            </a>

            <a class="navbar-item has-no-underline" href="{{ url('/creators') }}">
                <span class="navbar-item-icon is-mobile-only"><i class="fas fa-users"></i></span>
                <span class="navbar-item-label">Creators</span>
            </a>

            @if (AuthModel::isAuthenticated())
            <a class="navbar-item has-no-underline" href="{{ url('/favorites') }}">
                <span class="navbar-item-icon is-mobile-only"><i class="fas fa-heart"></i></span>
                <span class="navbar-item-label">Favorites</span>
            </a>
            @endif

            @if (isset($subs))
            <div class="navbar-item has-dropdown is-hoverable" id="navbar-subs-dropdown">
                <a class="navbar-link has-no-underline">
                    <span class="navbar-item-icon is-mobile-only"><i class="fas fa-th"></i></span>
                    <span class="navbar-item-label">Subs</span>
                </a>

                <div class="navbar-dropdown" id="navbar-dropdown-toggle">
                    @if (isset($subs))
                        <?php $lastCat = ''; ?>
                        @for ($i = 0; $i < $subs->count(); $i++)
                            <?php 
                                if ($lastCat !== $subs->get($i)->get('category')) {
                                    $lastCat = $subs->get($i)->get('category');

                                    echo '<div class="navbar-item is-nav-category">' . $subs->get($i)->get('category') . '</div>';
                                } 
                            ?>

                            <a class="navbar-item is-nav-item" href="{{ url('/' . $subs->get($i)->get('sub_ident')) }}" onclick="event.stopPropagation(); return true;">
                                {{ $subs->get($i)->get('sub_ident') }}
                            </a>
                        @endfor
                    @endif

                    @if (env('APP_ALLOWCUSTOMSUBS'))
                        <div class="navbar-item is-nav-category">Custom</div>
                        <div>
                            <form><div class="navbar-item is-nav-item">r/<input type="text" class="input-dark" placeholder="sub" onkeypress="if (event.which === 13) { event.preventDefault(); window.vue.customSub(this.value); }"></div></form>
                            <form><div class="navbar-item is-nav-item">u/<input type="text" class="input-dark" placeholder="user" onkeypress="if (event.which === 13) { event.preventDefault(); window.vue.customUser(this.value); }"></div></form>
                        </div>
                    @endif
                </div>
            </div>
            @else
            <a class="navbar-item has-no-underline" href="{{ url('/') }}">
                <span class="navbar-item-icon is-mobile-only"><i class="fas fa-compass" title="Browse"></i></span>
                <span class="navbar-item-label">Browse</span>
            </a>
            @endif

            <a class="navbar-item has-no-underline" href="javascript:void(0);" onclick="{{ ((AuthModel::isAuthenticated()) ? 'window.vue.bShowUserSettings = true;' : 'location.href = window.location.origin + \'/auth\';') }}">
                <span class="navbar-item-icon"><i class="fas fa-user" title="{{ ((AuthModel::isAuthenticated()) ? 'User settings' : 'Login') }}"></i></span>
                <span class="navbar-item-label is-mobile-only">{{ ((AuthModel::isAuthenticated()) ? 'User settings' : 'Login') }}</span>
            </a>

            @if (AuthModel::isAuthenticated())
            <a class="navbar-item has-no-underline" href="{{ url('/logout') }}">
                <span class="navbar-item-icon"><i class="fas fa-sign-out-alt" title="Logout"></i></span>
                <span class="navbar-item-label is-mobile-only">Logout</span>
            </a>
            @endif
        </div>
    </div>
</nav>