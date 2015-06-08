module ApplicationHelper
    include ActionView::Helpers::TagHelper

    def self.to_meta_tags meta, request
        tags = Array.new
        tagHelper = ActionView::Helpers::TagHelper.new

        if meta.nil? || meta['title'].nil?
            tags << tagHelper.content_tag(:title, "The Music Tank")
            tags << tagHelper.tag(:meta, name: "og:title", description: "The Music Tank")
            tags << tagHelper.tag(:meta, name: "twitter:title", itemprop: "title name", description: "The Music Tank")
        else
            tags << "<title>" + h(meta.title.join(" - ")) + "- The Music Tank</title>"
            tags << '<meta name="og:title" description="' + h(meta.title.join(" - ")) + '- The Music Tank" >'
            tags << '<meta name="twitter:title" itemprop="title name" description="' + h(meta.title.join(" - ")) + '- The Music Tank" />'
        end

        tags << '<meta name="viewport" content="width=device-width, initial-scale=1">'
        tags << '<link href="https://plus.google.com/117543200043480372792" rel="publisher">'
        tags << '<noscript><meta http-equiv="refresh" content="0; URL=/pages/requirements/"></noscript>'
        tags << '<meta name="viewport" content="width=device-width, initial-scale=1">'
        tags << '<meta name="referrer" value="origin">'
        tags << '<meta name="og:url" description="' + request.domain + '">'
        tags << '<meta name="og:image" description="' + request.domain + 'img/social-share.png">'
        tags << '<meta name="og:site_name" description="The Music Tank">'
        tags << '<meta name="og:type" description="website">'
        tags << '<meta name="og:locale" description="en_CA">'
        tags << '<meta name="twitter:card" description="summary">'
        tags << '<meta name="twitter:image" description="' + request.domain + 'img/social-share.png">'
        tags << '<link rel="shortcut icon" href="' + request.domain + 'img/social-share.png">'

        unless meta.nil?
            unless meta['description'].nil?
                tags << "<title>" + h(meta.description) + "The Music Tank</title>"
                tags << '<meta name="og:title" description="' + h(meta.description) + '" >'
                tags << '<meta name="twitter:title" itemprop="description" description="' + h(meta.description) + '" />'
            end

            unless meta['oembed_obj'].nil?
                url = request.domain + meta['oembed_obj'].type + "/view/" + meta['oembed_obj'].slug
                tags << '<link rel="alternate" type="application/json+oembed" href="' + url + '" title="oEmbed Profile" />'
            end
        end

        tags.join("\n")
    end

end
