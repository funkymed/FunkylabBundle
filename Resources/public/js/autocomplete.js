var autoTplDefault = function (repo) {
  if (repo.loading) return repo.text;

  var markup = "" +
    "<div class='select2-result-repository clearfix'>" +
    "   <div class='select2-result-repository__meta'>" +
    "       <div class='select2-result-item'>" + repo.title + "</div>"+
    "       <div class='select2-result-repository__meta'>" + repo.summary + "</div>" +
    "   </div>" +
    "</div>";

  return markup;
};

var autoTplSelectionDefault =  function (repo) {
  return repo.title;
};

function autocomplete(idSelect,SF2Route,templateResult,templateSelection)
{
  if(!templateResult)
    templateResult = autoTplDefault;
  if(!templateSelection)
    templateSelection = autoTplSelectionDefault;

  $('#'+idSelect).select2({
    ajax: {
      url: Routing.generate(SF2Route),
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term,
          page: params.page
        };
      },
      processResults: function (data, params) {
        params.page = params.page || 1;
        return {
          results: data.items,
          pagination: {
            more: (params.page * 30) < data.total_count
          }
        };
      },
      cache: true
    },
    escapeMarkup: function (markup) { return markup; },
    templateResult: templateResult,
    templateSelection: templateSelection
  });

  $('#'+idSelect).parent().find('.select2-selection__rendered').html($('#'+idSelect+' option').html());
}