/* 6 okt */
var Video = [];
Video.url = '/razz/record-save';
Video.tmp = '/files/tmp';
Video.timer = null;
Video.maxTime = 60*2*1000;
Video.frameWidth =  366; // INSERTED
Video.frameHeight = 276; // INSERTED
Video.snapshot; // INSERTED
Video.aspectRatio = 1.33

Video.dfd = new jQuery.Deferred(); // INSERTED

$(document).ready(function () {

    // $("input[type=submit]").hide();

    var csrfToken = $('meta[name="csrf-token"]').attr("content"); // INSERTED START

    // Взять конфигурационные параметры
    $.ajax({
        url: "/razz/get-config-ajax",
        data: {
            keys: 'video_capturing_time|capturing_frame_width|capturing_frame_height',
            _csrf : csrfToken
        },
        dataType: "json",
        type: "post",
        cache: false
    }).done(
        function(data){
            Video.maxTime = data.video_capturing_time;
            Video.frameWidth = data.capturing_frame_width;
            Video.frameHeight = data.capturing_frame_height;
        }).fail(
        function(jqXHR, textStatus){
            console.log(jqXHR)
            console.log(textStatus)
        }
    ); // INSERTED END

    Video.init();

    /*
    Video.buttonRecord.click(function(){

        $(Video.video).show(); // INSERTED
        $(Video.canvas).hide();

        Video.clean();

        Video.startRecording();

        return false;
    });
    */

    Video.buttonSave.click(function(){
        $.when(Video.saveVideo()).done(Video.afterStop()); // INSERTED
        //Video.saveVideo(); // COMMENTED
        return false;
    });

    Video.buttonReplay.click(function(){
        //$.when(Video.replayVideo()).done(Video.afterReplay()); // INSERTED
        Video.replayVideo();
        return false;
    });

    // TODO менять эти параметры onResize
    Video.frameWidth =  $(Video.video).width()
    Video.frameHeight =  $(Video.video).height()

    $(Video.canvas).css("width",Video.frameWidth + "px")
    $(Video.canvas).css("height",Video.frameHeight + "px")


});



Video.init = function(){

    Video.isFirefox = !!navigator.mozGetUserMedia;
    Video.canvas = document.getElementById('canvas');
    if(Video.canvas)Video.context = Video.canvas.getContext('2d');
    Video.video = document.getElementById('video');
    Video.audio = document.getElementById('audio');
    Video.videoOut = document.getElementById('video');
    Video.fileName = '';
    Video.snapshot = ''; // INSERTED
    Video.buttonRecord = $('#recordVideo');
    Video.buttonSave = $('#saveVideo');
    Video.buttonReplay = $('#replayVideo');
    Video.allow = document.getElementById('allow');
    Video.progress = $('#progress');

    Video.buttonSave.hide();
    Video.buttonReplay.hide();
    Video.progress.hide();

    $("#video").on("ended",function(){ // INSERTED
        Video.afterReplay();
    });
}


    Video.replayVideo = function () {
        if (!Video.isFirefox) {
            recordVideo.getDataURL(function (dataURL) {
                Video.videoOut.src = dataURL;
            });
            recordAudio.getDataURL(function (dataURL) {
                Video.audio.src = dataURL;
            });
        } else {
            recordAudio.getDataURL(function (dataURL) {
                Video.videoOut.src = dataURL;
            });
        }
        $(Video.video).show(); // INSERTED
        $(Video.canvas).hide(); // INSERTED

    }

Video.afterReplay = function(){ // INSERTED
// After 'Replay' button was clicked and video ended
    setTimeout(function(){
        $(Video.video).hide();
        $(Video.canvas).show();

    },300);


}

Video.afterStop = function(){ // INSERTED
// After just stopping capturing

    $(Video.video).hide();
    $(Video.canvas).show();

}

