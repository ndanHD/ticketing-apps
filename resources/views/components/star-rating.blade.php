@php
    // $rating: float or null
    $max = $max ?? 5;
    $ratingVal = isset($rating) ? (float) $rating : null;
    $rounded = $ratingVal !== null ? round($ratingVal * 2) / 2 : 0;
    $full = $ratingVal !== null ? floor($rounded) : 0;
    $half = $ratingVal !== null ? ($rounded - $full) === 0.5 : false;
    $empty = $max - $full - ($half ? 1 : 0);
@endphp

<style>
    .star-rating { display: inline-flex; align-items: center; gap: .25rem; }
    .star { font-size: 1rem; line-height: 1; display:inline-block; width:1rem; height:1rem; position:relative; color: #e4e5e9; }
    .star.full::before { content: '★'; color: #ffc107; position:absolute; left:0; top:0; }
    .star.empty::before { content: '☆'; color: #e4e5e9; position:absolute; left:0; top:0; }
    /* half star: gray star with gold overlay clipped to 50% */
    .star.half { color: #e4e5e9; }
    .star.half::before { content: '☆'; color: #e4e5e9; position:absolute; left:0; top:0; }
    .star.half::after { content: '★'; color: #ffc107; position:absolute; left:0; top:0; width:50%; overflow:hidden; display:inline-block; }
    .star-text { font-size: .9rem; color: #495057; margin-left: .35rem; }
</style>

<span class="star-rating" aria-hidden="true">
    @if($ratingVal === null)
        @for($i=0;$i<$max;$i++)
            <span class="star empty"></span>
        @endfor
    @else
        @for($i=0;$i<$full;$i++)
            <span class="star full"></span>
        @endfor
        @if($half)
            <span class="star half"></span>
        @endif
        @for($i=0;$i<$empty;$i++)
            <span class="star empty"></span>
        @endfor
    @endif
    @if(isset($count))
        <span class="star-text">@if($ratingVal !== null){{ number_format($ratingVal,1) }} @endif ({{ $count }})</span>
    @else
        @if($ratingVal !== null)
            <span class="star-text">{{ number_format($ratingVal,1) }}</span>
        @endif
    @endif
</span>
