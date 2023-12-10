(function ($) {
    $("#mvx_visitor_stats_date_filter").change(function (e) {
        var stats_period = $(this).val();
        var visitor_stats = visitor_map_stats[stats_period];
        $('#visitor_data_stats').html('');
        $('#visitor_data_stats').html(visitor_stats.data_stats);
        var colors = Array();
        $.each(visitor_stats.map_stats, function (key, val) {
            colors[key] = val.color;
        });
        $('#vmap').replaceWith("<div id='vmap' style='height: 270px;'></div>");
        var jQuerymap = $('#vmap');
        jQuerymap.vectorMap(
        {
            map: visitor_map_stats.init.map,
            backgroundColor: visitor_map_stats.init.background_color,
            color: visitor_map_stats.init.color,
            colors: colors,
            hoverOpacity: visitor_map_stats.init.hover_opacity, // opacity for :hover
            hoverColor: visitor_map_stats.init.hover_color,
            onLabelShow: function (element, label, code) {
                if (visitor_stats.map_stats[code] !== undefined) {
                    label.html(label.html() + ' - ' + visitor_stats.map_stats[code].hits_count + visitor_map_stats.lang.visitors);
                } else {
                    label.html(label.html() + ' [0]');
                }
            }
        });
    }).change();
})(jQuery);