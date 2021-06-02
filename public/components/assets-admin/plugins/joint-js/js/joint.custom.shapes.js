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
            source: {x: 900, y: y},
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
    setStart: function (y) {
        console.log('test');
    },
});
var headerHeight = 40;
var buttonSize = 14;

joint.dia.Element.define('custom.Amplifier', {
    size: {
        width: 50,
        height: 50
    },
    collapsed: false,
    attrs: {
        root: {
            magnetSelector: 'buttonIcon'
        },
        shadow: {
            refWidth: '100%',
            refHeight: '100%',
            x: 3,
            y: 3,
            fill: '#b4b4b4',
            opacity: 0.05
        },
        link: {
            xlinkShow: 'new',
            cursor: 'pointer'
        },
        header: {
            refWidth: '100%',
            refHeight: '100%',
            refX: '50%',
            refY: 0,
            strokeWidth: 4,
            fill: 'rgba(255,255,255,0)',
            stroke: '#b4b4b4',
            refPoints: '10,0 20,10 10,20'
        },
        label: {
            textVerticalAnchor: 'bottom',
            textAnchor: 'left',
            refWidth: '100%',
            refHeight: '100%',
            fontSize: 12,
            fontFamily: 'sans-serif',
            fill: '#222222'
        },
        button: {
            refX: '100%',
            refX2: 40,
            refY: 20,
            cursor: 'pointer',
            event: 'element:button:pointerdown',
            title: 'Collapse / Expand',
        },
        buttonBorder: {
            fill: '#4C65DD',
            stroke: '#FFFFFF',
            strokeWidth: 0.5,
            r: 10,
            cx: 7,
            cy: 7
        },
        buttonIcon: {
            fill: 'none',
            stroke: '#FFFFFF',
            strokeWidth: 1
        },
    }
}, {

    markup: [
        {
            tagName: 'a',
            selector: 'link',
            children: [
                {
                    tagName: 'text',
                    selector: 'label'
                }, {
                    tagName: 'polygon',
                    selector: 'header'
                }]
        }, {
            tagName: 'g',
            selector: 'button',
            children: [{
                tagName: 'circle',
                selector: 'buttonBorder'
            }, {
                tagName: 'path',
                selector: 'buttonIcon'
            }]
        },
    ],

    toggle: function (shouldCollapse) {
        var buttonD;
        var collapsed = (shouldCollapse === undefined) ? !this.get('collapsed') : shouldCollapse;
        if (collapsed) {
            buttonD = 'M 2 7 12 7 M 7 2 7 12';
            // this.resize(30, 30);
        } else {
            buttonD = 'M 2 7 12 7';
            // this.fitChildren();
        }


        this.attr(['buttonIcon', 'd'], buttonD);
        this.set('collapsed', collapsed);
    },

    isCollapsed: function () {
        return Boolean(this.get('collapsed'));
    },

    fitChildren: function () {
        var padding = 10;
        this.fitEmbeds({
            padding: {
                top: headerHeight + padding,
                left: padding,
                right: padding,
                bottom: padding
            },
            deep: true
        });
    }
});

joint.dia.Element.define('custom.House', {
    z: 2,
    size: {
        width: 50,
        height: 50
    },
    attrs: {
        label: {
            fontFamily: 'monospace',
            fontSize: 12,
            textAnchor: 'middle',
            refX: 25,
            refY: 20,
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
            strokeWidth: 4,
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
    }],
    toggle: function (shouldCollapse) {
        var collapsed = (shouldCollapse === undefined) ? !this.get('collapsed') : shouldCollapse;
        this.set('collapsed', collapsed);
    },
    isCollapsed: function () {
        return Boolean(this.get('collapsed'));
    },
});

