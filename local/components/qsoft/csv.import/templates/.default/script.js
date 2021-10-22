$(function(){
    $progress = $('#csv-import-progress');
    var itemsCount = parseInt($('#max_value').val());
    var canceled = false;

    if(itemsCount) {
        $progress.append(progressBar.getContainer());
    }
    
    $button = $('#csv-import-start');

    $button.on('click', function (e) {
        e.preventDefault();
        $button.attr('disabled', true);

        processChunk();
    });
    var processChunk =  function (start = 0, end = 100) {
        var request = BX.ajax.runComponentAction('qsoft:csv.import', 'processChunk', {data: {start: start, end: end, tmpFile: $('#tmp_file').val()}});
        request.then(function (response) {
            progressBar.update(response.data.processed);
            if(response.data.finished) {
                $button.hide();
                $('#csv-import-cancel').val('Далее');
                $('#csv-import-preview').html('<h2  style="text-align: center">Импорт успешно завершен</h2>');
            } else {
                if(!canceled) {
                    processChunk(start + end, end)
                }
            }

        });
    }
});

