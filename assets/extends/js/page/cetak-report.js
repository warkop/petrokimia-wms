
    function cetak(param = null, preview = null){
        if(param !== null){
            if(preview == 'preview')
            var data = $('#form-report').serialize()+"&preview=true"
            else
            var data = $('#form-report').serialize()
            $.ajax({
                type:'GET',
                headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data:data,
                url : baseUrl + '/report/'+param,
                beforeSend:function(){
                    $('.se-pre-con').show()
                },
                success : function(response){
                    $('.se-pre-con').hide()
                    $('#error-msg').html('')
                    window.open(baseUrl + 'report/'+param+'?'+data+'&validate=true');
                },
                error : function(response){
                    $('.se-pre-con').hide()
                    $('#error-msg').html(response.responseJSON.data)
                }
            })
        }
    }
    