joint.shapes.standard.Link.define('custom.LabeledSmoothLine', {
        connector: {name: 'smooth'},
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

joint.shapes.standard.Link.define('custom.Link', {
    router: {name: 'manhattan'},
    connector: {name: 'rounded'},
    attrs: {
        line: {
            stroke: '#222222',
            strokeWidth: 1,
            targetMarker: {
                'd': 'M 4 -4 0 0 4 4 M 7 -4 3 0 7 4 M 10 -4 6 0 10 4',
                'fill': 'none'
            }
        }
    }
});

joint.dia.Element.define('custom.Bubble', {
    size: {
        width: 50,
        height: 50
    },
    collapsed: false,
    pagination: {
        current_page: 0,
        from:0,
        to: 0,
        total: 0,
        last_page:0
    },
    attrs: {
        root: {
            magnetSelector: 'buttonIcon'
        },
        link: {
            xlinkShow: 'replace',
            cursor: 'pointer'
        },
        header: {
            fill: '#186d20',
            stroke: '#FFFFFF',
            strokeWidth: 0.5,
            r: 30,
            cx: 25,
            cy: 25
        },
        m_count_label: {
            textVerticalAnchor: 'bottom',
            textAnchor: 'left',
            refWidth: '100%',
            refHeight: '100%',
            refX: '30%',
            refY: 20,
            fontSize: 14,
            fontFamily: 'sans-serif',
            fill: '#222222'
        },
        m_online_critical_label: {
            textVerticalAnchor: 'bottom',
            textAnchor: 'left',
            refWidth: '100%',
            refHeight: '100%',
            refX: '30%',
            refY: 35,
            fontSize: 14,
            fontFamily: 'sans-serif',
            fill: '#222222'
        },
        m_avg_upstream_label: {
            textVerticalAnchor: 'bottom',
            textAnchor: 'left',
            refWidth: '100%',
            refHeight: '100%',
            refX: '30%',
            refY: 50,
            fontSize: 14,
            fontFamily: 'sans-serif',
            fill: '#222222'
        },
        button: {
            refX: '100%',
            refX2: 10,
            refY: 20,
            cursor: 'pointer',
            event: 'element:button:pointerdown',
            title: 'Collapse / Expand',
        },
        buttonBorder: {
            fill: '#4C65DD',
            stroke: '#FFFFFF',
            strokeWidth: 0.5,
            r: 10,
            cx: 7,
            cy: 7
        },
        buttonIcon: {
            fill: 'none',
            stroke: '#FFFFFF',
            strokeWidth: 1
        },
    }
}, {

    markup: [
        {
            tagName: 'a',
            selector: 'link',
            children: [
                {
                    tagName: 'circle',
                    selector: 'header'
                }, {
                    tagName: 'text',
                    selector: 'm_count_label'
                }, {
                    tagName: 'text',
                    selector: 'm_online_critical_label'
                }, {
                    tagName: 'text',
                    selector: 'm_avg_upstream_label'
                }]
        }, {
            tagName: 'g',
            selector: 'button',
            children: [{
                tagName: 'circle',
                selector: 'buttonBorder'
            }, {
                tagName: 'path',
                selector: 'buttonIcon'
            }]
        },
    ],

    toggle: function (shouldCollapse) {
        var buttonD;
        var collapsed = (shouldCollapse === undefined) ? !this.get('collapsed') : shouldCollapse;
        if (collapsed) {
            buttonD = 'M 2 7 12 7 M 7 2 7 12';
            // this.resize(30, 30);
        } else {
            buttonD = 'M 2 7 12 7';
            // this.fitChildren();
        }


        this.attr(['buttonIcon', 'd'], buttonD);
        this.set('collapsed', collapsed);
    },

    isCollapsed: function () {
        return Boolean(this.get('collapsed'));
    },

    fitChildren: function () {
        var padding = 10;
        this.fitEmbeds({
            padding: {
                top: headerHeight + padding,
                left: padding,
                right: padding,
                bottom: padding
            },
            deep: true
        });
    }
});

var Path = joint.dia.Element.define('custom.Cloud', {
    size: {width: 50, height: 50},
    collapsed: false,
    attrs: {
        link: {
            xlinkShow: 'new',
            cursor: 'pointer'
        },
        path: {
            refD: 'm 465.83684,466.9869 c -18.8823,0 -37.6499,7.7751 -51.0017,21.127 -8.4122,8.4121 -14.6043,18.9734 -18.0383,30.3583 -8.2539,1.5581 -16.0492,5.5866 -21.9931,11.5304 -7.7473,7.7473 -12.2573,18.6331 -12.2573,29.5895 0,10.9565 4.51,21.8458 12.2573,29.5931 7.7474,7.7473 18.6367,12.2573 29.5931,12.2573 l 187.4365,0 c 6.9935,0 13.9453,-2.8775 18.8904,-7.8224 4.9452,-4.9451 7.8227,-11.8971 7.8227,-18.8906 0,-6.9935 -2.8775,-13.9417 -7.8227,-18.8868 -4.9147,-4.9149 -11.8119,-7.784 -18.7617,-7.8193 -1.3562,-12.6709 -7.0967,-24.867 -16.1148,-33.8851 -10.3847,-10.3847 -24.9798,-16.4314 -39.666,-16.4314 -3.5656,0 -7.1233,0.3656 -10.6192,1.0539 -2.5582,-3.8175 -5.4715,-7.3951 -8.7235,-10.6469 -13.3518,-13.3519 -32.1193,-21.127 -51.0017,-21.127 z', // path offset is 10,10
            fill: 'red',
            stroke: 'black'
        },
        label: {
            textVerticalAnchor: 'bottom',
            textAnchor: 'left',
            fontSize: 12,
            fontFamily: 'sans-serif',
            fill: '#222222'
        },
        button: {
            refX: '50%',
            refX2: 25,
            refY: 7,
            cursor: 'pointer',
            event: 'element:button:pointerdown',
            title: 'Collapse / Expand',
        },
        buttonBorder: {
            fill: '#4C65DD',
            stroke: '#FFFFFF',
            strokeWidth: 0.5,
            r: 10,
            cx: 7,
            cy: 7
        },
        buttonIcon: {
            fill: 'none',
            stroke: '#FFFFFF',
            strokeWidth: 1
        },
    }
}, {
    markup: [{
        tagName: 'a',
        selector: 'link',
        children: [
            {
                tagName: 'path',
                selector: 'path'
            }, {
                tagName: 'text',
                selector: 'label'
            }]
    }, {
        tagName: 'g',
        selector: 'button',
        children: [{
            tagName: 'circle',
            selector: 'buttonBorder'
        }, {
            tagName: 'path',
            selector: 'buttonIcon'
        }]
    },],

    toggle: function (shouldCollapse) {
        var buttonD;
        var collapsed = (shouldCollapse === undefined) ? !this.get('collapsed') : shouldCollapse;
        if (collapsed) {
            buttonD = 'M 2 7 12 7 M 7 2 7 12';
        } else {
            buttonD = 'M 2 7 12 7';
            // this.fitChildren();
        }


        this.attr(['buttonIcon', 'd'], buttonD);
        this.set('collapsed', collapsed);
    },

    isCollapsed: function () {
        return Boolean(this.get('collapsed'));
    },

    fitChildren: function () {
        var padding = 10;
        this.fitEmbeds({
            padding: {
                top: headerHeight + padding,
                left: padding,
                right: padding,
                bottom: padding
            },
            deep: true
        });
    }
});


joint.dia.Element.define('custom.Cluster', {
    size: {width: 50, height: 50},
    collapsed: false,
    attrs: {
        root: {
            magnetSelector: 'buttonIcon'
        },

        link: {
            xlinkShow: 'new',
            cursor: 'pointer'
        },
        body: {
            refWidth: '100%',
            refHeight: '100%',
            strokeWidth: 3,
            stroke: '#000000',
            fill: '#FFFFFF',
            strokeLinejoin: 'round'
        },
        cluster_dot1: {
            fill: '#000000',
            stroke: 'none',
            strokeWidth: 0.5,
            r: 4,
            refX: '20%',
            refY: 10,
        },

        cluster_dot2: {
            fill: '#000000',
            stroke: 'none',
            strokeWidth: 0.5,
            r: 4,
            refX: '20%',
            refY: 20,
        },
        cluster_dot3: {
            fill: '#000000',
            stroke: 'none',
            strokeWidth: 0.5,
            r: 4,
            refX: '20%',
            refY: 30,
        },
        cluster_line1: {
            refX: '40%',
            refY: 10,
            x1: 0, y1: 0, x2: 20, y2: 0, stroke: 'black', strokeWidth: 3
        },
        cluster_line2: {
            refX: '40%',
            refY: 20,
            x1: 0, y1: 0, x2: 20, y2: 0, stroke: 'black', strokeWidth: 3
        },
        cluster_line3: {
            refX: '40%',
            refY: 30,
            x1: 0, y1: 0, x2: 20, y2: 0, stroke: 'black', strokeWidth: 3
        },
        label: {
            textVerticalAnchor: 'bottom',
            textAnchor: 'left',
            fontSize: 12,
            fontFamily: 'sans-serif',
            fill: '#222222'
        },
        button: {
            refX: '50%',
            refX2: 25,
            refY: 7,
            cursor: 'pointer',
            event: 'element:button:pointerdown',
            title: 'Collapse / Expand',
        },
        buttonBorder: {
            fill: '#4C65DD',
            stroke: '#FFFFFF',
            strokeWidth: 0.5,
            r: 10,
            cx: 7,
            cy: 7
        },
        buttonIcon: {
            fill: 'none',
            stroke: '#FFFFFF',
            strokeWidth: 1
        },
    }
}, {

    markup: [
        {
            tagName: 'a',
            selector: 'link',
            children: [
                {
                    tagName: 'rect',
                    selector: 'body'
                }, {
                    tagName: 'circle',
                    selector: 'cluster_dot1'

                }, {
                    tagName: 'circle',
                    selector: 'cluster_dot2'

                }, {
                    tagName: 'circle',
                    selector: 'cluster_dot3'

                }, {
                    tagName: 'line',
                    selector: 'cluster_line1'

                }, {
                    tagName: 'line',
                    selector: 'cluster_line2'

                }, {
                    tagName: 'line',
                    selector: 'cluster_line3'

                }, {
                    tagName: 'text',
                    selector: 'label'
                }]
        }, {
            tagName: 'g',
            selector: 'button',
            children: [{
                tagName: 'circle',
                selector: 'buttonBorder'
            }, {
                tagName: 'path',
                selector: 'buttonIcon'
            }]
        },
    ],

    toggle: function (shouldCollapse) {
        var buttonD;
        var collapsed = (shouldCollapse === undefined) ? !this.get('collapsed') : shouldCollapse;
        if (collapsed) {
            buttonD = 'M 2 7 12 7 M 7 2 7 12';
        } else {
            buttonD = 'M 2 7 12 7';
            // this.fitChildren();
        }


        this.attr(['buttonIcon', 'd'], buttonD);
        this.set('collapsed', collapsed);
    },

    isCollapsed: function () {
        return Boolean(this.get('collapsed'));
    },

    fitChildren: function () {
        var padding = 10;
        this.fitEmbeds({
            padding: {
                top: headerHeight + padding,
                left: padding,
                right: padding,
                bottom: padding
            },
            deep: true
        });
    }
});

joint.dia.Element.define('custom.Block', {
    size: {width: 100, height: 30},
    collapsed: false,
    attrs: {
        root: {
            magnetSelector: 'buttonIcon'
        },
        link: {
            xlinkShow: 'new',
            cursor: 'pointer'
        },
        header: {
            refHeight: '100%',
            refWidth: '100%',
            fill: '#ffe900',
            stroke: '#8f8c8c',
            strokeWidth: 2,
        },
        label: {
            textVerticalAnchor: 'bottom',
            textAnchor: 'left',
            refWidth: '100%',
            refHeight: '100%',
            fontSize: 12,
            fontFamily: 'sans-serif',
            fill: '#222222'
        },
        button: {
            refX: '100%',
            refX2: 5,
            refY: 7,
            cursor: 'pointer',
            event: 'element:button:pointerdown',
            title: 'Collapse / Expand',
        },
        buttonBorder: {
            fill: '#4C65DD',
            stroke: '#FFFFFF',
            strokeWidth: 0.5,
            r: 10,
            cx: 7,
            cy: 7
        },
        buttonIcon: {
            fill: 'none',
            stroke: '#FFFFFF',
            strokeWidth: 1
        },
    }
}, {

    markup: [
        {
            tagName: 'a',
            selector: 'link',
            children: [
                {
                    tagName: 'rect',
                    selector: 'header'
                }, {
                    tagName: 'text',
                    selector: 'label'
                }]
        }, {
            tagName: 'g',
            selector: 'button',
            children: [{
                tagName: 'circle',
                selector: 'buttonBorder'
            }, {
                tagName: 'path',
                selector: 'buttonIcon'
            }]
        },
    ],

    toggle: function (shouldCollapse) {
        var buttonD;
        var collapsed = (shouldCollapse === undefined) ? !this.get('collapsed') : shouldCollapse;
        if (collapsed) {
            buttonD = 'M 2 7 12 7 M 7 2 7 12';
        } else {
            buttonD = 'M 2 7 12 7';
            // this.fitChildren();
        }


        this.attr(['buttonIcon', 'd'], buttonD);
        this.set('collapsed', collapsed);
    },

    isCollapsed: function () {
        return Boolean(this.get('collapsed'));
    },

    fitChildren: function () {
        var padding = 10;
        this.fitEmbeds({
            padding: {
                top: headerHeight + padding,
                left: padding,
                right: padding,
                bottom: padding
            },
            deep: true
        });
    }
});
