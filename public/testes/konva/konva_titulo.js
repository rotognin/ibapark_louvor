const titulo = "Caça Palavras";

const tituloTexto = new Konva.Text({
    x: stage.width() / 2,
    y: 15,
    text: titulo,
    fontSize: 40,
    fontFamily: 'Calibri',
    fill: 'green'
});

tituloTexto.offsetX(tituloTexto.width() / 2);

layer.add(tituloTexto);