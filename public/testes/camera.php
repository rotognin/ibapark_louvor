<?php

require_once('header.php');

use Funcoes\Layout\Layout as L;
use Funcoes\Layout\Form;
use Funcoes\Layout\FormControls as FC;

$pageHeader = L::pageTitle(
    '<h1 class="m-0 text-dark">Camera</h1>',
    L::backButton()
);

$form = new Form();
$form->setTitle('Leitura da Câmera');
$form->setForm('id="form-camera" action="?posicao=salvar" method="post"');

$foto = <<<HTML
    <div class="camera">
        <video id="video" style="display:none">...</video>
        <button id="start-button" style="display:none">Tirar foto</button>
    </div>
    <canvas id="canvas" style="display:none"> </canvas>
        <div class="output">
            <img id="photo" src="" alt="The screen capture will appear in this box." />
        </div>
    <br>
    <h3 style="display:none" id="msg_aguarde">Aguarde...</h3>
HTML;

$aForm = array(
    ['<br>'],
    ['<div class="row"><button id="camera_etq" onclick="lerEtiqueta(event)">Ler Etiqueta</button></div>'],
    ['<br>'],
    ['<div class="row"><div id="readerEtq" width="300px"></div></div>'],
    ['<br>'],
    ['<div class="row"><button id="camera_cilindro" onclick="fotoCilindro(event)">Foto Cilindro</button></div>'],
    ['<br>'],
    ['<div class="row">' . $foto . '</div>']
);

$form->setFields($aForm);
$form->setActions(L::submit(_('Salvar')));

$response->page(
    <<<HTML
    $pageHeader
    <div class="content pb-1">
        <div class="container-fluid pb-1">
            {$form->html()}
        </div>
    </div>
    <script>
        const width = 320; // We will scale the photo width to this
        let height = 0; // This will be computed based on the input stream

        let streaming = false;

        var primeiro = true;
        var video = document.getElementById("video");
        const canvas = document.getElementById("canvas");
        const photo = document.getElementById("photo");
        const startButton = document.getElementById("start-button");

        const constraints = {
            advanced: [{
                facingMode: "environment"
            }]
        };

        video.addEventListener(
            "canplay",
            (ev) => {
                if (!streaming) {
                height = video.videoHeight / (video.videoWidth / width);

                video.setAttribute("width", width);
                video.setAttribute("height", height);
                canvas.setAttribute("width", width);
                canvas.setAttribute("height", height);
                streaming = true;
                }
            },
            false,
        );

        startButton.addEventListener(
            "click",
            (ev) => {
                takePicture();
                ev.preventDefault();
            },
            false,
        );

        function clearPhoto() {
            const context = canvas.getContext("2d");
            context.fillStyle = "#AAA";
            context.fillRect(0, 0, canvas.width, canvas.height);

            const data = canvas.toDataURL("image/png");
            photo.setAttribute("src", data);
        }

        clearPhoto();

        function takePicture() {
            const context = canvas.getContext("2d");
            if (width && height) {
                canvas.width = width;
                canvas.height = height;
                context.drawImage(video, 0, 0, width, height);

                const data = canvas.toDataURL("image/png");
                photo.setAttribute("src", data);

                $("#start-button").hide();
                video.pause();
                video.currentTime = 0;
                $("#video").hide();

                //$("#msg_aguarde").show();

                // Enviar a imagem para ser lida
                //$.ajax()


            } else {
                clearPhoto();
            }
        }

        function fotoCilindro(e){
            e.preventDefault();

            if (primeiro){
                navigator.mediaDevices
                    .getUserMedia({ video: constraints, audio: false })
                    .then((stream) => {
                        video.srcObject = stream;
                        //video.play();
                    })
                    .catch((err) => {
                        console.error('An error occurred: ' + err);
                    });

                primeiro = false;
            }

            video.play();
            $("#video").show();
            $("#start-button").show();
        }

        var html5QrcodeScannerEtq = new Html5QrcodeScanner(
            'readerEtq',
            { 
                fps: 10, 
                qrbox: {
                    width: 250, 
                    height: 250
                }, 
                
                videoConstraints: {
                    facingMode: { 
                        exact: "environment" 
                    },
                },
                
            },
            false
        );

        function onScanSuccessEtq(decodedText, decodedResult)
        {
            \$('#part_number').val(decodedText);
            console.log(decodedText);
            html5QrcodeScannerEtq.stop().then((ignore) => {}).catch((err) => {});
            //console.log(decodedText);
        }

        function onScanFailureEtq(error)
        {
            //alert(error);
        }

        function lerEtiqueta(e){
            e.preventDefault();
            html5QrcodeScannerEtq.render(onScanSuccessEtq, onScanFailureEtq);            
        }
    </script>
    HTML,
    ["title" => 'Câmera']
);
