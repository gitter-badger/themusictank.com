$(function() {

    // search box
    var artistsSearch = new Bloodhound({
            name : 'artists',
            datumTokenizer: function(d) { return Bloodhound.tokenizers.obj.whitespace(d.artist); },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: '/ajax/artist_search/?q=%QUERY'
        }),
        albumsSearch = new Bloodhound({
            name : 'albums',
            datumTokenizer: function(d) { return Bloodhound.tokenizers.obj.whitespace(d.album); },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: '/ajax/album_search/?q=%QUERY'
        }),
        tracksSearch = new Bloodhound({
            name : 'tracks',
            datumTokenizer: function(d) { return Bloodhound.tokenizers.obj.whitespace(d.track); },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: '/ajax/track_search/?q=%QUERY'
        }),
        searchBox = $('.typeahead');


    // Listens for when Typeahead a selected a value.
    function typeahead_onSelected(e, data, section) {
        e.preventDefault();
        document.location = "/" + [section, 'view', data.slug].join("/");
    }

    artistsSearch.initialize();
    albumsSearch.initialize();
    tracksSearch.initialize();

    searchBox.on("typeahead:selected", typeahead_onSelected);
    searchBox.typeahead(
        { minLength: 3, highlight: true, cache: true },
        [
            {
                name: 'artists',
                displayKey: 'artist',
                source: artistsSearch.ttAdapter(),
                cache: true,
                templates: {
                    header: '<h3>Artists</h3>',
                    suggestion: function(data) { return ["<p>", data.artist, "</p>"].join(""); }
                }
            },
            {
                name: 'albums',
                displayKey: 'album',
                source: albumsSearch.ttAdapter(),
                cache: true,
                templates: {
                    header: '<h3>Albums</h3>',
                    suggestion: function(data) { return ["<p>", data.album, " by ", data.artist, "</p>"].join(""); }
                }
            },
            {
                name: 'tracks',
                displayKey: 'track',
                source: tracksSearch.ttAdapter(),
                cache: true,
                templates: {
                    header: '<h3>Tracks</h3>',
                    suggestion: function(data) { return ["<p>", data.track, " from ", data.album, "</p>"].join(""); }
                }
            }
        ]
    );

});
