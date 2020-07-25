joint.shapes.standard.Link.define('custom.Bus', {
    z: -1,
    attrs: {
        line: {
            strokeWidth: 2,
            sourceMarker: null,
            targetMarker: null,
            strokeDasharray: '5,5'
        },
        magnet: true
    }
}, {
    defaultLabel: {
        markup: [{
            tagName: 'text',
            selector: 'labelText'
        }],
        position: {
            distance: 0,
            offset: 15,
            args: {
                keepGradient: true,
                ensureLegibility: true
            }
        }
    }
}, {
    create: function (y, label, color) {
        return new this({
            source: {x: 700, y: y},
            target: {x: 50, y: y},
            attrs: {
                line: {
                    stroke: color,
                }
            },
            labels: [{
                attrs: {
                    labelText: {
                        text: label,
                        fontFamily: 'monospace'
                    }
                }
            }]
        });
    },
    setStart: function(y) {
        console.log('test');
    },
});

joint.dia.Element.define('custom.Amplifier', {
    z: 2,
    size: {
        width: 30,
        height: 30
    },
    attrs: {
        label: {
            fontFamily: 'monospace',
            fontSize: 12,
            textVerticalAnchor: 'top',
            textAnchor: 'start',
            refDx: 5,
            stroke: '#333333'
        },
        body: {
            strokeWidth: 2,
            refWidth: '100%',
            refHeight: '100%',
            fill: 'rgba(255,255,255,0)',
            refPoints: '10,0 20,10 10,20',
            stroke: '#000'
        },
        magnet: true
    },
    anchor: {
        name: 'bottom',
        args: {
            rotate: true,
        }
    }
}, {
    markup: [{
        tagName: 'polygon',
        selector: 'body'
    }, {
        tagName: 'text',
        selector: 'label'
    }]
}, {
    create: function (x, y, label) {
        return new this({
            position: {x: x, y: y},
            attrs: {
                label: {
                    text: label
                }
            }
        });
    }
});

joint.shapes.standard.Link.define('custom.LabeledSmoothLine', {
    connector: { name: 'smooth' },
    attr: {
        name: 'smoothed-line'
    }
}, {},
    {
        create: function (source, target) {
            var connector = new this();
            if (Array.isArray(source)) {
                console.log(source)
                connector.source(new g.Point(source[0], source[1]));
            } else {
                console.log(source);
                connector.source(source, {selector: source.isLink() ? 'root' : 'body'});
            }
            if (Array.isArray(target)) {
                connector.target(new g.Point(target[0], target[1]));
            } else {
                connector.target(target, {selector: target.isLink() ? 'root' : 'body'});
            }
            return connector;
        }
    });
