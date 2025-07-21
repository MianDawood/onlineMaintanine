<style> 

  .search-wrapper {
    position: absolute;
    top: 0;
    left: 100%;
    margin-left: 10px;
    width: 320px;
    max-height: 300px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid #ccc;
    z-index: 9999;
    padding: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  }
  .each_tags,
  .paragraph_lists li {
    background: #f1f1f1;
    margin-bottom: 6px;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 14px;
  }
  
  
  .search_title {
    position: absolute;
    top: 100px;
    left: 100px;
    width: 300px;
    z-index: 1050;
    background: #fff;
    border: 1px solid #ccc;
    padding: 10px;
    border-radius: 4px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
  }
  
  #draggableBoxSearch {
    background: #4CAF50;
    color: white;
    padding: 8px;
    cursor: move;
    text-align: center;
    border-radius: 4px;
    font-weight: bold;
    margin-bottom: 10px;
  }
  
  .search_title .filter-field {
    margin-bottom: 10px;
  }
  
  .search_title input[type="text"] {
    width: 100%;
    padding: 6px 8px;
    font-size: 13px;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  
  #closeDraggable {
    background: none;
    border: none;
    font-size: 16px;
    font-weight: bold;
    color: white;
    cursor: pointer;
  }
  
  
  
  </style>


<div>
  <button id="reopenDraggable" style="display: none; top: 10px; left: 10px;  padding: 6px 10px; font-size: 14px;">🔍 Reopen Filter</button>
</div>
<!-- Draggable Box -->
<div class="search_title" id="draggableBox">
<div class="draggableSearch" id="draggableBoxSearch">
Click here to move
<button id="closeDraggable" style="float: right; font-size: 12px; background: transparent; border: none; color: white; cursor: pointer;">✕</button>
</div>
<input type="text" name="search_title" class="title_keyword" placeholder="filter title:">
<input type="text" name="search_parag" class="parag_keyword" placeholder="filter paragraph:">
<input type="text" name="search_meta_title" class="meta_title_keyword" placeholder="filter meta title:">
<input type="text" name="search_meta_des" class="meta_des_keyword" placeholder="filter meta description:">
<input type="text" name="search_qs_urls" class="filter_qs_urls" placeholder="filter qs urls:">
<input type="text" name="search_urls" class="filter_urls" placeholder="filter urls:">
</div>




<script>

$(document).ready(function () {
  $('#closeDraggable').on('click', function (e) {
    e.stopPropagation();
    $('#draggableBox').hide();
    $('#reopenDraggable').show();
  });

  $('#reopenDraggable').on('click', function () {
    $('#draggableBox').show();
    $(this).hide();
  });
});

$(document).on('keyup', 'input.title_keyword', function(){
    var txt = $(this).val();
    $(document).find('div.custom_title_section div.title_section').children().hide();
    $(document).find('.custom_title_section div.title_section').children().each(function(i, obj) {
      if($(obj).html().toUpperCase().indexOf(txt.toUpperCase()) != -1) {
        $(obj).show();
      }
    })
  })  
  $(document).on('keyup','.meta_title_keyword',function(){  
      var searchWarpper = $(this).closest('.search_title');
      searchWarpper.find('div.meta_title_section ul').children().hide();
      var txt = $(this).val();
      searchWarpper.find('div.meta_title_section ul').children().each(function(i,v) {
        if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1) {
          $(this).show();
        }
      })
  })
  $(document).on('keyup','.meta_des_keyword',function(){  
      var searchWarpper = $(this).closest('.search_title');
      searchWarpper.find('div.meta_des_section ul').children().hide();
      var txt = $(this).val();
      searchWarpper.find('div.meta_des_section ul').children().each(function(i,v) {
        if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1) {
          $(this).show();
        }
      })
  })
  $(document).on('keyup','.parag_keyword',function(){  
      var searchWarpper = $(this).closest('.search_title');
      searchWarpper.find('div.parag_section ul').children().hide();
      var txt = $(this).val();
      searchWarpper.find('div.parag_section ul').children().each(function(i,v) {
        if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1) {
          $(this).show();
        }
      })
  })
  
  $(document).on('keyup','input.filter_qs_urls',function(){  
      var searchWarpper = $(this).closest('.search_title');
      searchWarpper.find('div.custom_qs_url_section ul').children().hide();
      var txt = $(this).val();
      searchWarpper.find('div.custom_qs_url_section ul').children().each(function(i,v) {
        if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1) {
          $(this).show();
        }
      })
  })
  $(document).on('keyup','input.filter_urls',function(){  
      var searchWarpper = $(this).closest('.search_title');
      searchWarpper.find('div.custom_url_section ul').children().hide();
      var txt = $(this).val();
      searchWarpper.find('div.custom_url_section ul').children().each(function(i,v) {
        if($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1) {
          $(this).show();
        }
      })
  })
  


function toggleDropdown($input, dataList, sectionClass, ulClass) {
  const wrapper = $input.closest('.search_title');

  // Remove all dropdowns
  wrapper.find('.search-wrapper').remove();

  // If already focused, remove and return
  if ($input.hasClass('active_input')) {
    $input.removeClass('active_input');
    return;
  }

  // Clear other inputs' active class
  $('.search_title input').removeClass('active_input');
  $input.addClass('active_input');

  const dropdownHTML = createDropdownHTML(dataList, sectionClass, ulClass);
  $input.after(dropdownHTML);
}

// Attach handlers
$(document).on('click', '.search_title input.title_keyword', function (e) {
  e.stopPropagation();
  const custom_tags = <?php echo json_encode($custom_tags); ?>;
  toggleDropdown($(this), custom_tags, 'custom_title_section', 'title_section');
});

$(document).on('click', '.search_title input.parag_keyword', function (e) {
  e.stopPropagation();
  const paragraph_lists = <?php echo json_encode($paragraph_lists); ?>;
  toggleDropdown($(this), paragraph_lists, 'custom_parag_section', 'parag_section');
});

$(document).on('click', '.search_title input.meta_title_keyword', function (e) {
  e.stopPropagation();
  const meta_title_lists = <?php echo json_encode($meta_title_lists); ?>;
  toggleDropdown($(this), meta_title_lists, 'custom_meta_title_section', 'meta_title_section');
});

$(document).on('click', '.search_title input.meta_des_keyword', function (e) {
  e.stopPropagation();
  const meta_desc_lists = <?php echo json_encode($meta_desc_lists); ?>;
  toggleDropdown($(this), meta_desc_lists, 'custom_meta_des_section', 'meta_des_section');
});

$(document).on('click', '.search_title input.filter_qs_urls', function (e) {
  e.stopPropagation();
  const qs_urls = <?php echo json_encode($qs_urls); ?>;
  toggleDropdown($(this), qs_urls, 'custom_qs_url_section', 'qs_url_section');
});

$(document).on('click', '.search_title input.filter_urls', function (e) {
  e.stopPropagation();
  const url_tags = <?php echo json_encode($url_tags); ?>;
  toggleDropdown($(this), url_tags, 'custom_url_section', 'url_section');
});

// Clicking outside will remove dropdowns
$(document).on('click', function (e) {
  if (!$(e.target).closest('.search_title').length) {
    $('.search-wrapper').remove();
    $('.search_title input').removeClass('active_input');
  }
});








</script>