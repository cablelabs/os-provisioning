'use strict';

const http = require("http");

function syncProvision(args, callback) {

    const options = {
        port: 7557,
        path: "/presets/sync-" + args,
        method: "DELETE",
    }

    var req = http.request(options, (res) => {
        var chunks = [];
        //console.log(`STATUS: ${res.statusCode}`);
        //console.log(`HEADERS: ${JSON.stringify(res.headers)}`);

        if (res.statusCode !== 200) {
            return callback(null, null);
        }

        res.on("data", function (chunk) {
            chunks.push(chunk);
            //console.log(`BODY: ${chunk}`);
        });

        res.on("end", function (chunk) {
            var result = Buffer.concat(chunks).toString();
            //console.log('No more data in response.');
            callback(null, result);
        });
    });

    req.on('error', (e) => {
        //console.error(`problem with request: ${e.message}`);
        callback(e.message);
    });

    req.end();
}

exports.ret = syncProvision;
