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
        box.typeahead(getDefaults(),
            {
                name: 'artists',
                display: 'artist',
                source: getSource('artist', '/ajax/search/artist/?q=%QUERY').ttAdapter(),
                templates: {
                    header: '<h3>Artists</h3>',
                    empty: '<h3>Artists</h3><p class="empty-message">Could not find matching artists.</p>',
                    suggestion: function (data) { return '<p><a href="/artists/' + data.slug + '/">' + data.name + '</a></p>'; }
                }
            },
            {
                name: 'albums',
                display: 'album',
                source: getSource('album', '/ajax/search/album/?q=%QUERY').ttAdapter(),
                templates: {
                    header: '<h3>Albums</h3>',
                    empty: '<h3>Albums</h3><p class="empty-message">Could not find matching albums.</p>',
                    suggestion: function (data) { return '<p><a href="/albums/' + data.slug + '/">' + data.name + '</a> by <a href="/artists/' + data.artist.slug + '/">' + data.artist.name + '</a></p>'; }
                }
            },
            {
                name: 'tracks',
                display: 'track',
                source: getSource('track', '/ajax/search/track/?q=%QUERY').ttAdapter(),
                templates: {
                    header: '<h3>Tracks</h3>',
                    empty: '<h3>Tracks</h3><p class="empty-message">Could not find matching tracks.</p>',
                    suggestion: function (data) { return '<p><a href="/tracks/' + data.slug + '/">' + data.name + '</a> by <a href="/artists/' + data.artist.slug + '/">' + data.artist.name + '</a></p>'; }
                }
            },
            {
                name: 'users',
                display: 'user',
                source: getSource('user', '/ajax/search/user/?q=%QUERY').ttAdapter(),
                templates: {
                    header: '<h3>Tankers</h3>',
                    empty: '<h3>Tankers</h3><p class="empty-message">Could not find matching user.</p>',
                    suggestion: function (data) { return '<p><a href="/tankers/' + data.slug + '/">' + data.name + '</a></p>'; }
                }
            }
        );
        box.on('typeahead:selected', this.resultSelected.bind(this));
    }
};

function getSource(key, endpoint) {
    var source = new Bloodhound({
        name: key,
        datumTokenizer: Bloodhound.tokenizers.whitespace,
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
        cache: true,
        hint: true
    };
}
</script>

<template>
    <form class="ctl ctl-search" action="/search/" method="get">
        <input type="text" name="q" value="" placeholder="Search across everything" />
        <input type="submit" name="search" />
    </form>
</template>


<style lang="scss">
.ctl-search {

    .twitter-typeahead {
        width: 500px;
    }

    .tt-query,
    .tt-hint {
        margin-bottom: 0;
    }

    .tt-input,
    .tt-hint {
        display: block;
        width: 100%;
        height: 40px;
        padding: 8px 12px;
        line-height: 1.428571429;
        color: #999;
        background-color: #ffffff;
        border: 1px solid #666;
        border-radius: 2px;
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
    }
    .tt-menu {
        min-width: 500px;
        max-width: 80%;
        margin-top: 2px;
        padding: 5px 0;
        background-color: #666;
        border: 1px solid #cccccc;
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 2px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
        background-clip: padding-box;
    }
    .tt-suggestion {
        display: block;
        padding: 3px 20px;
    }
    .tt-cursor {
        color: #fff;
        background-color: #428bca;
    }
    .tt-cursor a {
        color: #fff;
    }
    .tt-suggestion p {
        margin: 0;
    }
    .tt-selectable {
        cursor: pointer;
    }
    .tt-selectable:hover {
        background-color: #678aa9;
    }

    h3 {
        margin: 0 0.5em 0.5em;
        font-size: 1.1em;
    }

    p {
        margin: 0 0.5em 0.5em;

        &.empty-message {
            margin: 0 1em 0.5em;
        }
    }

    a {
        display: block;
    }

    input[type=submit] {
        display: none;
    }

}
</style>
