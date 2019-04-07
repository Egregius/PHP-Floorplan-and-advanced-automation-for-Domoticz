function navigator_Go(url) {window.location.assign(url);}
function toggle_visibility(id){var e=document.getElementById(id);if(e.style.display=='inherit') e.style.display='none';else e.style.display='inherit';}
$(document).ready(function() {
  var qsRegex;
  var buttonFilter;
  var $grid = $('.grid').isotope({
    itemSelector: '.element-item',
    layoutMode: 'fitRows',
    filter: function() {
      var $this = $(this);
      var searchResult = qsRegex ? $this.text().match(qsRegex) : true;
      var buttonResult = buttonFilter ? $this.is(buttonFilter) : true;
      return searchResult && buttonResult;
    },
	  getSortData: {title: '.title',size: '.size parseInt',jaar: '.date parseInt',duur: '.duur parseInt',},
	  sortAscending:{title: true,size: false,jaar: false,duur: true,}
  });
	$('.sort-by-button-group').on('click', 'button', function(){
	  var sortValue = $(this).attr('data-sort-value');
	  $grid.isotope({sortBy: sortValue});
	});
  $('#filters').on('click', 'button', function() {
    buttonFilter = $(this).attr('data-filter');
    $grid.isotope();
  });

  var $quicksearch = $('#quicksearch').keyup(debounce(function() {
    qsRegex = new RegExp($quicksearch.val(), 'gi');
    $grid.isotope();
  }));

  $('.button-group').each(function(i, buttonGroup) {
    var $buttonGroup = $(buttonGroup);
    $buttonGroup.on('click', 'button', function() {
      $buttonGroup.find('.is-checked').removeClass('is-checked');
      $(this).addClass('is-checked');
    });
  });

});

function debounce(fn, threshold) {
  var timeout;
  return function debounced() {
    if (timeout) {
      clearTimeout(timeout);
    }
    function delayed() {
      fn();
      timeout = null;
    }
    setTimeout(delayed, threshold || 700);
  };
}
