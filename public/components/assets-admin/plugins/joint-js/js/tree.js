// FUNCTIONAL SUBROUTINES
function identity(x) {
    return x;
}

function cons(x, coll_2) {
    return [x].concat(coll_2);
}

function is_empty(coll) {
    return coll == null || coll.length == 0;
}

function zip(coll_1, coll_2) {
    // Convert two lists into a list of pairs.
    // The result will be as long as the shorter list.
    if (is_empty(coll_1) || is_empty(coll_2)) {
        return [];
    }
    return cons([coll_1[0], coll_2[0]],
        zip(coll_1.slice(1),
            coll_2.slice(1)));
}

function unzip(pairs) {
    // Convert a list of pairs into two lists of equal length.
    // Returns an array containing both lists.
    if (is_empty(pairs)) {
        return [[], []];
    }
    var two_lists = unzip(pairs.slice(1));
    return [cons(pairs[0][0], two_lists[0]),
        cons(pairs[0][1], two_lists[1])];
}


function reverse(coll) {
    var tmp = coll.slice(0);
    return is_empty(coll) ? [] : cons(tmp.pop(), reverse(tmp));
}

// TREE DATA TYPES

function Tree(offset, children, shape) {
    this.parent;
    this.children = children || [];
    this.shape = shape;


    this.child_offset = offset || 0; // relative distance from parent
    this.extents = []; // absolute left/right bounds for each level of this tree

    this.add = function (subtree) {
        // Append subtree and return it.
        subtree.parent = this;
        this.children.push(subtree);
        return subtree;
    };

    this.up = function () {
        // Return this trees's parent (or itself, if it's root).
        return this.parent || this;
    }
    this.push = function () {
        // Create a subtree with the given label, append it, and return it.
        var t = new Tree();
        return this.add(t);
    }

}


function tree_move(tree, displacement) {
    var ret = $.extend({}, tree); // shallow copy
    ret.child_offset += displacement;
    return ret;
}


function extents_move(extents, displacement) {
    return extents.map(function (ext) {
        return [ext[0] + displacement,
            ext[1] + displacement];
    });
}

function extents_merge(exts_1, exts_2) {
    // Create a new list of extents from the left bounds of the first
    // list and the right bounds of the second list. If the lists are
    // of different length, the remainder of the longer list will be
    // appended verbatim.

    if (exts_1.length == 0 || exts_2.length == 0) {
        return exts_1.concat(exts_2);
    } else {
        return cons([exts_1[0][0], exts_2[0][1]],
            extents_merge(
                exts_1.slice(1),
                exts_2.slice(1)));
    }
}

function extents_merge_list(extents_list) {
    return extents_list.reduce(extents_merge, []);
}


function extents_fit(exts_1, exts_2) {
    // Return the minimum horizontal displacement between the two
    // extents necessary to ensure that they do not overlap when
    // exts_1 is placed to the left of exts_2.

    if (exts_1.length > 0 && exts_2.length > 0) {
        return Math.max(
            extents_fit(exts_1.slice(1),
                exts_2.slice(1)),
            exts_1[0][1] - exts_2[0][0] + 1 // right edges of exts_1, left edges of exts_2
        );
    } else {
        return 0;
    }
}


function extents_fit_list_left(extents_list) {
    return (function fn(accumulated,
                        elist) {
        if (elist.length == 0) {
            return [];
        } else {
            var exts = elist[0];
            var x = extents_fit(accumulated, exts);
            return cons(x,
                fn(
                    extents_merge(accumulated,
                        extents_move(exts, x)),
                    elist.slice(1)
                ));
        }
    })([], extents_list);
}


function extents_fit_list_right(extents_list) {
    var flip_elist = function (elist) {
        return elist.map(function (x) {
            return [-x[1], -x[0]]
        });
    }
    var negate = function (x) {
        return -x;
    };

    return reverse(extents_fit_list_left(reverse(extents_list.map(flip_elist))).map(negate));
}


function extents_fit_list_symmetric(extents_list) {
    var A = extents_fit_list_left(extents_list);
    var B = extents_fit_list_right(extents_list);

    return zip(A, B).map(function (x) {
        return (x[0] + x[1]) / 2
    });
}

