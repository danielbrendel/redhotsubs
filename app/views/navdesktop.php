<div class="navbar-desktop" id="navbar-desktop">
    @if (isset($subs))
        <?php $lastCat = ''; ?>
        @for ($i = 0; $i < $subs->count(); $i++)
            <?php 
                if ($lastCat !== $subs->get($i)->get('category')) {
                    $lastCat = $subs->get($i)->get('category');

                    echo '<div class="navbar-item is-nav-category">' . $subs->get($i)->get('category') . '</div>';
                } 
            ?>

            <div class="media-list-item">
                <a class="" href="{{ url('/' . $subs->get($i)->get('sub_ident')) }}">
                    {{ $subs->get($i)->get('sub_ident') }}
                </a>
            </div>
        @endfor
    @endif

    @if (env('APP_ALLOWCUSTOMSUBS'))
        <div class="navbar-item is-nav-category">Custom</div>
        <span class="navbar-item is-nav-item">r/<input type="text" class="input-dark" placeholder="name" onkeypress="if (event.which === 13) { window.vue.customSub(this.value); }"></span>
    @endif
</div>