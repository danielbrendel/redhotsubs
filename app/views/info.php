@if (AppSettingsModel::hasInfo())
    <div class="info info-box-{{ AppSettingsModel::getInfoStyle() }}">
        <div class="info-title info-header-{{ AppSettingsModel::getInfoStyle() }}">Information</div>

        <div class="info-content info-content-{{ AppSettingsModel::getInfoStyle() }}">{!! AppSettingsModel::getInfo() !!}</div>
    </div>
@endif