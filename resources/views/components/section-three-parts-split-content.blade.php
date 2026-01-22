<div class="section-split-wrapper" style="display: flex; flex-direction: column; gap: 1.5rem;">
    @if(isset($left))
        <div style="flex: 1;">
            {{ $left }}
        </div>
    @endif

    @if(isset($right))
        <div style="flex: 1;">
            {{ $right }}
        </div>
    @endif
</div>

<style>
@media (min-width: 768px) {
    .section-split-wrapper {
        flex-direction: row !important;
    }
}
</style>
