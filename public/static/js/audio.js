$(function () {

    function __log(text) {
        console.log(text);
    }

    var audio_context;
    var recorder;

    function startUserMedia(stream) {
        var input = audio_context.createMediaStreamSource(stream);
        __log('Media stream created.');

        // Uncomment if you want the audio to feedback directly
        //input.connect(audio_context.destination);
        //__log('Input connected to audio context destination.');

        recorder = new Recorder(input);
        __log('Recorder initialised.');
    }

    function startRecording() {
        $('#recordWav').text('Идет запись');
        recorder && recorder.record();
        __log('Recording...');
    }

    function stopRecording() {
        $('#recordWav').text('Записать');
        recorder && recorder.stop();
        __log('Stopped recording.');

        sendToServer();

        recorder.clear();
    }

    function sendToServer() {
        recorder && recorder.exportWAV(function(blob) {

            var form_data = new FormData();
            form_data.append('file', blob);
            form_data.append('nameFile', 'Запись_'+ new Date().toLocaleString());
            form_data.append('record', 'record');
            form_data.append('_token', $('input[name=_token]').val());

            $.ajax({
                url: '/autocalls/voice',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function (data) {
                    console.log(data);
                    $('.tableVoices table').find('input[name="voice_id"]').prop('checked', false);
                    $('.tableVoices table').prepend('<tr><td><input style="width: 20px;"  type="radio" name="voice_id" checked value="' + data.id + '"/></td><td>' + data.title + '</td><td>' + data.duration + '</td><td><audio controls src="/static/voices/' + data.file + '"></audio></td></tr>')
                    $('.saveButton').trigger('click');

                    $('.playAudio').unbind( "click" );
                    $('.playAudio').click(function () {
                        playAudio($(this));
                    });
                }
            });
        });
    }

    window.onload = function init() {
        try {
            // webkit shim
            window.AudioContext = window.AudioContext || window.webkitAudioContext;
            navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia;
            window.URL = window.URL || window.webkitURL;

            audio_context = new AudioContext;
            __log('Audio context set up.');
            __log('navigator.getUserMedia ' + (navigator.getUserMedia ? 'available.' : 'not present!'));
        } catch (e) {
            alert('No web audio support in this browser!');
        }


    };

    $('#recordWavstop').click(function(e){
        e.preventDefault();
        if($('#recordWav').hasClass('start')) {
            stopRecording();
            $('.la_sound').slideUp();
            $('#recordWav').removeClass('start');
            $('#recordWavstop').hide();
        }
    });
    
    $('#recordWav').click(function () {
        if($(this).hasClass('start')) {
            $(this).removeClass('start');
            stopRecording();
            $('#recordWavstop').hide();
        } else {
            $(this).addClass('start');
        $('#recordWavstop').show();
            
            if(!recorder) {
                navigator.getUserMedia = ( navigator.getUserMedia ||
                    navigator.webkitGetUserMedia ||
                    navigator.mozGetUserMedia ||
                    navigator.msGetUserMedia);
                navigator.getUserMedia({audio: true}, function (stream) {
                    startUserMedia(stream);
                    startRecording();
                }, function(e) {
                    __log('No live audio input: ' + e);
                });
            } else {
                startRecording();
            }
        }
    });

    $( 'audio.pleer' ).audioPlayer();

});

