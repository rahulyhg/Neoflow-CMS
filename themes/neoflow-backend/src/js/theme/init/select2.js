// Load language file for select2
$.getScript(THEME_URL + '/js/select2/i18n/' + LANGUAGE_CODE + '.js', function () {

    $('select')
            .select2({
                theme: 'bootstrap',
                // minimumResultsForSearch: -1,
                matcher: function (params, data) {

                    function internalMatcher(params, data) {
                        var original_matcher = $.fn.select2.defaults.defaults.matcher;
                        var result = original_matcher(params, data);

                        var dataDescription = data.element['attributes']['data-description'];
                        if (typeof dataDescription !== 'undefined') {
                            if (dataDescription['nodeValue'].toLowerCase().indexOf(params.term) > -1) {
                                result = $.extend({}, data, true);
                            }
                        }

                        return result;
                    }

                    var result = internalMatcher(params, data);

                    if (result && data.children && result.children && data.children.length != result.children.length) {
                        for (var i = data.children - 1; i >= 0; i--) {
                            var child = data.children[i];

                            var childResult = internalMatcher(result, child);
                            if (childResult) {
                                result = $.extend(result, childResult, true);
                            }
                        }

                        if (result && result.children.length === 0) {
                            return null;
                        }

                    }
                    return result;
                },
                language: LANGUAGE_CODE,
                width: '100%',
                tokenSeparators: [',', ' '],
                templateResult: function (item) {
                    if (item.hasOwnProperty('element')) {
                        var $element = $(item.element);
                        if ($element.data('level')) {
                            var level = $element.data('level');
                            return $('<span style="padding-left:' + (20 * parseInt(level)) + 'px;">' + item.text + '</span>');
                        } else if ($element.data('description')) {
                            var description = $element.data('description');
                            return $('<span>' + item.text + '</span><br /><small>' + description + '</small>');
                        }
                    }
                    return item.text;
                }
            })
            .focus(function () {
                $(this).select2('open');
            });
});