<div id="news-widget-{{$top_category->term->slug}}" class="news-widget mb-3" style="background: rgba(0,0,0,0.7);">
  <div id="news-widget-{{$top_category->term->slug}}-header" class="p-2">
    <h2 class="text-white text-center mb-0 mt-0" style="font-size: 1rem; font-weight: bold;">Latest News</h2>
    <div style="font-size: 0.7rem;" class="mt-1"><i class="fas fa-angle-double-left"></i> {{$top_category->term->name}} <i class="fas fa-angle-double-right"></i></div>
  </div>
  <div id="news-listing-{{$top_category->term->slug}}" class="p-2 text-left" style="font-size: 0.7rem;">
    <div class="loader"></div>
  </div>
</div>