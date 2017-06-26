<div class="pct-badge {{ strtolower($label) }} {{ $percent > 75 ? 'positive' : '' }}  {{ $percent < 25 ? 'negative' : '' }}">
    <em>{{ $percent }}<span>%</span></em>
    <span>{{ $label }}</span>
</div>