Video.postBlob = function(blob, fileType, fileName){

    var formData = new FormData();
    var png = Video.canvas.toDataURL();

    formData.append(fileType + '-filename', fileName);
    formData.append(fileType + '-blob', blob);
    formData.append(fileType + '-png', png);

    $.ajax({
        url: Video.url, // record-save
        data: formData,
        processData: false,
        contentType: false,
        type: 'POST',
        dataType: "json",
        success: function (data) {
            // console.log(data);
            if(data.type == "video") {
                Video.fileName = data.file;
                Video.merge(data.base);
            }

            //if(data.snapshot) // INSERTED
            //  Video.snapshot = data.snapshot;

            //  Video.videoOut.src = Video.tmp+'/'+data+'?t='+Math.round(Math.random() * 99999999);
            Video.buttonRecord.show();
            Video.buttonSave.hide();
            Video.buttonReplay.show();
            Video.progress.hide();


            // показать снапшот // INSERTED
            //Video.getSnapshot();






        },
        xhr: function()
        {
            var xhr = new window.XMLHttpRequest();
            //Upload progress
            xhr.upload.addEventListener("progress", function(evt){
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    Video.progress.find('div').width(Math.round(percentComplete * 100) + "%");
                    // console.log(Math.round(percentComplete * 100) + "%");
                }
            }, false);
            //Download progress
            xhr.addEventListener("progress", function(evt){
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    //Do something with download progress
                    //console.log('AAA '+Math.round(percentComplete * 100) + "%");
                }
            }, false);
            return xhr;
        }
    });



}

/**
 * Остановить запись видео с веб-камеры
 */
Video.saveVideo = function(){
    Video.video.src = '';
    Video.progress.show();
    Video.fileName = Math.round(Math.random() * 99999999) + 99999999;
    if (!Video.isFirefox) {
        recordAudio.stopRecording(function () {
            Video.postBlob(recordAudio.getBlob(), 'audio', Video.fileName + '.wav');
        });
    } else {
        recordAudio.stopRecording(function (url) {
            //Video.video.src = '';

            var video_blob = recordAudio.getBlob();
            var arrayBuffer;
            var fileReader = new FileReader();
            fileReader.onload = function (ex) {
                arrayBuffer = this.result;
                video_blob = new Blob([arrayBuffer], {type: "video/webm"});
                Video.postBlob(video_blob, 'video', Video.fileName + '.webm');
            };
            fileReader.readAsArrayBuffer(video_blob);

            //Video.PostBlob(recordAudio.getBlob(), 'video', fileName + '.webm');
        });
    }

    if (!Video.isFirefox) {

        recordVideo.stopRecording(function() {
            Video.postBlob(recordVideo.getBlob(), 'video', Video.fileName + '.webm');
        });


    }

}


Video.clean = function(){// INSERTED

    $.ajax({
        url: "/razz/clean",
        dataType: "json",
        type: "post",
        cache: false
    }).done(
        function(data){

        }).fail(
        function(jqXHR, textStatus){
            console.log("Error in Video::clean()")
            console.log(jqXHR)
            console.log(textStatus)
        }
    );

};


