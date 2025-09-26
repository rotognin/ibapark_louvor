const stage = new Konva.Stage({
    container: 'container',
    width: 400,
    height: 400
});

const layer = new Konva.Layer();
stage.add(layer);

const rect = new Konva.Rect({
    x: 100,
    y: 100,
    width: 50,
    height: 50,
    fill: 'red'
});
layer.add(rect);

rect.on('click', () => {
    rect.to({
        x: 200,
        duration: 0.1
    });
});

layer.draw();