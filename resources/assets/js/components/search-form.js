
(function ($, undefined) {

    "use strict";

    var SearchForm = namespace("Tmt.Components").SearchForm = function () {
        this.initialize();
    };

    inherit([Tmt.EventEmitter], SearchForm, {

        "initialize": function () {
            Tmt.EventEmitter.prototype.initialize.call(this);

            // search box
            var artistsSearch = new Bloodhound({
                name: 'artists',
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('artist'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: '/ajax/artistSearch/?q=%QUERY',
                    wildcard: '%QUERY'
                }
            }),
                albumsSearch = new Bloodhound({
                    name: 'albums',
                    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('album'),
                    queryTokenizer: Bloodhound.tokenizers.whitespace,
                    remote: {
                        url: '/ajax/albumSearch/?q=%QUERY',
                        wildcard: '%QUERY'
                    }
                }),
                tracksSearch = new Bloodhound({
                    name: 'tracks',
                    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('track'),
                    queryTokenizer: Bloodhound.tokenizers.whitespace,
                    remote: {
                        url: '/ajax/trackSearch/?q=%QUERY',
                        wildcard: '%QUERY'
                    }
                }),
                searchBox = $('.typeahead');


            // Listens for when Typeahead a selected a value.
            function typeahead_onSelected(e, data, section) {
                e.preventDefault();
                document.location = $('.tt-cursor a:nth-child(1)').attr('href');
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
                            empty: '<h3>Artists</h3><p class="empty-message">Could not find matching artists.</p>',
                            suggestion: function (data) { return ['<p><a href="/artists/' + data.slug + '/">' + data.name + '</a></p>'].join(""); }
                        }
                    },
                    {
                        name: 'albums',
                        display: 'album',
                        source: albumsSearch,
                        cache: true,
                        templates: {
                            header: '<h3>Albums</h3>',
                            empty: '<h3>Albums</h3><p class="empty-message">Could not find matching albums.</p>',
                            suggestion: function (data) { return ['<p><a href="/albums/' + data.slug + '/">' + data.name + '</a> by <a href="/artists/' + data.artist.slug + '/">' + data.artist.name + '</a></p>'].join(""); }
                        }
                    },
                    {
                        name: 'tracks',
                        display: 'track',
                        source: tracksSearch,
                        cache: true,
                        templates: {
                            header: '<h3>Tracks</h3>',
                            empty: '<h3>Tracks</h3><p class="empty-message">Could not find matching tracks.</p>',
                            suggestion: function (data) { return ['<p><a href="/tracks/' + data.slug + '/">' + data.name + '</a> by <a href="/artists/' + data.artist.slug + '/">' + data.artist.name + '</a></p>'].join(""); }
                        }
                    }
                ]
            );

        }
    });


})(jQuery);
