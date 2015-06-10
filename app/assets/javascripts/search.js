$(function() {

    // search box
    var artistsSearch = new Bloodhound({
            name : 'artists',
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('artist'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '/ajax/artist_search/?q=%QUERY',
                wildcard: '%QUERY'
            }
        }),
        albumsSearch = new Bloodhound({
            name : 'albums',
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('album'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '/ajax/album_search/?q=%QUERY',
                wildcard: '%QUERY'
            }
        }),
        tracksSearch = new Bloodhound({
            name : 'tracks',
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('track'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: '/ajax/track_search/?q=%QUERY',
                wildcard: '%QUERY'
            }
        }),
        searchBox = $('.typeahead');


    // Listens for when Typeahead a selected a value.
    function typeahead_onSelected(e, data, section) {
        e.preventDefault();
        document.location = data.slug
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
                display: 'artist',
                source: artistsSearch,
                cache: true,
                templates: {
                    header: '<h3>Artists</h3>',
                    suggestion: function(data) { return ["<p>", data.artist, "</p>"].join(""); }
                }
            },
            {
                name: 'albums',
                display: 'album',
                source: albumsSearch,
                cache: true,
                templates: {
                    header: '<h3>Albums</h3>',
                    suggestion: function(data) { return ["<p>", data.album, " by ", data.artist, "</p>"].join(""); }
                }
            },
            {
                name: 'tracks',
                display: 'track',
                source: tracksSearch,
                cache: true,
                templates: {
                    header: '<h3>Tracks</h3>',
                    suggestion: function(data) { return ["<p>", data.track, " from ", data.album, "</p>"].join(""); }
                }
            }
        ]
    );

});
