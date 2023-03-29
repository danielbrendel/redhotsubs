<div class="page">
	<h1>{{ $page_title }}</h1>

    <p>You can contact us by submitting this form.</p>

    <div class="page-content">
        <form>
            <div class="field">
                <label class="label">Your Name</label>
                <div class="control">
                    <input class="input" type="text" id="name" placeholder="Please enter your name" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Your E-Mail</label>
                <div class="control">
                    <input class="input" type="email" id="email" placeholder="Please enter a valid e-mail address" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Subject</label>
                <div class="control">
                    <select class="input" id="subject">
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->get('subject') }}">{{ $subject->get('subject') }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="field">
                <label class="label">Message</label>
                <div class="control">
                    <textarea class="textarea" id="content" placeholder="Please describe your issue" required></textarea>
                </div>
            </div>

            <div class="field">
                <label class="label">{{ $captcha[0] }} + {{ $captcha[1] }} = ?</label>
                <div class="control">
                    <input class="input" type="text" id="captcha" required>
                </div>
            </div>
        </form>

        <p>&nbsp;</p>

        <div class="field">
            <div class="control">
                <button class="button is-link" onclick="window.vue.contactRequest();" onsubmit="return false;">Submit</button>
            </div>
        </div>
    </div>
</div>