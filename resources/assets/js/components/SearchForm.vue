<script>
import ComponentBase from './mixins/base.js'
import Bloodhound from 'bloodhound-js'

export default {
    mixins: [ComponentBase],

    methods: {
        resultSelected(event) {
            event.preventDefault();
            document.location = this.getSelectResultElement().attr('href');
        },

        getSelectResultElement() {
            return this.getElement().find('.tt-cursor a:nth-child(1)');
        },

        getSearchBox() {
            return this.getElement().find('input[type=text]');
        }
    },

    mounted() {
        var box = this.getSearchBox();
        box.typeahead(getDefaults(), getSources());
        box.on('typeahead:selected', this.resultSelected.bind(this));
    }
};

function getSource(key, endpoint) {
    var source = new Bloodhound({
        name: key,
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace(key),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: endpoint,
            wildcard: '%QUERY'
        }
    });
    source.initialize();
    return source;
}

function getDefaults() {
    return {
        minLength: 3,
        highlight: true,
        cache: true
    };
};

function getSources() {
    return [
        {
            name: 'artists',
            display: 'artist',
            source: getSource('artist', '/ajax/artistSearch/?q=%QUERY'),
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
            source: getSource('album', '/ajax/albumSearch/?q=%QUERY'),
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
            source: getSource('track', '/ajax/trackSearch/?q=%QUERY'),
            cache: true,
            templates: {
                header: '<h3>Tracks</h3>',
                empty: '<h3>Tracks</h3><p class="empty-message">Could not find matching tracks.</p>',
                suggestion: function (data) { return ['<p><a href="/tracks/' + data.slug + '/">' + data.name + '</a> by <a href="/artists/' + data.artist.slug + '/">' + data.artist.name + '</a></p>'].join(""); }
            }
        }
    ];
};
</script>

<template>
    <form action="/search/" method="get">
        <input type="text" name="q" value="" placeholder="Search across everything" />
        <input type="submit" name="search" />
    </form>
</template>
