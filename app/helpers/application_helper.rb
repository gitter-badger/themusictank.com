module ApplicationHelper

    def self.to_meta_tags meta, request
        tags = Array.new
        domain = get_domain request unless request.nil?
        socialIcon = 'http://static.themusictank.com/assets/images/social-share.png'

        title = parse_title meta
        description = parse_description meta

        # General
        tags << '<title>' + title + '</title>'
        tags << '<meta name="viewport" content="width=device-width, initial-scale=1">'
        tags << '<link href="https://plus.google.com/117543200043480372792" rel="publisher">'
        tags << '<link rel="shortcut icon" href="' + socialIcon + '">'
        tags << '<noscript><meta http-equiv="refresh" content="0; URL=/pages/requirements/"></noscript>'
        tags << '<meta name="viewport" content="width=device-width, initial-scale=1">'
        tags << '<meta name="referrer" value="origin">'
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
        tags << '<meta name="twitter:description" content="' + description + '" />'
        tags << ''

        # OEmbed
        unless meta.nil? or meta['oembed_obj'].nil?
            url =  domain + "/" + meta['oembed_obj'].class.name.downcase + "s/" + meta['oembed_obj'].slug

            tags << '<link rel="alternative" type="application/json+oembed" href="/oembed?url=' + url + '" title="oEmbed Profile" />'
            tags << ''
        end

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
            meta['title'].join(" - ") + " - The Music Tank"
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
