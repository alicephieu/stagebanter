jQuery(function($) {
  
  $.each(gfcpt_linked_selects.items, function() {
    var linked_item = this;
    
    $(linked_item.target).attr('disabled','disabled');
    
    var $source = $(linked_item.source);
    
    $source.change(function() {
      var $this = $(this);
      var $target = $(linked_item.target);
      
      $target.find('option').remove();

      if ( parseInt($this.val()) > 0 ) {
        $target.removeAttr('disabled');
        $target.append('<option selected="selected" value="">'+linked_item.default_option+'</option>');
        $.each(linked_item.terms, function() {
          var term = this;
          if (term.parent == $this.val()) {
            $target.append('<option value="'+term.id+'">'+term.name+'</option>');
          }
        });
      } else {
        $target.attr('disabled','disabled').append('<option value="">'+linked_item.unselected+'</option>');
      }
    });
    
    //if we already have a value selected, then force the change event to fire!
    if ( parseInt($source.val()) > 0 ) {
      $source.change();
    }
    
  });

});
