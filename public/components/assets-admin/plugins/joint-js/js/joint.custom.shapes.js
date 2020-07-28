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
            source: {x: 800, y: y},
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
            textAnchor: 'middle',
            refX: 0,
            refY: -15,
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
        name: 'center',
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

joint.dia.Element.define('custom.House', {
    z: 2,
    size: {
        width: 40,
        height: 40
    },
    attrs: {
        label: {
            fontFamily: 'monospace',
            fontSize: 12,
            textAnchor: 'middle',
            refX: 20,
            refY: 15,
            stroke: '#333333'
        },
        labelHouseNo: {
            fontFamily: 'monospace',
            fontSize: 12,
            textAnchor: 'middle',
            refX: 20,
            refY: 45,
            stroke: '#333333'
        },
        body: {
            strokeWidth: 2,
            fill: 'rgba(255,255,255,0)',
            refPoints: '1,0 2,1 2,3 1.25,3 1.25,2 0.75,2 0.75,3 0,3 0,1 0,1',
            stroke: '#0080f0'
        },
        magnet: true
    },
    anchor: {
        name: 'midSide',
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
    }, {
        tagName: 'text',
        selector: 'labelHouseNo'
    }]
}, {
    create: function (x, y, label, houseNo) {
        return new this({
            position: {x: x, y: y},
            attrs: {
                label: {
                    text: label
                },
                labelHouseNo: {
                    text: houseNo
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
                connector.source(new g.Point(source[0], source[1]));
            } else {
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
