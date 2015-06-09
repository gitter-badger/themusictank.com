module ApplicationHelper

    def self.to_meta_tags meta, request
        tags = Array.new

        if meta.nil? || meta['title'].nil?
            tags << content_tag(:title, "The Music Tank")
            tags << tag(:meta, name: "og:title", description: "The Music Tank")
            tags << tag(:meta, name: "twitter:title", itemprop: "title name", description: "The Music Tank")
        else
            tags << "<title>" + meta['title'].join(" - ") + "- The Music Tank</title>"
            tags << '<meta name="og:title" description="' + meta['title'].join(" - ") + '- The Music Tank" >'
            tags << '<meta name="twitter:title" itemprop="title name" description="' + meta['title'].join(" - ") + '- The Music Tank" />'
        end

        tags << '<meta name="viewport" content="width=device-width, initial-scale=1">'
        tags << '<link href="https://plus.google.com/117543200043480372792" rel="publisher">'
        tags << '<noscript><meta http-equiv="refresh" content="0; URL=/pages/requirements/"></noscript>'
        tags << '<meta name="viewport" content="width=device-width, initial-scale=1">'
        tags << '<meta name="referrer" value="origin">'
        tags << '<meta name="og:url" description="' + request.domain.to_s + '">'
        tags << '<meta name="og:image" description="' + request.domain.to_s + 'img/social-share.png">'
        tags << '<meta name="og:site_name" description="The Music Tank">'
        tags << '<meta name="og:type" description="website">'
        tags << '<meta name="og:locale" description="en_CA">'
        tags << '<meta name="twitter:card" description="summary">'
        tags << '<meta name="twitter:image" description="' + request.domain .to_s+ 'img/social-share.png">'
        tags << '<link rel="shortcut icon" href="' + request.domain.to_s + 'img/social-share.png">'

        unless meta.nil?
            unless meta['description'].nil?
                tags << "<title>" + meta['description'] + "The Music Tank</title>"
                tags << '<meta name="og:title" description="' + meta['description'] + '" >'
                tags << '<meta name="twitter:title" itemprop="description" description="' + meta['description'] + '" />'
            end

            unless meta['oembed_obj'].nil?
                #url = request.domain + meta['oembed_obj'].link_back
                url = meta['oembed_obj'].link_back
                tags << '<link rel="alternate" type="application/json+oembed" href="' + url + '" title="oEmbed Profile" />'
            end
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

end
