module MetatagsHelper

    def self.to_meta_tags meta, request
        tags = Array.new

        domain = ""
        domain = get_domain request unless request.nil?

        url = ""
        url = request.original_url unless request.nil?

        socialIcon = 'http://static.themusictank.com/assets/images/social-share.png'

        title = parse_title meta
        description = parse_description meta

        # General
        tags << '<title>' + title + '</title>'

        tags << '<meta charset="utf-8">'
        tags << '<meta http-equiv="X-UA-Compatible" content="IE=edge">'
        tags << '<meta name="referrer" value="origin">'
        tags << '<meta name="viewport" content="width=device-width, initial-scale=1">'
        tags << '<meta name="author" content="Francois Faubert, active members of the community and contributors on Github">'
        tags << ''

        # Social
        tags << '<link href="https://plus.google.com/117543200043480372792" rel="publisher">'
        tags << ''


        # Theming
        tags << '<link rel="apple-touch-icon" href="'+socialIcon+'">'
        tags << '<link rel="icon" href="'+socialIcon+'">'
        tags << '<meta name="theme-color" content="#999999">'
        tags << '<link rel="manifest" href="manifest.json">'
        tags << ''

        # Open graph
        tags << '<meta property="og:url" content="' + domain + '">'
        tags << '<meta property="og:image" content="' + socialIcon + '">'
        tags << '<meta property="og:site_name" content="The Music Tank">'
        tags << '<meta property="og:type" content="website">'
        tags << '<meta property="og:locale" content="en_CA">'
        tags << '<meta property="og:title" content="'+title+'">'
        tags << '<meta property="og:description" content="' + description + '" >'
        tags << ''

        # Twitter
        tags << '<meta name="twitter:card" content="summary_large_image">'
        tags << '<meta name="twitter:creator" content="@themusictank">'
        tags << '<meta name="twitter:card" content="summary">'
        tags << '<meta name="twitter:image:src" content="' + socialIcon + '">'
        tags << '<meta name="twitter:title" content="'+title+'">'
        tags << '<meta name="twitter:description" content="' + description + '">'

        unless meta.nil? or meta['oembed_obj'].nil?
            tags << '<meta name="twitter:player" content="' + domain + '/embed/' + meta['oembed_obj'].class.name.downcase + "s/" + meta['oembed_obj'].slug + '">'
            tags << '<meta name="twitter:player:width" content="1280">'
            tags << '<meta name="twitter:player:height" content="720">'
        end

        tags << ''


        # OEmbed
        unless meta.nil? or meta['oembed_obj'].nil?
            url =  domain + "/" + meta['oembed_obj'].class.name.downcase + "s/" + meta['oembed_obj'].slug

            tags << '<link rel="alternative" type="application/json+oembed" href="' + domain + '/services/oembed?url=' + url + '" title="'+ title +'">'
            tags << ''
        end

        # Legacy
        tags << '<!--[if lt IE 9]>'
        tags << '  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>'
        tags << '  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>'
        tags << '<![endif]-->'
        tags << ''


        # GA
        tags << "<script>"
        tags << " (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){"
        tags << " (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),"
        tags << " m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)"
        tags << " })(window,document,'script','//www.google-analytics.com/analytics.js','ga');"
        tags << " ga('create', 'UA-1624062-1', 'auto');"
        tags << " ga('send', 'pageview');"
        tags << "</script>"
        tags << ''


        tags.join("\n").html_safe
    end

    private

    def self.content_tag param1, param2
        ActionController::Base.helpers.content_tag param1, param2
    end

    def self.tag param1, param2
        ActionController::Base.helpers.tag param1, param2
    end

    def self.image_path src
        ActionController::Base.helpers.image_path src
    end


    def self.get_domain request = nil
        if request.nil?
            return "/"
        end

        domain = request.port.blank? ? request.host : "#{request.host}:#{request.port}"
        "#{request.protocol}#{domain}"
    end

    def self.parse_title meta
        if meta.nil? || meta['title'].nil?
            "The Music Tank"
        else
            meta['title'].join(" &middot; ") + " &middot; The Music Tank"
        end
    end

    def self.parse_description meta
         unless meta.nil? || meta['description'].nil?
             meta['description']
         else
            "The Music Tank is a place where you can rate and discover music."
         end
    end

end
