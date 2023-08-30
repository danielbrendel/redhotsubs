<div class="page">
    <div class="page-content">
        <div class="item">
            <div class="item-header">
                <div class="item-author">
                    By <a href="{{ url('/user/' . $post_data->author) }}">u/{{ $post_data->author }}</a>
                </div>
    
                <div class="item-date">
                    {{ $post_data->diffForHumans }}
                </div>
            </div>
    
            <div class="item-title">{{ $post_data->title }}</div>
    
            <div class="item-media" id="item-media-{{ $post_data->all->id }}">
                @if ($post_data->all->domain === 'redgifs.com')
                    <center>
                    <div id="media-video" class="media-video-preview is-pointer" onclick="window.vue.renderIFrame(document.getElementById('item-media-{{ $post_data->all->id }}'), 'https://www.redditmedia.com/mediaembed/{{ $post_data->all->id }}');" style="background-image: url('{{ $post_data->all->thumbnail }}');">
                        <div class="media-video-preview-overlay">    
                            <div class="media-video-preview-inner">
                                <i class="fas fa-play-circle"></i>&nbsp;Play
                            </div>
                        </div>
                    </div></center>
                @else
                    <a href="{{ $post_data->media }}" target="_blank"><img src="{{ $post_data->media }}" alt="{{ $post_data->title }}"/></a>
                @endif
            </div>
    
            <div class="item-footer">
                <div class="item-comments"><i class="far fa-comments"></i>&nbsp;{{ $post_data->comment_amount }}</div>
                <div class="item-subscribers"><i class="far fa-grin-stars"></i>&nbsp;{{ $post_data->upvote_amount }}</div>
                <div class="item-right">
                    <span id="favorite-action-{{ $post_data->all->subreddit }}-{{ $post_data->all->name }}-add" class="{{ ($post_data->hasFavorited) ? 'is-hidden' : '' }}">
                        <a href="javascript:void(0);" onclick="window.vue.addFavorite('{{ $post_data->all->permalink }}', function() { document.getElementById('favorite-action-{{ $post_data->all->subreddit }}-{{ $post_data->all->name }}-add').classList.add('is-hidden'); document.getElementById('favorite-action-{{ $post_data->all->subreddit }}-{{ $post_data->all->name }}-remove').classList.remove('is-hidden'); });"><i class="fas fa-plus fav-icon-add"></i>&nbsp;Favorites</a>&nbsp;|&nbsp;
                    </span>

                    <span id="favorite-action-{{ $post_data->all->subreddit }}-{{ $post_data->all->name }}-remove" class="{{ (!$post_data->hasFavorited) ? 'is-hidden' : '' }}">
                        <a href="javascript:void(0);" onclick="window.vue.removeFavorite('{{ $post_data->all->permalink }}', function() { document.getElementById('favorite-action-{{ $post_data->all->subreddit }}-{{ $post_data->all->name }}-add').classList.remove('is-hidden'); document.getElementById('favorite-action-{{ $post_data->all->subreddit }}-{{ $post_data->all->name }}-remove').classList.add('is-hidden'); });"><i class="fas fa-minus fav-icon-remove"></i>&nbsp;Favorites</a>&nbsp;|&nbsp;
                    </span>
                    
                    <a href="{{ $post_data->link }}">View post</a>
                </div>
            </div>
        </div>
    </div>
</div>