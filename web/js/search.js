jQuery(function ($) {
    $('#search').submit(function (e) {
        e.preventDefault();

        $(this).find('.alert-warning').hide();
        var resultDiv = $(this).find('.result');
        resultDiv.empty()
            .hide();

        var repository = $(this).find('#search-input').val();

        if (repository.length === 0 || repository.indexOf('/') === -1) {
            $(this).find('.alert-warning').show();
            return;
        }

        var loadingDiv = $('<div><i class="fa fa-spinner fa-spin"></i> computing, please wait</div>');

        resultDiv.append('<h4>' + repository + '</h4>')
            .append(loadingDiv)
            .show();

        $.get('/check/' + repository, function (html) {
            loadingDiv.html(html);
        });
    });
});
