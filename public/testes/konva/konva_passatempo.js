const stage = new Konva.Stage({
    container: 'container',
    width: 1000,
    height: 700
});

const layer = new Konva.Layer()
stage.add(layer);

const rect = new Konva.Rect({
    x: 1,
    y: 1,
    width: 998,
    height: 698,
    stroke: 'black'
});

layer.add(rect);
layer.draw();