<nav class="navbar is-dark" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item navbar-item-brand is-title-font" href="{{ url('/') }}">
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
                <div class="navbar-item is-navbar-button is-inline-block">
                    <a id="link-sorting-hot" class="link-sorting-hot button is-danger" href="javascript:void(0);" onclick="window.vue.setPostSorting('hot'); document.getElementById('media-content').innerHTML = ''; window.fetch_item_after = null; window.vue.fetchPosts(window.vue.getSubSelection(), window.vue.getPostSorting(), document.getElementById('media-content')); window.vue.setSortingUnderline('hot'); document.getElementById('burger-action').click();">
                        Hot
                    </a>
                </div>

                <div class="navbar-item is-inline-block">
                    <a id="link-sorting-top" class="link-sorting-top navbar-item" href="javascript:void(0);" onclick="window.vue.setPostSorting('top'); document.getElementById('media-content').innerHTML = ''; window.fetch_item_after = null; window.vue.fetchPosts(window.vue.getSubSelection(), window.vue.getPostSorting(), document.getElementById('media-content')); window.vue.setSortingUnderline('top'); document.getElementById('burger-action').click();">
                        <i class="fas fa-star star-color"></i>&nbsp;Top
                    </a>
                </div>

                <div class="navbar-item is-inline-block">
                    <a id="link-sorting-new" class="link-sorting-new navbar-item" href="javascript:void(0);" onclick="window.vue.setPostSorting('new'); document.getElementById('media-content').innerHTML = ''; window.fetch_item_after = null; window.vue.fetchPosts(window.vue.getSubSelection(), window.vue.getPostSorting(), document.getElementById('media-content')); window.vue.setSortingUnderline('new'); document.getElementById('burger-action').click();">
                        New
                    </a>
                </div>
            </center>
        </div>
        @endif

        <div class="navbar-end">
            @if (env('APP_ENABLEAPPPAGE'))
            <a class="navbar-item" href="{{ url('/getapp') }}">
                App
            </a>
            @endif

            <a class="navbar-item" href="{{ url('/video') }}">
                Videos
            </a>

            <a class="navbar-item" href="{{ url('/creators') }}">
                Creators
            </a>

            <a class="navbar-item" href="{{ url('/favorites') }}">
                Favorites
            </a>

            @if (isset($subs))
            <div class="navbar-item has-dropdown is-hoverable" id="navbar-subs-dropdown">
                <a class="navbar-link">
                    Subs
                </a>

                <div class="navbar-dropdown">
                    @if (isset($subs))
                        <?php $lastCat = ''; ?>
                        @for ($i = 0; $i < $subs->count(); $i++)
                            <?php 
                                if ($lastCat !== $subs->get($i)->get('category')) {
                                    $lastCat = $subs->get($i)->get('category');

                                    echo '<div class="navbar-item is-nav-category">' . $subs->get($i)->get('category') . '</div>';
                                } 
                            ?>

                            <a class="navbar-item is-nav-item" href="{{ url('/' . $subs->get($i)->get('sub_ident')) }}">
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
            <a class="navbar-item" href="{{ url('/') }}">
                Browse
            </a>
            @endif
        </div>
    </div>
</nav>