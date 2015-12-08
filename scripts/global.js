    $.fn.hoverablePopover = function(options) {
        this.each(function() {
        var $button = $(this);
        var popoverVisible = false;
        var hideTimer = null;

        var show = function() {
          clearTimeout(hideTimer);
          hideTimer = null;
          if (!popoverVisible) {
            $button.popover('show');
            popoverVisible = true;
          }
        };

        var hide = function() {
          hideTimer = setTimeout(function() {
            $button.popover('hide');
            popoverVisible = false;
          }, 100);
        };

        $button.popover(options);
        $button.on('mouseleave', hide);
        $button.on('mouseenter', function() {
          show();
          $button.siblings('.popover')
            .off()
            .on('mouseenter', show)
            .on('mouseleave', hide);
        });
      });
    };