var Amplifier = joint.shapes.custom.Amplifier;
var Bubble = joint.shapes.custom.Bubble;
var House = joint.shapes.custom.House;
var Block = joint.shapes.custom.Block;
var Cluster = joint.shapes.custom.Cluster;
var Link = joint.shapes.custom.Link;

function arrange(tree) {
    return (function fn(tree) {
        var subdata = unzip(tree.children.map(fn));
        var subtrees = subdata[0];
        var subextent_list = subdata[1];

        var positions = extents_fit_list_symmetric(subdata[1]);

        var subtrees_positioned = zip(subtrees, positions).map(function (x) {
            return tree_move(x[0], x[1]);
        });
        var subextent_list_positioned = zip(subextent_list, positions).map(function (x) {
            return extents_move(x[0], x[1]);
        });

        var result_extents = cons([0, 0], extents_merge_list(subextent_list_positioned));
        var shape;
        if (tree.netelementtype.name === 'Cluster') {
            shape = new Cluster({
                id: tree.id,
                attrs: {
                    link: {
                        xlinkHref: 'https://jointjs.com'
                    },
                    label: {text: tree.name}
                }
            });

        } else if (tree.netelementtype.name === 'Passive Component') {
            shape = new Amplifier({
                id: tree.id, attrs: {label: {text: tree.name}}
            });
        } else if (tree.netelementtype.name === 'modem') {
            shape = new House({
                id: tree.id,
                attrs: {
                    label: {text: tree.id}, body: {stroke: tree.us_snr > 0 ? '#00FF00' : '#FF0000'}
                }
            });
        } else if (tree.netelementtype.name === 'bubble') {
            shape = new Bubble({
                id: tree.id,
                collapsed: false,
                attrs: {
                    link: {
                        xlinkHref: tree.url,
                        title: 'Total number of modems: ' + tree.m_count + ' \n' +
                            'Number of Online modems / Number of Critical modems: '
                            + tree.m_online_count + '/' + tree.m_critical_count + '\n' +
                            'Avg. Upstream Power: ' + _.round(tree.m_upstream_avg[0].us_pwr_avg, 1)
                    },
                    m_count_label: {text: tree.m_count},
                    m_online_critical_label: {text: tree.m_online_count + '/' + tree.m_critical_count},
                    m_avg_upstream_label: {text: _.round(tree.m_upstream_avg[0].us_pwr_avg, 1)},
                },
                parent_id: tree.parent_id,
                pagination: tree.pagination,
            });
            shape.toggle();
        } else if (tree.netelementtype.name === 'load_more') {
            shape = new joint.shapes.standard.Circle();
            shape.size(50, 50);
            shape.attr('root/title', 'Load more modems...');
            shape.attr('label/text', '+' + (tree.total-tree.to));
            shape.attr('body/fill', 'lightblue');
            shape.attr(['root','cursor'], 'pointer');
            shape.set('node_id', tree.node_id);
            shape.set('parent_id', tree.parent_id);
            shape.set('next_page', tree.next_page)
        }else {
            shape = new Amplifier({
                id: tree.id,
                attrs: {
                    link: {
                        xlinkHref: '/admin/NetElement/'+tree.id+'/controlling/0/0'
                    },
                    label: {
                        text: tree.name
                    },
                    header: {stroke: 'green'}
                }
            });
            shape.toggle(false);
        }

        var result_tree = new Tree(0, [], shape);
        result_tree.extents = result_extents;
        for (var i in subtrees_positioned) {
            result_tree.add(subtrees_positioned[i]);
        }

        return [result_tree, result_extents];
    })(tree)[0];
}

function draw_tree(graph, tree, xoff, yoff, xscale, yscale) {
    var x0 = xoff + tree.child_offset * xscale;
    var y0 = yoff;


    if (tree.children.length > 0) {
        tree.shape.position(x0, y0);
    }
    tree.shape.addTo(graph);
    if (tree.parent) {
        tree.parent.shape.embed(tree.shape);
        tree.shape.position(x0, y0 - yscale / 2);
        var link = new Link({
            z: 4,
            source: {id: tree.parent.shape.id},
            target: {id: tree.shape.id}
        });
        link.addTo(graph);
        link.reparent();
        tree.parent.shape.toggle(false);
    }

    for (const i in tree.children) {
        draw_tree(graph, tree.children[i], x0, y0 + yscale,
            xscale, yscale);
    }
}

