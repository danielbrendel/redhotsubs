@if (FlashMessage::hasMsg('success'))
    <div id="success-message">
        <article class="message is-success">
            <div class="message-header">
                <p>Success</p>
                <button class="delete" aria-label="delete" onclick="document.getElementById('success-message').style.display = 'none';"></button>
            </div>
            <div class="message-body">
                {!! FlashMessage::getMsg('success') !!}
            </div>
        </article>
    </div>
@endif

@if (FlashMessage::hasMsg('error'))
    <div id="error-message">
        <article class="message is-danger">
            <div class="message-header">
                <p>Error</p>
                <button class="delete" aria-label="delete" onclick="document.getElementById('error-message').style.display = 'none';"></button>
            </div>
            <div class="message-body">
                {!! FlashMessage::getMsg('error') !!}
            </div>
        </article>
    </div>
@endif