<div class="pct-score {{ strtolower($label) }} {{ $percent > 75 ? 'positive' : '' }}  {{ !is_null($percent) && $percent < 25 ? 'negative' : '' }}">
    <em>{{ is_null($percent) ? "N/A" : $percent . "<span>%</span>" }}</em>
    <span>{{ $label }}</span>
</div>