Video.startRecording = function(){


    // navigator.getUserMedia  и   window.URL.createObjectURL (смутные времена браузерных противоречий 2012)
    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
    if(window.URL)
        window.URL.createObjectURL = window.URL.createObjectURL || window.URL.webkitCreateObjectURL || window.URL.mozCreateObjectURL || window.URL.msCreateObjectURL;
    // запрашиваем разрешение на доступ к поточному видео камеры


    if(!!navigator.getUserMedia === false)
        console.log("getUserMedia not supported")

    if (false) {

        var constraints = { audio: true, video: true };
        /*
         navigator.mediaDevices.getUserMedia(constraints).
         then(function(mediaStream) {

         }).
         catch(function(err) {
         console.log(err.name + ": " + err.message);
         alert(err.name + ": " + err.message);
         });
         */

        //--
        navigator.mediaDevices = navigator.mediaDevices || ((navigator.mozGetUserMedia || navigator.webkitGetUserMedia) ? {
                getUserMedia: function(c) {
                    return new Promise(function(y, n) {
                        (navigator.mozGetUserMedia ||
                        navigator.webkitGetUserMedia).call(navigator, c, y, n);
                    });
                }
            } : null);

        if (!navigator.mediaDevices) {
            console.log("getUserMedia() not supported.");
            //alert("getUserMedia() not supported.");
            return;
        }




        navigator.mediaDevices.getUserMedia(constraints)
            .then(function(stream) {
                var video = document.querySelector('video');
                video.src = window.URL.createObjectURL(stream);
                video.onloadedmetadata = function(e) {
                    video.play();
                };
            })
            .catch(function(err) {
                console.log(err.name + ": " + err.message);
                alert(err.name + ": " + err.message);
            });

    } else {

        navigator.getUserMedia({video: true,audio:true}, function (stream) {
            Video.buttonRecord.hide();
            Video.buttonSave.show();
            Video.buttonReplay.hide();

            clearTimeout(Video.timer);
            Video.timer = setTimeout( 'Video.saveVideo();', Video.maxTime);

            if (window.URL) {
                Video.video.src = window.URL.createObjectURL(stream);
            } else {
                Video.video.src = stream;
            }

            // получаем url поточного видео
            videoStreamUrl = window.URL.createObjectURL(stream);
            // устанавливаем как источник для video
            Video.video.src = videoStreamUrl;

            // var legalBufferValues = [256, 512, 1024, 2048, 4096, 8192, 16384];
            // sample-rates in at least the range 22050 to 96000.
            recordAudio = RecordRTC(stream, {
                //  bufferSize: 256,
                //sampleRate: 45000,
                onAudioProcessStarted: function () {
                    if (!Video.isFirefox) {
                        recordVideo.startRecording();
                    }
                }
            });

            if (Video.isFirefox) {
                recordAudio.startRecording();
            }
            if (!Video.isFirefox) {

                recordVideo = RecordRTC(stream, {
                    type: 'video'
                });
                recordAudio.startRecording();
            }

            Video.takeSnapshot();


        }, function () {
            console.log('Error');
        });


    }

    Video.success = function(){ // INSERTED
        $("#razz-filename").val(Video.fileName);

        // FIXME - с первого раза не работает
        Video.getSnapshot();

        setTimeout(function(){

            // TODO
            //$("input[type=submit].anyone-elem").show()
            //$("input[type=submit].someone-elem").show()

            // показать снапшот
            Video.getSnapshot();

        },1000);
    };

    Video.takeSnapshot = function () {

        setTimeout(function () {
            Video.context.drawImage(Video.video, 0, 0, Video.frameWidth, Video.frameHeight);
        }, 1000);

    };

    Video.getSnapshot = function(){ // INSERTED

        //var img2 = document.createElement('img');
        //img2.src = '/frontend/web/files/tmp/' + Video.snapshot;
        //var canvas = document.getElementById("canvas");
        //
        //
        //$("#canvas").show()
        //
        //var ctx = canvas.getContext("2d");
        //
        //ctx.drawImage(img2,1,6);
        ////Video.context.drawImage(img2, 0, 0, Video.frameWidth, Video.frameHeight);
        //
        //$("#canvas").css("width",Video.frameWidth * 0.8 + "px")
        //$("#canvas").css("height",Video.frameHeight * 0.8 + "px")
        //
        //
        //$(Video.video).hide();

        var frameWidth = Video.frameWidth/1.18
        var frameHeight = Video.frameHeight/1.18

        var img3 = document.createElement('img');

        img3.src = '/frontend/web/files/tmp/' + Video.snapshot;

        $(Video.canvas).show();
        var ctx = document.getElementById("canvas").getContext("2d");

        ctx.drawImage(img3,0,0,Video.frameHeight/1.1438,240,0,0,Video.frameWidth,Video.frameHeight);

        $(Video.canvas).width(frameWidth)
        $(Video.canvas).height(frameWidth/ Video.aspectRatio)

        $(Video.video).hide();
    };

    Video.merge = function(filename){

        $.ajax({
            url: "/razz/merge",
            data: {
                filename: filename,
            },
            dataType: "json",
            type: "post",
            cache: false
        }).done(
            function(data){
                res = data.result;

                if(data.snapshot) // INSERTED
                    Video.snapshot = data.snapshot;



                Video.success();

            }).fail(
            function(jqXHR, textStatus){
                console.log("Error in Video::merge()")
                console.log(jqXHR)
                console.log(textStatus)
            }
        );

    };



}
