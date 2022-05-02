@if (AppSettingsModel::hasInfo())
    <div class="info">
        <div class="info-title">Information</div>

        <div class="info-content">{!! AppSettingsModel::getInfo() !!}</div>
    </div>
@endif