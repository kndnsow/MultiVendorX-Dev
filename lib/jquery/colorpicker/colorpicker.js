(function($) {
  "use strict";
  function pickColor(color, element) {
    element.val(color);
  }
  function toggle_text(link_color) {
    if ("" === link_color.val().replace("#", "")) {
      link_color.val(default_color);
      pickColor(default_color, link_color);
    } else pickColor(link_color.val(), link_color);
  }
  var default_color = "#fbfbfb";
  $(document).ready(function() {
    $(".colorpicker").each(function() {
      var link_color = $(this); 
      link_color.wpColorPicker({
        change: function(event, ui) {
          pickColor(link_color.wpColorPicker("color"), link_color);
        },
        clear: function() {
          pickColor("", link_color);
        }
      });
    });
    $(".colorpicker").each(function() {
      $(this).click(function() { toggle_text($(this)); });
      toggle_text($(this));
    });
  });
})(jQuery);