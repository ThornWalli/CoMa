coma.define(['jquery-master'], function (jq) {
    var jq = jq || $;
    console.log('jQuery Version:', jq().jquery);
    return jq.noConflict( true );
});